<?php
/**
 * CubeWp single cpt contains all function to show single post page content
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Single_Cpt
 */
class CubeWp_Single_Cpt {

	use CubeWp_Single_Page_Trait;

	public static $post_id;
	public static $post_type;
	public static $author_id;
	public static $groups;
	public static $Load = null;

    
    /**
     * Method instance
     *
     * @return instance
     * @since  1.0.0
     */
    public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}
		return self::$Load;
	}
	public function __construct() {
		self::set_post_args();
        add_shortcode( 'cubewp_post_save', array($this, 'get_post_save_button'));
        add_shortcode( 'cubewp_post_share', array($this, 'get_post_share_button'));
	}
	
	/**
	 * Method set_post_args
	 *
	 * @return void
     * @since  1.0.0
	 */
	public static function set_post_args() {
		self::$post_id   = get_the_ID();
		self::$post_type = get_post_type( self::$post_id );
        self::$author_id = get_post_field( 'post_author', self::$post_id );
		self::$groups = cwp_get_groups_by_post_type( self::$post_type );
	}
        
    /**
     * Method cubewp_post_metas
     *
     * @param int $postid
     *
     * @return array
     * @since  1.0.0
     */
    public static function cubewp_post_metas($postid='', $rest_data = false) {
        if (!empty($postid)) self::$post_id = $postid;
		$args = array();
        $groups = !isset(self::$groups) || empty(self::$groups) ? cwp_get_groups_by_post_id(self::$post_id) : self::$groups;
        if (is_array($groups)) $groups = array_reverse($groups);
        foreach ($groups as $group) {
            $fields = cwp_get_fields_by_group_id( $group );
            if (!empty($fields) && is_array($fields)) {
                foreach ($fields as $field) {
                    $field = get_field_options($field);
                    $field_rest_permission = $rest_data == true && isset( $field["rest_api"] ) ? $field["rest_api"] : 0;
                    if($field_rest_permission || $rest_data == false){
                        $field_type = isset( $field["type"] ) ? $field["type"] : "";
                        $meta_key   = isset( $field["name"] ) ? $field["name"] : "";
                        if($field_type == 'taxonomy'){
                            if(isset($field) && count($field)>0 && !empty($field['filter_taxonomy'])){
                                $field_type = 'terms';
                            }
                        }
                        if (empty($field_type) || empty($meta_key)) continue;
                        $label = isset( $field["label"] ) ? $field["label"] : "";
                        $value = self::get_single_meta_value($meta_key,$field_type);
                        $args[$meta_key] = array(
                            'type'                  =>    $field_type,
                            'meta_key'              =>    $meta_key,
                            'meta_value'            =>    $value,
                            'label'                 =>    $label,
                        );
                    }
                }
            }
        }
        return $args;
    }
    	
	/**
	 * Method get_post_featured_image
	 *
	 * @return string html/null
     * @since  1.0.0
	 */
	public static function get_post_featured_image() {
		if ( has_post_thumbnail( self::$post_id ) ) {
			return '<div class="cwp-single-fet-img"><img src="' . esc_url( get_the_post_thumbnail_url( self::$post_id, array( 1250, 500 ) ) ) . '" alt="' . get_the_title( self::$post_id ) . '"></div>';
		} else {
			return null;
		}
	}
	
	/**
	 * Method get_post_breadcrumb_tags
	 *
	 * @param string $call 
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public static function get_post_breadcrumb_tags( $call ) {
		$output     = null;
		$class      = null;
		$post_terms = self::get_post_terms();
		if ( "breadcrumb" === $call ) {
			$class = 'cwp-cpt-single-post-terms';
		} elseif ( "tags" === $call ) {
			$class = 'cwp-single-category-widget-inner';
		}
		if ( isset( $post_terms ) && ! empty( $post_terms ) && is_array( $post_terms ) ) {
			$output .= '<ul class="' . $class . '">';
			foreach ( $post_terms as $terms ) {
				foreach ( $terms as $term ) {
					$output .= '<li>
                        <a href="' . get_term_link( $term ) . '">
                            <p>' . $term->name . '</p>
                        </a>
                    </li>';
				}
			}
			$output .= '</ul>';
		}

		return $output;
	}
	
	/**
	 * Method get_post_terms
	 *
	 * @return array
     * @since  1.0.0
	 */
	public static function get_post_terms() {
		$post_terms = array();
		$taxonomies = get_object_taxonomies( self::$post_type );
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms[] = get_the_terms( self::$post_id, $taxonomy );
			}
		}

		return isset( $post_terms ) && ! empty( $post_terms ) ? array_filter( $post_terms ) : array();
	}
    
    /**
     * Method get_post_save_button
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_post_save_button() {
        $isSaved = '';
        if(class_exists('CubeWp_Saved')){
            $SavedText = CubeWp_Saved::is_cubewp_post_saved(self::$post_id,false,false);
            $SavedClass = CubeWp_Saved::is_cubewp_post_saved(self::$post_id,false,true);
        }else{
            $SavedText = esc_html__("Save", "cubewp-framework" );
            $SavedClass = 'cwp-save-post';
        }
        echo '<div class="cwp-single-save-btns cwp-single-widget">
         <span class="cwp-main '.esc_attr($SavedClass).'" data-pid="'.esc_attr(self::$post_id).'">
         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
               <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
         </svg>
         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
               <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
         </svg>
            <span class="cwp-saved-text">'.esc_html($SavedText).'</span>
        </span>
        </div>';
    }
	
	/**
	 * Method field_author
	 *
	 * @return string html
     * @since  1.0.0
	 */
    public static function field_author() {
        $user_id = self::$author_id;
        return get_user_details($user_id);
    }
	
	/**
	 * Method get_post_sidebar_tags
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public static function get_post_sidebar_tags() {
        $post_tags = self::get_post_breadcrumb_tags('tags');
        
        if(!empty($post_tags)){
        return '<div class="cwp-single-category-widget cwp-single-widget">
            <h5 class="cwp-widget-title">' . esc_html__( "Tags", "cubewp-framework" ) . '</h5>
            ' . self::get_post_breadcrumb_tags('tags') . '
        </div>';
        }else{
            return '';
        }
    }
    
    /**
     * Method get_post_sidebar
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_post_sidebar() {
        $output='<div class="cwp-single-left-side">
    	    ' . self::field_author() . '
            </div>';
        if(!empty(self::get_post_sidebar_tags())){
            $output .='<div class="cwp-single-left-side">
                ' . self::get_post_sidebar_tags() . '
            </div>';
        }
	    return $output;
    }
	
	/**
	 * Method get_post_group_fields_callback
	 *
	 * @param array $fields
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public static function get_post_group_fields_callback($fields) {
        $output = null;
		foreach ($fields as $field) {
			$options = get_field_options($field);
			$field_type = isset($options['type']) ? $options['type'] : '';
            $meta_key = isset($options['name']) ? $options['name'] : '';
			if (empty($field_type) || empty($meta_key)) continue;
            if($field_type == 'taxonomy'){
                if(isset($options) && count($options)>0 && !empty($options['filter_taxonomy'])){
                    $field_type = 'terms';
                }
            }
            $value = self::get_single_meta_value($meta_key,$field_type);
            $options['value'] = $value;
            $options = apply_filters( 'cubewp/custom/cube/field/options', $options );
			if(function_exists('cube_' . $field_type)){
                $output .= call_user_func('cube_' . $field_type, $options);
            }else if (method_exists(__CLASS__, 'field_' . $field_type)) {
                $output .= call_user_func( array( __CLASS__, 'field_' . $field_type ), $options);
			}else {
				$output .= '<p style="color: #ff0000">' . sprintf(esc_html__("Invalid Field Type: %s", "cubewp-framework"), $field_type) . '</p>';
			}
		}
        return $output;
    }
        
    /**
     * Method get_single_meta_value
     *
     * @param string $meta_key
     * @param string $field_type
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_single_meta_value($meta_key='',$field_type=''){
        
        if($meta_key == 'the_title' || $meta_key == 'the_content' || $meta_key == 'the_excerpt' || $meta_key == 'featured_image'){
            return '';
        }elseif($field_type == 'taxonomy'){
            $value = get_the_terms( self::$post_id, $meta_key );
        }elseif($field_type == 'dropdown' || $field_type == 'radio' || $field_type == 'checkbox'){
            $value = get_post_meta( self::$post_id, $meta_key, true );
            $value = render_multi_value($meta_key, $value);
        }elseif($field_type == 'repeating_field'){
            $values = get_post_meta( self::$post_id, $meta_key, true );
            if (is_array($values)) {
                $value  = self::get_repeating_Fields_value($values);
            }
        }else{
            $value = get_post_meta( self::$post_id, $meta_key, true );
            if ("google_address" === $field_type) {
                $lat = get_post_meta( self::$post_id, $meta_key. '_lat', true );
                $lng = get_post_meta( self::$post_id, $meta_key. '_lng', true );
                $value = array(
                    'address' => $value,
                    'lat' => $lat,
                    'lng' => $lng
                );
            }
        }
        if(empty($value)){
            return '';
        }
        return apply_filters( 'cubewp/single/page/field/value', $value, self::$post_id, $meta_key, $field_type );
    }
    	
	/**
	 * Method get_repeating_Fields_value
	 *
	 * @param array $values
	 *
	 * @return array
     * @since  1.0.0
	 */
	private static function get_repeating_Fields_value($values) {
        $value = array();
        if(isset($values) && !empty($values)){
            for($i = 0; $i < count($values); $i++){
                
                foreach ($values[$i] as $k=> $subValue) {
                    $options = get_field_options($k);
                    if(count($options)>0){
                        $field_type = $options['type'];
                        $field_label = $options['label'];
                        $field_class = $options['class'];
                        if ("google_address" === $field_type) {
                            $lat = $values[$i][$k. '_lat'];
                            $lng = $values[$i][$k. '_lng'];
                            
                            $subValue = array(
                                'address' => $subValue,
                                'lat' => $lat,
                                'lng' => $lng
                            );
                        }elseif($field_type == 'dropdown' || $field_type == 'radio' || $field_type == 'checkbox'){
                            $subValue = render_multi_value($k, $subValue);
                        }
                        $value[$i][$k] = array(
                            'type'                  =>    $field_type,
                            'container_class'       =>    "",
                            'class'                 =>    $field_class,
                            'label'                 =>    $field_label,
                            'value'                 =>    $subValue,
                        );
                    }
                }
            }
            return $value;
        }
    }
	
		/**
	 * Method get_post_groups_callback
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public static function get_post_groups_callback() {
		$output = null;
        if (is_array(self::$groups)) self::$groups = array_reverse(self::$groups);
        foreach (self::$groups as $group) {
	    	$fields = cwp_get_fields_by_group_id( $group );
            if (!empty($fields) && is_array($fields)) {
                $fields_ui = self::get_post_group_fields_callback($fields);
                if(!empty($fields_ui)){
                    $output .= '<div class="cwp-single-group">';
                    $output .= $fields_ui;
		            $output .= '</div>';
                }
            }
        }
        return $output;
    }

    
    /**
     * Method get_single_sidebar_area
     *
     * @return string html
     * @since  1.0.0
     */
    public static function get_single_sidebar_area() {
        echo self::get_post_sidebar();

    }    
    /**
     * Method get_single_content_area
     *
     * @since  1.0.0
     */
    public static function get_single_content_area() {
        echo self::get_post_content();
    }
    
    /**
     * Method get_sections_data_of
     *
     * @param string $of 
     *
     * @return array
     * @since  1.0.0
     */
    public static function get_sections_data_of($of='') {
        $section = array();
        if (is_array(self::$groups)){
            foreach (self::$groups as $group) {
                $section[$group]['title']  = get_the_title($group);
                $section[$group]['class']  = 'class-'.$group;
                $section[$group]['id']     = 'id-'.$group;
            }
        }
        return $section;
    }
	
	/**
	 * Method get_post_content
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public static function get_post_content() {
        return '<div class="cwp-single-right-side">
            <div class="cwp-single-groups">
                ' . self::get_post_groups_callback() . '
            </div>
        </div>';
    }

}