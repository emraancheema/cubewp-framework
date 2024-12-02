<?php

/**
 * CubeWp Frontend is gateway to cubewp frontend.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CubeWp_frontend
 */
class CubeWp_Frontend {
    
    const FSAH = 'CubeWp_Search_Ajax_Hooks';
    public static $Load = null;
    public static $post_type = '';
    public static $taxonomy = '';
    public static $parent_field = '';
    public static $post_count = 0;
    
    public function __construct( ) {
        $this->include_fields();
        add_filter('cubewp/frontend/field/parametrs', array($this, 'frontend_field_parameters'), 10, 1);
        add_action('cubewp_loaded', array('CubeWp_Frontend_Alerts', 'init'), 10);
        add_action('wp', array('CubeWp_Frontend_Templates', 'init'), 10);
        add_action('cubewp_loaded', array('CubeWp_Saved', 'init'), 10);
        add_action('cubewp_loaded', array('CubeWp_Pagination', 'init'), 10);
        add_action('cubewp_loaded', array('CubeWp_Frontend_Search_Fields', 'init'), 10);
        add_action('cubewp_loaded', array('CubeWp_Frontend_Search_Filter', 'init'), 10);
        add_action('cubewp_loaded', array('CubeWp_Frontend_Recaptcha', 'init'), 10);
        add_filter('cubewp_frontend_search_data', array($this, 'cubewp_frontend_search_data'), 10, 2);
        
        new CubeWp_Ajax( '',
            self::FSAH,
            'cwp_search_filters_ajax_content'
        );
        new CubeWp_Ajax( 'wp_ajax_nopriv_',
            self::FSAH,
            'cwp_search_filters_ajax_content'
        );
        
    }
        
    /**
     * Method filters shows all search filters
     *
     * @return string html
     * @since  1.0.0
     */
    public static function filters() {
        return CubeWp_Frontend_Search_Filter::get_filters();
    }
        
    /**
     * Method Single initiate all instances of single cpt class
     *
     * @return void
     * @since  1.0.0
     */
    public static function Single() {
        return CubeWp_Single_Cpt::instance();
    }
        
    /**
     * Method post_metas shows all single post metas
     *
     * @param int $postID 
     *
     * @return array
     * @since  1.0.0
     */
    public static function post_metas(int $postID ) {
        return self::single()->cubewp_post_metas($postID);
    }
        
    /**
     * Method include_fields
     *
     * @return void
     * @since  1.0.0
     */
    public function include_fields(){
        $frontend_fields = array(
            'text', 'number','color', 'range', 'email', 'url', 'password', 'textarea', 'wysiwyg-editor', 'oembed', 'file', 'image', 'gallery', 'dropdown', 'checkbox', 'radio', 'switch', 'business-hours', 'google-address', 'date-picker', 'date-time-picker', 'time-picker', 'post', 'taxonomy', 'terms', 'user', 'repeater'
        );
        foreach($frontend_fields as $frontend_field){
            $field_path = CWP_PLUGIN_PATH . "cube/fields/frontend/cubewp-frontend-{$frontend_field}-field.php";
            if(file_exists($field_path)){
                include_once $field_path;
            }
        }
    }
        
    /**
     * Method frontend_field_parameters
     *
     * @param array $args frontend field arguments
     *
     * @return array 
     * @since  1.0.0
     */
    public function frontend_field_parameters( array $args ){

        $default = array(
            'type'                  =>    '',
            'id'                    =>    '',
            'class'                 =>    '',
            'options'               =>    '',
            'container_class'       =>    '',
            'container_attrs'       =>    '',
            'name'                  =>    '',
            'custom_name'           =>    '',
            'value'                 =>    '',
            'minimum_value'         =>   0,
            'maximum_value'         =>   100,
            'steps_count'           =>   1,
            'file_types'            =>   '',
            'placeholder'           =>    '',
            'upload_size'           =>   '',
            'max_upload_files'      =>   '',
            'label'                 =>    '',
            'description'           =>    '',
            'multiple'              =>    0,
            'select2_ui'            =>    0,
            'current_user_posts'    =>    0,
            'editor_media'          =>    0,
            'auto_complete'         =>    0,
            'appearance'            =>    '',
            'required'              =>    false,
            'validation_msg'        =>    '',
            'conditional'           =>    0,
            'conditional_field'     =>    '',
            'conditional_operator'  =>    '',
            'conditional_value'     =>    '',
            'char_limit'            =>    '',
            'extra_attrs'           =>    '',
            'field_size'            =>    '',
            'sub_fields'            =>    array(),
            'files_save'            =>    'ids',
            'filter_post_types'     =>    '',
            'files_save_separator'  =>    'array',
        );
        return wp_parse_args($args, $default);

    }    
    /**
     * Method cwp_frontend_conditional_attributes
     *
     * @param array $args frotnend field args
     *
     * @return array
     * @since  1.0.0
     */
    public static function cwp_frontend_conditional_attributes(array $args) {

        if(isset($args['conditional']) && 
           !empty($args['conditional']) && 
           !empty($args['conditional_field']))
        {
            $conditional_value = isset($args['conditional_value']) && !empty($args['conditional_value']) ? $args['conditional_value'] : '';
            $condi_val = $args['conditional_operator'] != '!empty' && 'empty' !=  $args['conditional_operator'] ? $conditional_value : '';
            if (
                has_shortcode(get_the_content(), 'cwpSearch') ||
                has_shortcode(get_the_content(), 'cwpFilters') ||
                is_search() ||
                is_archive()
            ) {
                return array();
            }
            if(empty($args['value'])){
                $attr['style'] = ' style="display:none"';
            }
            
            $attr['class'] = ' conditional-logic '.$args['conditional_field'].$conditional_value;
            $attr['data']  = ' data-field="'.$args['conditional_field'].'"';
            $attr['data'] .= ' data-operator="'.$args['conditional_operator'].'"';
            $attr['data'] .= ' data-value="'.$condi_val.'"';
            return $attr;
        }
        return '';
    }    
    /**
     * Method cwp_frontend_field_description
     *
     * @param array $args 
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_frontend_field_description(  array $args ) {

        if( !$args['description'] ) return;
        return '<p class="description">' . $args['description'] . '</p>';

    }
        
    /**
     * Method cwp_frontend_post_field_container
     *
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_frontend_post_field_container(  array $args ) {
        
        $args   =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        
        $conditional_attr    =  self::cwp_frontend_conditional_attributes($args);
        $conditionalClass    =  isset($conditional_attr['class']) ? $conditional_attr['class'] : '';
        $conditionalStyle    =  isset($conditional_attr['style']) ? $conditional_attr['style'] : '';
        $conditionalData     =  isset($conditional_attr['data']) ? $conditional_attr['data'] : '';
        
        
        $container_attr  = isset($args['id']) && !empty($args['id']) ? ' data-id="'. esc_attr($args['id']).'"' : '';
        $container_attr .= isset($args['name']) && !empty($args['name']) ? ' data-name="'. esc_attr($args['name']).'"' : '';
        $container_attr .= isset($args['type']) && !empty($args['type']) ? ' data-type="'. esc_attr($args['type']).'"' : '';
        $container_attr .=   $conditionalData.$conditionalStyle;
        if(isset($args['validation_msg']) && !empty($args['validation_msg'])){
            $container_attr .= ' data-validation_msg="'. $args['validation_msg'] .'"';
        }
        
        if(isset($args['container_attrs']) ){
            $container_attr .= $args['container_attrs'];
        }
        
        $required_class = '';
        if(isset($args['required']) && $args['required'] == true){
            $required_class = ' is-required';
        }
        
        $select2 =  self::cwp_frontend_field_select2($args['select2_ui']);
        $classes = "cwp-field-container cwp-field-{$args['type']} form-group {$args['container_class']} {$args['field_size']} {$required_class} {$conditionalClass} {$select2}";
        return '<div class="'.$classes.'" '. $container_attr .'>';

    }
        
    /**
     * Method cwp_frontend_search_field_container
     *
     * @param array $args search related fields args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_frontend_search_field_container(  array $args ) {
        
        $args   =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        
        $conditional_attr    =  self::cwp_frontend_conditional_attributes($args);
        $conditionalClass    =  isset($conditional_attr['class']) ? $conditional_attr['class'] : '';
        $conditionalStyle    =  isset($conditional_attr['style']) ? $conditional_attr['style'] : '';
        $conditionalData     =  isset($conditional_attr['data']) ? $conditional_attr['data'] : '';
 
        
        $container_attr  = isset($args['id']) && !empty($args['id']) ? ' data-id="'. esc_attr($args['id']).'"' : '';
        $container_attr .= isset($args['name']) && !empty($args['name']) ? ' data-name="'. esc_attr($args['name']).'"' : '';
        $container_attr .= isset($args['type']) && !empty($args['type']) ? ' data-type="'. esc_attr($args['type']).'"' : '';
        $container_attr .=   $conditionalData.$conditionalStyle;
             
        if(isset($args['container_attrs']) ){
            $container_attr .= $args['container_attrs'];
        }
        
        $select2_ui = false;
		if ($args['multiple'] || $args['select2_ui']) {
			$select2_ui = true;
		}

        $select2 =  self::cwp_frontend_field_select2($select2_ui);
        $classes = "cwp-field-container cwp-search-field cwp-search-field-{$args['type']} form-group {$args['container_class']} {$args['field_size']} {$conditionalClass} {$select2}";
        return '<div class="'.$classes.'" '. $container_attr .'>';

    }
        
    /**
     * Method cwp_frontend_field_required
     *
     * @param bolean $args to check if required true or not
     *
     * @return array 
     * @since  1.0.0
     */
    public static function cwp_frontend_field_required(  $args = '' ) {
        if( $args != true ) return;
        $required = array();
        if(isset($args) && $args == true){
            $required['span']               = ' <span class="cwp-required">*</span>';
            $required['conainer_required']  = ' is-required';
            $required['class']              = ' required';
        }else{
            $required['span']               = '';
            $required['conainer_required']  = '';
            $required['class']              = '';
        }
        return $required;
    }
        
    /**
     * Method cwp_frontend_field_label
     *
     * @param array $args field args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_frontend_field_label(  array $args ) {
        $args     =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required = self::cwp_frontend_field_required($args['required']);
        $required['span'] = $required['span'] ?? '';
        $g_address_switch = '';
        if ($args['type'] == 'google_address') {
            $g_address_switch = '<span class="cubewp-address-manually">' . esc_html__("(Enter Coordinates Manually)", "cubewp-framework") . '</span>';
        }
        $output  = '<label for="' . esc_html($args['id']) . '">'. esc_html($args['label']) . $required['span'] . $g_address_switch . '</label>';
        $output .= self::cwp_frontend_field_description($args);
        return $output;
    }
        
    /**
     * Method cwp_frontend_search_field_label
     *
     * @param array $args field args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_frontend_search_field_label( array $args ) {
        $args     =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $output  = '<label for="' . esc_html($args['id']) . '">'. esc_html($args['label']) . '</label>';
        return $output;
    }
        
    /**
     * Method cwp_frontend_field_select2
     *
     * @param bolean $args to check true/false of select2
     *
     * @return string class name
     * @since  1.0.0
     */
    public static function cwp_frontend_field_select2($args = '') {
        $class = '';
        if( isset($args) && $args == 1 ){
            wp_enqueue_style('select2');
            wp_enqueue_script('select2');
            $class = ' cwp-select2';
        }
        return $class;
    }
        
    /**
     * Method cubewp_frontend_search_data
     *
     * @param string $output output of filter
     * @param array $args search result data args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cubewp_frontend_search_data( string $output , array $args) {
        $total_posts  = '';
        $terms        = '';
        extract($args);
        
        if(empty($terms)){
            $terms = esc_html__("found", "cubewp-framework");
        }else{
            $terms = esc_html__("in ", "cubewp-framework").$terms;
        }
        if($total_posts > 1){
            $results = esc_html__("Results", "cubewp-framework");
        }else{
            $results = esc_html__("Result", "cubewp-framework");
        }
        
        return "<h5>{$total_posts} {$results} {$terms}</h5>";
    }
        
    /**
     * Method results_data
     *
     * @return string html
     * @since  1.0.0
     */
    public static function results_data() {
        echo '<div class="cwp-total-results"></div>';
    }
    
    /**
     * Method list_switcher
     *
     * @return string html
     * @since  1.0.0
     */
    public static function list_switcher() {
		$cwp_active_grid = $cwp_active_list = '';
        if(cwp_get_post_card_view() == 'list-view'){
            $cwp_active_list = 'cwp-active-style';
        }else if(cwp_get_post_card_view() == 'grid-view'){
            $cwp_active_grid = 'cwp-active-style';
        }else{
            $cwp_active_grid = 'cwp-active-style';
        }
        $output = '<div class="cwp-archive-toggle-Listing-style">
            <div class="listing-switcher grid-view '.$cwp_active_grid.'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                </svg>
            </div>
            <div class="listing-switcher list-view '.$cwp_active_list.'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
        </div>';

        echo apply_filters('cubewp/frontend/archive/list/switcher', $output);
    }
        
    /**
     * Method sorting_filter
     *
     * @return string html
     * @since  1.0.0
     */
    public static function sorting_filter( ) {
        $sorting = apply_filters('cubewp/frontend/sorting/filter','');
        $option = [
            'DESC' => esc_html__('Newest','cubewp-framework'),
            'ASC' => esc_html__('Oldest','cubewp-framework'),
            'relevance' => esc_html__('Best Match','cubewp-framework'),
            'title' => esc_html__('Title','cubewp-framework'),
            'rand' => esc_html__('Random','cubewp-framework'),
        ];
        if(!empty($sorting)){
            foreach($sorting as $k=>$v){
                $option[$v.'-ASC'] = $k.': '.esc_html__('Low to high','cubewp-framework');
                $option[$v.'-DESC'] = $k.': '.esc_html__('High to low','cubewp-framework');
            }
        }
        $input_attrs = array( 
            'class'        => 'cwp-orderby',
            'id'           => 'cwp-sorting-filter',
            'name'         => 'cwp_orderby',
            'value'        => isset($_GET['orderby']) && !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'DESC',
            'options'      => $option,
            'extra_attrs'  => '',
            'placeholder'  => esc_html__('Sort By','cubewp-framework')
        );
        echo cwp_render_dropdown_input( $input_attrs );
    }

    /**
     * Method is_cubewp_single
     *
     * @return bool
     * @since  1.0.7
     */
    public static function is_cubewp_single() {
		$get_CustomTypes = CWP_types();
        $get_CustomTypes = apply_filters( 'cubewp/is_single/post_types', $get_CustomTypes );
        if (is_array($get_CustomTypes) && !empty($get_CustomTypes) && count($get_CustomTypes)!=0) {
            foreach ($get_CustomTypes as $single_cpt) {
                self::$post_type = $single_cpt['slug'];
                // Custom Single Page
                if (is_singular( $single_cpt['slug'] )):
                    return true;
                endif;
            }
        }
        return false;
	}

    /**
     * Method is_cubewp_archive
     *
     * @return bool
     * @since  1.0.7
     */
    public static function is_cubewp_archive() {
		$get_CustomTypes = CWP_types();
        if (is_array($get_CustomTypes) && !empty($get_CustomTypes) && count($get_CustomTypes)!=0) {
            foreach ($get_CustomTypes as $single_cpt) {
                self::$post_type = $single_cpt['slug'];
                // Custom Single Page
                if (is_post_type_archive( $single_cpt['slug'] )):
                    return true;
                endif;
            }
        }
        return false;
	}

    /**
     * Method is_cubewp_taxonomy
     *
     *
     * @return bool
     * @since  1.0.7
     */
    public static function is_cubewp_taxonomy() {
		$get_CustomTax = CWP_custom_taxonomies();
        if (is_array($get_CustomTax) && !empty($get_CustomTax) && count($get_CustomTax)!=0) {
            foreach ($get_CustomTax as $single_tax) {
                self::$taxonomy = $single_tax['slug'];
                // Custom Taxonomy Page
                if ($single_tax['public'] == true && is_tax( $single_tax['slug'] )):
                    return true;
                endif;
            }
        }
        return false;
	}

    /**
     * Method have_fields
     *
     * @return void
     */
    public static function have_fields($field = '') {
        if(is_array($field) && !empty($field)){
            $field_count = count($field);
            if($field_count >= 1){
                $field_count -= 1;
            }
            if( $field_count >= self::the_subfield(true)){
                self::$parent_field = $field;
                return true;
            }
		}
		return false;
    }

    /**
     * Method the_subfield
     *
     * @return void
     */
    public static function the_subfield($incriment = false) {
        if($incriment != true){
            return self::$post_count++;
        }
        return self::$post_count;
    }

    /**
     * Method get_subfield_value
     *
     * @return void
     */
    public static function get_subfield_value($field = '') {
		$parent_field = self::$parent_field;
		$post_count = self::$post_count;
        $post_count -= 1;
		if(isset($parent_field[$post_count][$field])){
			return $parent_field[$post_count][$field]['value'];
		}
	}

     /**
     * Method cubewp_post_confirmation
     *
     * @return void
     */
    public static function cubewp_post_confirmation() {
		if(is_singular( )){
            do_action('cubewp_post_confirmation', get_the_ID());
        }
	}

    /**
     * Method cubewp_post_notification
     *
     * @return void
     */
    public static function cubewp_post_notification() {
		if(is_singular( )){
            do_action('cubewp_single_page_notification', get_the_ID());
        }
	}
        
    /**
     * Method init
     *
     * @return void
     */
    public static function init() {
        $CubeClass = __CLASS__;
        $GLOBALS['cubewp_frontend'] = new $CubeClass;
    } 
    
    
}