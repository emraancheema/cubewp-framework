<?php

/**
 * gateway to wordpress admin side of CubeWP.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 * 
 * CubeWp_Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Admin {
    
    const CubeWp = 'CubeWp_';
    public function __construct() {
        
        spl_autoload_register(array($this, 'admin_classes'));
        add_action('init', array('CubeWp_Post_Types', 'CWP_cpt'));
        add_action('init', array('CubeWp_taxonomy', 'CWP_taxonomies'));
        
        
        // Admin Field's helper functions to render data
        include_once CWP_PLUGIN_PATH . 'cube/functions/fields-helper.php';
        
        // Admin functions
        include_once CWP_PLUGIN_PATH . 'cube/functions/admin-functions.php';
        add_action( 'widgets_init', array( $this, 'CubeWp_register_widgets' ) );
        if (CWP()->is_request('admin')) {
            self::include_fields();
            
            add_filter('cubewp/admin/field/parametrs', array($this, 'admin_fields_parameters'), 10, 2);
            if( ! class_exists( 'WP_List_Table' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }
            add_action('cubewp_loaded', array('CubeWp_Form_Builder', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Search_Builder', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Custom_Fields', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Settings', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Welcome', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Submenu', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Import', 'init'), 10);
            add_action('cubewp_loaded', array('CubeWp_Export', 'init'), 10);
            
            $cubewp_admin_notices = new CubeWp_Admin_Notice();
            $cubewp_admin_notices->cubewp_load_default_notices();

            add_action('admin_print_scripts', array($this, 'cubewp_admin_css'));

            if( !class_exists( 'CubeWp_Frontend_Load' ) ) {
                add_action('cubewp_loaded', array('CubeWp_Builder_Pro', 'init'));
            }

            add_filter( 'post_updated_messages', array( $this, 'cubewp_updated_post_type_messages' ) );
            
        }

    }

    /**
     * Removes few admin menu links
     *
     */
    public function cubewp_admin_css() {
        echo '<style>
           #toplevel_page_cube_wp_dashboard .wp-submenu li:nth-child(6) {
              display: none;
           }
           #toplevel_page_cube_wp_dashboard .wp-submenu li:nth-child(7) {
              display: none;
           }
           #toplevel_page_cube_wp_dashboard .wp-submenu li:nth-child(6).current,
           #toplevel_page_cube_wp_dashboard .wp-submenu li:nth-child(7).current {
              display: block;
           }
        </style>';
        if( ! class_exists( 'CubeWp_Frontend_Load' ) ) {
            echo '<style>
               .wp-submenu li a[href="admin.php?page=cubewp-user-registration-form"]::after,
               .wp-submenu li a[href="admin.php?page=cubewp-user-profile-form"]::after,
               .wp-submenu li a[href="admin.php?page=cubewp-post-types-form"]::after,
               .wp-submenu li a[href="admin.php?page=cubewp-single-layout"]::after,
               .wp-submenu li a[href="admin.php?page=cubewp-user-dashboard"]::after
                {
                    content: "\f160";
                    margin-left:5px;
                    font-family: dashicons;
                    display: inline-block;
                    line-height: 1;
                    font-weight: 400;
                    font-style: normal;
                    speak: never;
                    text-decoration: inherit;
                    text-transform: none;
                    text-rendering: auto;
                    width: 16px;
                    height: 16px;
                    font-size: 16px;
                    vertical-align: top;
                    text-align: center;
                    transition: color .1s ease-in;
               }
            </style>';
        }
        
    }

    /**
     * All CubeWP classes files to be loaded automatically.
     *
     * @param string $className Class name.
     */
    private function admin_classes($className) {

        // If class does not start with our prefix (CubeWp), nothing will return.
        if (false === strpos($className, 'CubeWp')) {
            return null;
        }
        $modules = array(
            'post-types' => 'modules/',
            'search'     => 'modules/',
            'settings'   => 'modules/',
            'taxonomies' => 'modules/',
            'users'      => 'modules/',
            'list-tables'=> 'modules/',
            'elementor'  => 'modules/',
            
            'widgets'    => 'includes/',
            'shortcodes' => 'includes/',
        );
        
        foreach($modules as $module=>$path){
            $file_name = $path.$module.'/class-' .str_replace('_', '-', strtolower($className)).'.php';
            $file = CUBEWP_FILES.$file_name;
            // Checking if exists then include.
            if (file_exists($file)) {
                require_once $file;
            }
        }

        
        return;
    }
    
    /**
     * Method include_fields to include admin fields 
     *
     * @return void
     * @since  1.0.0
     */
    private function include_fields(){
        $admin_fields = array(
            'text', 
            'number', 
            'email', 
            'url', 
            'color', 
            'range',
            'password', 
            'textarea', 
            'wysiwyg-editor', 
            'oembed', 
            'file', 
            'image', 
            'gallery',
            'dropdown', 
            'checkbox', 
            'radio', 
            'switch', 
            'google-address', 
            'date-picker', 
            'date-time-picker', 
            'time-picker', 
            'post', 
            'taxonomy', 
            'user', 
            'repeater'
        );
        foreach($admin_fields as $admin_field){
            $FileName = "cubewp-admin-{$admin_field}-field.php";
            $field_path = CUBEWP_FILES."fields/admin/".$FileName;
            if(file_exists($field_path)){
                include_once $field_path;
            }
        }
    }
        
    /**
     * Method register_elementor_tags to include all elementer tag files
     *
     * @param $module $module is elementor tag argument 
     *
     * @return void
     * @since  1.0.0
     */
    public static function register_elementor_tags($module) {

        $tags = CubeWp_Posttype_Custom_Fields_Display::cwp_form_field_types();
		$module->register_group( 'cubewp-fields', [
			'title' => esc_html__( 'CubeWP Custom Fields', 'cubewp-framework' ),
		] );
        
		foreach ( $tags as $tag=>$label ) {
            $tag = 'CubeWp_Tag_'.ucfirst($tag);
			$module->register( new $tag() );
		}
	}

    public function cubewp_updated_post_type_messages( $messages ) {
        global $post, $post_ID;
        $post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );
        foreach ( $post_types as $post_type => $post_object ) {
           $messages[ $post_type ] = array(
              0  => '', // Unused. Messages start at index 1.
              1  => sprintf( __( '%s updated. <a href="%s">View %s</a>' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
              2  => __( 'Custom field updated.' ),
              3  => __( 'Custom field deleted.' ),
              4  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
              5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s' ), $post_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
              6  => sprintf( __( '%s published. <a href="%s">View %s</a>' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
              7  => sprintf( __( '%s saved.' ), $post_object->labels->singular_name ),
              8  => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
              9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %s</a>' ), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
              10 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview %s</a>' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
           );
           if ($post_type == 'price_plan') {
              $messages[ $post_type ][1] = sprintf( __( '%s updated.' ), $post_object->labels->singular_name );
              $messages[ $post_type ][6] = sprintf( __( '%s published.' ), $post_object->labels->singular_name );
              $messages[ $post_type ][8] = sprintf( __( '%s submitted.' ), $post_object->labels->singular_name );
              $messages[ $post_type ][9] = sprintf( __( '%s scheduled for: <strong>%1$s</strong>.' ), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) );
              $messages[ $post_type ][10] = sprintf( __( '%s draft updated.' ), $post_object->labels->singular_name );
           }
        }
     
        return $messages;
     }
        
    /**
     * Method admin_fields_parameters to pasrse field arguments
     *
     * @param $args $args field args
     *
     * @return void
     * @since  1.0.0
     */
    public function admin_fields_parameters( $args = array() ){

        $default = array(
            'type'                  =>    'text',
            'id'                    =>    '',
            'class'                 =>    '',
            'container_class'       =>    '',
            'name'                  =>    '',
            'custom_name'           =>    '',
            'value'                 =>    '',
            'minimum_value'           =>   0,
            'maximum_value'           =>   100,
            'steps_count'             =>   1,
            'file_types'              =>   '',
            'placeholder'           =>    '',
            'label'                 =>    '',
            'description'           =>    '',
            'required'              =>    false,
            'conditional'           =>    0,
            'conditional_field'      =>    '',
            'conditional_operator'   =>    '',
            'conditional_value'      =>    '',
            'multiple'              =>    0,
            'select2_ui'            =>    0,
            'sub_fields'            =>    array(),
            'wrap'                  =>    true,
        );
        return wp_parse_args($args, $default);

    }
        
    /**
     * Method CubeWp_register_widgets
     *
     * @return void
     * @since  1.0.0
     */
    public function CubeWp_register_widgets() {
        foreach ( $this->CubeWp_arrange_widgets() as $widget ) {
            register_widget( self::CubeWp . $widget );
        }
    }
    
    /**
     * Method CubeWp_arrange_widgets
     *
     * @return array
     * @since  1.0.0
     */
    private function CubeWp_arrange_widgets() {
       $widgets = array(
          "Posts_Widget",
          "Terms_Widget",
       );
       return $widgets;
    }
        
    /**
     * Method cwp_tr_start
     *
     * @param $args $args field args
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_tr_start($args = array()) {
        return '<tr '.self::cwp_conditional_attributes($args).' valign="top">';
    }
    
    /**
     * Method cwp_tr_end
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_tr_end() {
        return '</tr>';
    }
    
    /**
     * Method cwp_th_start
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_th_start() {
        return '<th scope="row">';
    }
    
    /**
     * Method cwp_th_end
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_th_end() {
        return '</th>';
    }
    
    /**
     * Method cwp_td_start
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_td_start() {
        return '<td>';
    }
    
    /**
     * Method cwp_td_end
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_td_end() {
        return '</td>';
    }
    
    /**
     * Method cwp_required_span
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_required_span() {
        return ' <span class="cwp-required">*</span>';
    }
    
    /**
     * Method cwp_label
     *
     * @param $label_for $label_for is string, id of field
     * @param $label_text $label_text string
     * @param $required_span $required_span string span of required star
     * @param $tooltip $tooltip string
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_label( $label_for = '', $label_text = '', $required_span = false, $tooltip = '' ) {

        if( $label_text == '' ){
            return '';
        }

        $output = '<label for="' . esc_attr( $label_for ) . '">';
            $output .= wp_strip_all_tags( $label_text );
            if( $required_span == true ){
                $output .= self::cwp_required_span();
            }
        $output .= self::cwp_field_tooltip($tooltip);
        $output .= '</label>';

        return $output;
    }
    
    /**
     * Method cwp_field_description
     *
     * @param $args $args array of field args
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_field_description(  $args = array() ) {

        if( !$args['description'] ) return;
        return '<p class="description">' . $args['description'] . '</p>';

    }
        
    /**
     * Method cwp_field_tooltip
     *
     * @param $tooltip $tooltip string
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_field_tooltip(  $tooltip = '' ) {

        if( empty($tooltip) ) return;
        return '<div class="cwp-icon-helpTip">
                    <span class="dashicons dashicons-editor-help"></span>
                    <div class="cwp-ctp-toolTips">
                        <div class="cwp-ctp-toolTip">
                        <h4>'.esc_html__('Tool Tip','cubewp-framework').'</h4>
                        <p class="cwp-ctp-tipContent">'.esc_html($tooltip).'</p>
                    </div>
                    </div>
                </div>';

    }
        
    /**
     * Method cwp_conditional_attributes
     *
     * @param $args $args array of field args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_conditional_attributes($args = array()) {
        if(isset($args['conditional']) && 
           !empty($args['conditional']) && 
           !empty($args['conditional_field']))
        {
            $condi_val = $args['conditional_operator'] != '!empty' && 'empty' !=  $args['conditional_operator'] ? $args['conditional_value'] : '';
            $attr = 'style="display:none"';
            $attr .= 'data-field="'.$args['conditional_field'].'"';
            $attr .= 'data-operator="'.$args['conditional_operator'].'"';
            $attr .= 'data-value="'.$condi_val.'"';
            $attr .= 'class="conditional-logic '.$args['conditional_field'].$args['conditional_value'].' '.$args['container_class'].'"';
            return $attr;
        }elseif(!empty($args['container_class'])){
            $args['container_attrs'] = isset($args['container_attrs']) ? $args['container_attrs'] : '';
            $attr = 'class="'.$args['container_class'].'"';
            $attr .= ' '. $args['container_attrs'] . ' ';
            return $attr;
           }
        return '';
    }
    
    /**
     * Method cwp_field_wrap_start
     *
     * @param $args $args array of field args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_field_wrap_start( $args = array() ) {

        if($args['wrap'] != true ) return;
        $tooltip = isset($args['tooltip']) && !empty($args['tooltip']) ? $args['tooltip'] : '';
        $output = self::cwp_tr_start($args);
        if(isset($args['label']) && $args['label'] != ''){
            $output .= self::cwp_th_start();
                $output .= self::cwp_label( $args['id'], $args['label'], $args['required'], $tooltip );
            $output .= self::cwp_th_end();
        }
        $output .= self::cwp_td_start();

        return $output;
    }
    
    /**
     * Method cwp_field_wrap_end
     *
     * @param $args $args array of field args
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cwp_field_wrap_end( $args = array() ) {

        if($args['wrap'] != true ) return;

        $output = self::cwp_field_description($args);
        $output .= self::cwp_td_end();
        $output .= self::cwp_tr_end();

        return $output;
    }
    
     
    /**
     * Method init
     *
     * @return void
     */
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }

}