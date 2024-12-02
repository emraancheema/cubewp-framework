<?php
/**
 * CubeWp query is to render post queries with all type of custom fields
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Query
 */
class CubeWp_Query{
    
    public static $meta_key = null;
    public static $terms = null;
    public static $q_args = null;
    public static $meta_query = array();
    
    public function __construct(array $args) {
        self::$q_args = $args;
    }
    
    /**
     * Method cubewp_post_query
     *
     * @return object
     * @since  1.0.0
     */
    public function cubewp_post_query(){
        $query = self::cubewp_query_builder();

        $the_query = new WP_Query($query);
        
        if($the_query->have_posts()){
            $the_query->posts = apply_filters('cubewp_the_posts',$the_query->posts,$the_query->query_vars);
            $the_query = apply_filters( 'cubewp_query_vars' , $the_query );
        }
        return $the_query;
    }
        
    /**
     * Method cubewp_query_builder
     *
     * @return array
     * @since  1.0.0
     */
    public static function cubewp_query_builder(){
        $args = self::$q_args;
        $query      = array();
        $query['post_type']      = isset($args['post_type']) ? $args['post_type'] : '';
        $query['posts_per_page'] = isset($args['posts_per_page']) ? $args['posts_per_page'] : 10;
        $query['paged']          = isset($args['page_num']) ? $args['page_num'] : 1;
        $query['post_status']    = isset($args['post_status']) ? $args['post_status'] : 'publish';
        $google_fields = false;
        $google_meta = false;

        foreach($args as $meta_key => $value){
            $field_type     = '';
            self::$meta_key = $meta_key;
            $field_type     = self::q_field_type();
            
            do_action( 'cubewp_meta_query' , $meta_key, $field_type );

            if($field_type == 'taxonomy'){
                $query['tax_query']['relation'] = 'AND';
                $query['tax_query'][] = self::q_type_taxonomy();
            }else if($field_type == 'google_address'){

                $google_fields = true;
                $google_meta = $meta_key;

            }else if($field_type == 'number'){
                self::q_type_number();
            }else if($field_type == 'checkbox' || $field_type == 'dropdown'){
                self::q_type_multi_options();
            }else if($field_type == 'date_picker' || $field_type == 'date_time_picker' || $field_type == 'time_picker'){
                self::q_type_date();
            }else if( $field_type != '' ){
                self::q_type_others();
            }
        }

        $args = self::$q_args;

        // Sorting
        if(isset($args['orderby']) && !empty($args['orderby'])){
            if (substr($args['orderby'], -strlen('-DESC')) === '-DESC') {
                $custom_sort_field = substr($args['orderby'], 0, -strlen('-DESC'));
                $query['order'] = 'DESC';
                $query['orderby'] = 'meta_value_num';
                $query['meta_key'] = $custom_sort_field;
            }elseif (substr($args['orderby'], -strlen('-ASC')) === '-ASC') {
                $custom_sort_field = substr($args['orderby'], 0, -strlen('-ASC'));
                $query['order'] = 'ASC';
                $query['orderby'] = 'meta_value_num';
                $query['meta_key'] = $custom_sort_field;
            }else{
                if(isset($args['orderby']) && ($args['orderby'] == 'DESC' || $args['orderby'] == 'ASC')){
                    $query['order'] = $args['orderby'];
                }else{
                    $query['orderby'] = $args['orderby'];
                }
            }
        }

        if(isset($args['order']) && !empty($args['order'])){
            $query['order'] = $args['order'];
        }

        // Extra Meta Query
        $extra_meta_query = isset($args['meta_query']) && !empty($args['meta_query']) ? $args['meta_query'] : array();
       
        if(!empty(self::$meta_query) && count(self::$meta_query) > 0){
            $query['meta_query'] = array_merge(self::$meta_query,$extra_meta_query);
        }elseif(!empty($extra_meta_query) && count($extra_meta_query) > 0){
            $query['meta_query'] = $extra_meta_query;
        }

        // Default Query arguments
        if(isset($args['s']) && !empty($args['s'])){
            $query['s'] = $args['s'];
        }
        if(isset($args['post__not_in']) && !empty($args['post__not_in'])){
            $query['post__not_in'] = $args['post__not_in'];
        }
        if(isset($args['fields']) && !empty($args['fields'])){
            $query['fields'] = $args['fields'];
        }
        if(isset($args['author']) && !empty($args['author'])){
            $query['author'] = $args['author'];
        }

        // Google Location Proximity search
        if($google_fields){
            $query['post__in'] = self::q_type_google($google_meta, $query);
            if(empty($query['post__in'])){
                return;
            }else{
                $query['orderby'] = 'post__in';
            }
        }

        // Post__in 
        if(isset($args['post__in']) && !empty($args['post__in'])){
            $query['post__in'] = $args['post__in'];
        }
        return $query;
    }
    
    /**
     * Method q_field_type
     *
     * @return string
     * @since  1.0.0
     */
    private static function q_field_type(){
        $field_type = '';
        $meta_key = self::$meta_key;
        $singleFieldOptions = get_field_options($meta_key);
        if(isset($singleFieldOptions) && !empty($singleFieldOptions)){
            $field_type = $singleFieldOptions['type'];
        }
        if( $field_type == '' ){
            $taxonomy = cwp_get_taxonomy($meta_key);
            if(isset($taxonomy) && !empty($taxonomy)){
                $field_type = 'taxonomy';
            }
        }
        return $field_type;
    }
    
    /**
     * Method q_type_taxonomy
     *
     * @return array
     * @since  1.0.0
     */
    private static function q_type_taxonomy(){
        $args = self::$q_args;
        $tax_query  = array();
        $meta_key = self::$meta_key;
        $_mKey = $args[$meta_key];
        if(isset($_mKey) && !empty($_mKey)){
            self::$terms = $_mKey;
            $values = explode(',', $_mKey);
            $tax_query = array(
                'taxonomy' => $meta_key,
                'field'    => 'id',
                'terms'    => $values
            );
        }
        return $tax_query;
    }
    
    private static function q_type_google($google_meta, $query){
        global $cwpOptions;
        $args = self::$q_args;
        $post_ids  = array();
        $meta_key = $google_meta;
        $_mKey = $args[$meta_key];
        if(isset($_mKey) && !empty($_mKey)){
            $lat = isset($args[$meta_key.'_lat']) ? $args[$meta_key.'_lat'] : '';
            $lng = isset($args[$meta_key.'_lng']) ? $args[$meta_key.'_lng'] : '';
            $radius = $cwpOptions['google_address_radius'];
            $range = $cwpOptions['google_address_default_radius'];
            $radius_unit = $cwpOptions['google_address_radius_unit'];
            if ($radius == '1' && isset($args[$meta_key.'_range'])) {
                $range = $args[$meta_key.'_range'];
            }
            $post_ids = cwp_get_proximity_sql( $meta_key.'_lat', $meta_key.'_lng', $lat, $lng, $radius_unit, $range, $query );
            //$post_ids = array_keys( (array) $post_ids );
        }
        return $post_ids;
    }
    
    /**
     * Method q_type_number
     *
     * @return array
     * @since  1.0.0
     */
    private static function q_type_number(){
        $args = self::$q_args;
        $meta_key = self::$meta_key;
        if(isset($args['min-'.$meta_key]) || isset($args['max-'.$meta_key])){
            if(isset($args['min-'.$meta_key]) && !empty($args['min-'.$meta_key])){
                self::$meta_query[] = array(
                    'key'        => $meta_key,
                    'value'      => $args['min-'.$meta_key],
                    'type'       => 'NUMERIC',
                    'compare'    => '>=',
                );
            }
            if(isset($args['max-'.$meta_key]) && !empty($args['max-'.$meta_key])){
                self::$meta_query[] = array(
                    'key'        => $meta_key,
                    'value'      => $args['max-'.$meta_key],
                    'type'       => 'NUMERIC',
                    'compare'    => '<=',
                );
            }
        }
    }
    
    /**
     * Method q_type_multi_options
     *
     * @return array
     * @since  1.0.0
     */
    private static function q_type_multi_options(){
        $args = self::$q_args;
        $meta_key = self::$meta_key;
        $_mKey = $args[$meta_key];
        if ( isset( $_mKey ) && ! empty( $_mKey ) ) {
            $values = explode( ',', $_mKey );
            $meta_query = array();
            $meta_query['relation'] = 'OR';
            foreach ( $values as $_val ) {
                $meta_query[] = array(
                'key'     => $meta_key,
                'value'   => $_val,
                'compare' => 'LIKE',
                );
            }
            
            self::$meta_query[] = $meta_query;
        }
    }
        
    /**
     * Method q_type_date
     *
     * @return array
     * @since  1.0.0
     */
    private static function q_type_date(){
        $args = self::$q_args;
        $meta_key = self::$meta_key;
        if(isset($args[$meta_key]) || !empty($args[$meta_key])){
            $meta_query = array();
            $date_range = explode('-',$args[$meta_key]);
            if(is_array($date_range)){
                if(isset($date_range[0]) && !empty($date_range[0])){
                    $meta_query[] = array(
                        'key'        => $meta_key,
                        'value'      => strtotime($date_range[0]),
                        'type'       => 'NUMBER',
                        'compare'    => '>=',
                    );
                }
                if(isset($date_range[1]) && !empty($date_range[1])){
                    $meta_query[] = array(
                        'key'        => $meta_key,
                        'value'      => strtotime($date_range[1]),
                        'type'       => 'NUMBER',
                        'compare'    => '<=',
                    );
                }
            }
            self::$meta_query[] = $meta_query;
        }
    }
        
    /**
     * Method q_type_others
     *
     * @return array
     * @since  1.0.0
     */
    private static function q_type_others(){
        $args = self::$q_args;
        $meta_query_filter  = array();
        $meta_key = self::$meta_key;
        $_mKey = $args[$meta_key];
        if(isset($_mKey) && !empty($_mKey)){
            if(is_array($_mKey)){
                $meta_query = array();
                $meta_query['relation'] = 'OR';
                foreach($_mKey as $_val){
                    $meta_query[] = array(
                        'key'  => $meta_key,
                        'value'	    => $_val,
                        'compare'   => 'LIKE',
                    );
                }
            }else{
                if(isset($_mKey) && !empty($_mKey)){
                    $meta_query = array(
                        'key'  => $meta_key,
                        'value'     => $_mKey,
                        'compare'	=> '=',
                    );
                }
            }
            self::$meta_query[] = $meta_query;
        }
    }


}