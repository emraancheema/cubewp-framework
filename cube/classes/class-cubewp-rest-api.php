<?php 
/**
 * CubeWp rest api for custom field data.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Rest_API
 */
class CubeWp_Rest_API {


	public static function init() {
		register_rest_field( self::get_types(), 'cubewp_post_meta', [
			'get_callback'    => [ __CLASS__, 'get_post_meta' ],
			'update_callback' => [ __CLASS__, 'update_post_meta' ],
		] );
		register_rest_field( self::get_types(), 'taxonomies', [
			'get_callback'    => [ __CLASS__, 'get_taxonomies' ],
			'update_callback' => '',
		] );
		register_rest_field( 'user', 'cubewp_user_meta', [
			'get_callback'    => [ __CLASS__, 'get_user_meta' ],
			'update_callback' => [ __CLASS__, 'update_user_meta' ],
		] );
		register_rest_field( self::get_types('taxonomy'), 'cubewp_term_meta', [
			'get_callback'    => [ __CLASS__, 'get_term_meta' ],
			'update_callback' => [ __CLASS__, 'update_term_meta' ],
		] );
	}
	
	/**
	 * Get post meta for the rest API.
	 *
	 * @param array $object Post object.
	 *
	 * @return array
	 */
	public static function get_post_meta( $object ) {
		$post_id   = $object['id'];
		$fields = CubeWp_Single_Cpt::cubewp_post_metas($post_id,true);
		return $fields;
	}

	/**
	 * Get all taxonomies and terms for the rest API.
	 *
	 * @param array $object Post object.
	 *
	 * @return array
	 */
	public static function get_taxonomies( $object ) {
		$post_id   = $object['id'];
		$post_terms = array();
		$taxonomies = get_object_taxonomies( get_post_type($post_id) );
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$all_terms = get_the_terms( $post_id, $taxonomy );
				if(!is_wp_error( $all_terms ) && !empty( $all_terms )){
					foreach($all_terms as $all_term){
						$post_terms[] = $all_term->name;
					}
				}
			}
		}

		return isset( $post_terms ) && ! empty( $post_terms ) ? array_filter( $post_terms ) : array();
	}

	/**
	 * Update post meta for the rest API.
	 *
	 * @param string|array $data   Post meta values in either JSON or array format.
	 * @param object       $object Post object.
	 */
	public static function update_post_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$options = get_field_options($field_id);
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if($options['type'] == 'google_address'){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_post_meta( $object->ID, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_post_meta( $object->ID, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_post_meta( $object->ID, $field_id. '_lng', $meta_val['lng'] );
				}
			}elseif($options['type'] == 'repeating_field'){
				$repeater_vals =  get_post_meta( $object->ID, $field_id, true );
				$sub_meta_val = [];
				for($i = 0; $i < count($repeater_vals); $i++){
					foreach ( $repeater_vals[$i] as $k=> $sub_field ) {
						$org_val = $sub_field;
						$sub_data = $meta_val[$i][$k];
						$lat_key = str_replace("_lat","",$k);
						$lng_key = str_replace("_lng","",$k);
						$sub_lat_data = $meta_val[$i][$lat_key];
						$sub_lng_data = $meta_val[$i][$lng_key];
						$subOptions = get_field_options($k);
						if($subOptions['type'] == 'google_address'){
								if(isset($sub_data['value']['address']) && !empty($sub_data['value']['address'])){
									$sub_meta_val[$i][$k] = $sub_data['value']['address'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
						}else{
							if(isset($sub_lat_data['value']['lat']) || isset($sub_lng_data['value']['lng'])){
								if(isset($sub_lat_data['value']['lat']) && !empty($sub_lat_data['value']['lat'])){
									$sub_meta_val[$i][$lat_key.'_lat'] = $sub_lat_data['value']['lat'];
								}
								if(isset($sub_lng_data['value']['lng']) && !empty($sub_lng_data['value']['lng'])){
									$sub_meta_val[$i][$lng_key.'_lng'] = $sub_lng_data['value']['lng'];
								}
							}else{
								if(isset($sub_data['value']) && !empty($sub_data['value'])){
									$sub_meta_val[$i][$k] = $sub_data['value'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
							}
						}
					}
				}
				update_post_meta( $object->ID, $field_id, $sub_meta_val );
			}else{
				update_post_meta( $object->ID, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get term meta for the rest API.
	 *
	 * @param array $object Term object.
	 *
	 * @return array
	 */
	public static function get_term_meta( $object ) {
		$term_id = $object['id'];
		$term    = get_term( $term_id );
		if ( is_wp_error( $term ) || ! $term ) {
			return [];
		}

		return CubeWp_Taxonomy_Metabox::cubewp_taxonomy_metas($term->taxonomy,$term_id);
	}

	/**
	 * Update term meta for the rest API.
	 *
	 * @param string|array $data   Term meta values in either JSON or array format.
	 * @param object       $object Term object.
	 */
	public static function update_term_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if(isset($meta_val['lng']) || isset($meta_val['lat']) || isset($meta_val['address'])){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_term_meta( $object->term_id, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_term_meta( $object->term_id, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_term_meta( $object->term_id, $field_id. '_lng', $meta_val['lng'] );
				}
			}else{
				update_term_meta( $object->term_id, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get user meta for the rest API.
	 *
	 * @param array $object User object.
	 *
	 * @return array
	 */
	public static function get_user_meta( $object ) {
		$user_id   = $object['id'];
		if ( ! $user_id ) {
			return [];
		}

		return CubeWp_Custom_Fields::cubewp_user_metas($user_id,true);
	}

	/**
	 * Update user meta for the rest API.
	 *
	 * @param string|array $data   User meta values in either JSON or array format.
	 * @param object       $object User object.
	 */
	public static function update_user_meta( $data, $object ) {
		$data = is_array( $data ) ? $data : array();
		foreach ( $data as $field_id => $value ) {
			$options = get_user_field_options($field_id);
			$meta_val = isset($value['meta_value']) ? $value['meta_value'] : '';
			if($options['type'] == 'google_address'){
				if(isset($meta_val['address']) && !empty($meta_val['address'])){
					update_user_meta( $object->ID, $field_id, $meta_val['address'] );
				}elseif(isset($meta_val['lat']) && !empty($meta_val['lat'])){
					update_user_meta( $object->ID, $field_id. '_lat', $meta_val['lat'] );
				}elseif(isset($meta_val['lng']) && !empty($meta_val['lng'])){
					update_user_meta( $object->ID, $field_id. '_lng', $meta_val['lng'] );
				}
			}elseif($options['type'] == 'repeating_field'){
				$repeater_vals =  get_post_meta( $object->ID, $field_id, true );
				$sub_meta_val = [];
				for($i = 0; $i < count($repeater_vals); $i++){
					foreach ( $repeater_vals[$i] as $k=> $sub_field ) {
						$org_val = $sub_field;
						$sub_data = $meta_val[$i][$k];
						$lat_key = str_replace("_lat","",$k);
						$lng_key = str_replace("_lng","",$k);
						$sub_lat_data = $meta_val[$i][$lat_key];
						$sub_lng_data = $meta_val[$i][$lng_key];
						$subOptions = get_user_field_options($k);
						if($subOptions['type'] == 'google_address'){
								if(isset($sub_data['value']['address']) && !empty($sub_data['value']['address'])){
									$sub_meta_val[$i][$k] = $sub_data['value']['address'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
						}else{
							if(isset($sub_lat_data['value']['lat']) || isset($sub_lng_data['value']['lng'])){
								if(isset($sub_lat_data['value']['lat']) && !empty($sub_lat_data['value']['lat'])){
									$sub_meta_val[$i][$lat_key.'_lat'] = $sub_lat_data['value']['lat'];
								}
								if(isset($sub_lng_data['value']['lng']) && !empty($sub_lng_data['value']['lng'])){
									$sub_meta_val[$i][$lng_key.'_lng'] = $sub_lng_data['value']['lng'];
								}
							}else{
								if(isset($sub_data['value']) && !empty($sub_data['value'])){
									$sub_meta_val[$i][$k] = $sub_data['value'];
								}else{
									$sub_meta_val[$i][$k] = $org_val;
								}
							}
						}
					}
				}
				update_user_meta( $object->ID, $field_id, $sub_meta_val );
			}else{
				update_user_meta( $object->ID, $field_id, $meta_val );
			}
		}
	}

	/**
	 * Get supported types in Rest API.
	 *
	 * @param string $type 'post' or 'taxonomy'.
	 *
	 * @return array
	 */
	private static function get_types( $type = 'post' ) {
		$types = get_post_types( [], 'objects' );
		if ( 'taxonomy' === $type ) {
			$types = get_taxonomies( [], 'objects' );
		}
		foreach ( $types as $type => $object ) {
			if ( empty( $object->show_in_rest ) ) {
				unset( $types[ $type ] );
			}
		}

		return array_keys( $types );
	}

}
