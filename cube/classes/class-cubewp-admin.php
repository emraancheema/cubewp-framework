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

    use CubeWp_Builder_Ui;

    const CubeWp = 'CubeWp_';
    public function __construct() {
        
        spl_autoload_register(array($this, 'admin_classes'));
        add_action('init', array('CubeWp_Post_Types', 'CWP_cpt'));
        add_action('init', array('CubeWp_taxonomy', 'CWP_taxonomies'));
        
        
        // Admin Field's helper functions to render data
        include_once CWP_PLUGIN_PATH . 'cube/functions/fields-helper.php';
        
        // Admin functions
        include_once CWP_PLUGIN_PATH . 'cube/functions/admin-functions.php';

        // Block Render
        include_once CWP_PLUGIN_PATH . 'cube/functions/blocks-render.php';

        add_action( 'widgets_init', array( $this, 'CubeWp_register_widgets' ) );
        
        //CubeWP TB init
        add_action( 'cubewp_loaded', array( 'CubeWp_Theme_Builder', 'init' ) );
        add_action( 'cubewp_loaded', array( 'CubeWp_Theme_Builder_Rules', 'init' ) );
        add_filter('elementor/documents/register', [$this, 'add_elementor_support']);
        add_filter('cubewp/posttypes/new', [$this, 'register_theme_builder_post_type']);
        add_filter('cubewp-submenu', array($this, 'Cubewp_Menu'), 10);
        new CubeWp_Ajax( '', 'CubeWp_Theme_Builder', 'cubewp_theme_builder_template' );
        add_action('admin_footer', ['CubeWp_Theme_Builder', 'add_custom_popup_form']);

        //Only admin related
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
            add_action('cubewp_loaded', array('CubeWp_Loop_Builder', 'init'), 10);

            ( new CubeWp_Admin_Notice )->cubewp_load_default_notices();

            add_action('admin_print_scripts', array($this, 'cubewp_admin_css'));

            if( !class_exists( 'CubeWp_Frontend_Load' ) ) {
                add_action('cubewp_loaded', array('CubeWp_Builder_Pro', 'init'));
            }
			
			if( !class_exists( 'CubeWp_Forms_Custom' ) ) {
                add_action('cubewp_loaded', array('CubeWp_Forms_Pro', 'init'));
            }

            add_filter( 'post_updated_messages', array( $this, 'cubewp_updated_post_type_messages' ) );

            new CubeWp_Ajax( '',
                self::class,
                'cubewp_get_builder_widgets'
            );
            
            new CubeWp_Ajax( '',
                self::class,
                'cubewp_process_post_card_css'
            );
            
        }
    }

    /**
     * Method cubewp_get_builder_widgets to Ajax callback function 
     *
     * @return json
     * @since  1.1.10
     */
    public static function cubewp_get_builder_widgets() {

        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp-admin-nonce")) {
			wp_send_json_error(array(
				'msg' => esc_html__('Sorry! Security Verification Failed.', 'cubewp-framework'),
			), 404);
		}
        
        $widgets_ui = self::cubewp_builder_widgets_display($_POST['nested_switcher'],$_POST['form_type'],$_POST['slug']);
		wp_send_json_success(array( 'sidebar' => $widgets_ui));
	}

        /**
     * Method cubewp_process_post_card_css
     *
     * @param string $type builder name
     *
     * @return void
     * @since  1.0.0
     */
    public static function cubewp_process_post_card_css() {
        if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp-admin-nonce")) {
			wp_send_json_error(array(
				'msg' => esc_html__('Sorry! Security Verification Failed.', 'cubewp-framework'),
			), 404);
		}
        if (isset($_POST['styles']) && is_string($_POST['styles'])) {

            $data = json_decode(stripslashes($_POST['styles']), true);
            if (is_array($data)) {
                $cleaned_data = array_map(function($item) {
                    // Remove surrounding quotes
                    $item = trim($item, '"');
                    // Replace escaped newlines with actual newlines
                    $item = str_replace('\n', "\n", $item);
                    // Replace plus signs with spaces
                    $item = str_replace('+', ' ', $item);
                    return $item;
                }, $data);

                // Convert the cleaned array into a single string
                $css_code = implode("\n", $cleaned_data);
    
                if (!file_exists(CUBEWP_POST_CARDS_DIR)) {
                    wp_mkdir_p(CUBEWP_POST_CARDS_DIR);
                }
                $file_path = CUBEWP_POST_CARDS_DIR . '/cubewp-post-cards.css';
    
                if (!file_exists(dirname($file_path))) {
                    mkdir(dirname($file_path), 0755, true);
                }
    
                if (file_put_contents($file_path, $css_code) !== false) {
                    wp_send_json_success(array('message' => 'CSS file created successfully', 'file' => $file_path));
                } else {
                    wp_send_json_error(array('message' => 'Failed to write CSS file'));
                }
            } else {
                wp_send_json_error(array('message' => 'Invalid JSON data'));
            }
        } else {
            wp_send_json_error(array('message' => 'No valid data received'));
        }
    
        wp_die();
    }

    /**
     * Removes few admin menu links
     *
     */
    public function cubewp_admin_css() {
        echo '<style>
        .wp-submenu li a[href="admin.php?page=cubewp-libraries"]{
            display: none!important;
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
            'custom-fields' => 'modules/',
            'theme-builder'  => 'modules/',
            'post-types' => 'modules/',
            'users'      => 'modules/',
            'search'     => 'modules/',
            'settings'   => 'modules/',
            'taxonomies' => 'modules/',
            'list-tables'=> 'modules/',
            'elementor'  => 'modules/',
            'recaptcha'  => 'modules/',
            'builder'  => 'modules/',
            
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
            'business-hours', 
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
        $tags = CubeWp_Custom_Fields_Markup::cwp_form_field_types( 'elementor_dynamic_tags' );
        $module->register_group( 'cubewp-fields', [
        'title' => esc_html__( 'CubeWP Custom Fields', 'cubewp-framework' ),
        ] );
        $field_types = array_reduce($tags, 'array_merge', array());
        foreach ( $field_types as $tag=>$label ) {
            $tag = 'CubeWp_Tag_'.ucfirst($tag);
            if(class_exists($tag)){
                $module->register( new $tag() );
            }
        }
    
        $module->register_group( 'cubewp-single-fields', [
            'title' => esc_html__( 'CubeWP Single Post Data', 'cubewp-framework' ),
        ] );
        $single_tags = array(
            "title" => esc_html__("Post Title", "cubewp-framework"),
            "post_content" => esc_html__("Post Content", "cubewp-framework"),
            "featured_image" => esc_html__("Featured Image", "cubewp-framework"),
            "post_author" => esc_html__("Post Author", "cubewp-framework"),
            "post_info" => esc_html__("Post Info", "cubewp-framework"),
            "post_term" => esc_html__("Post Term", "cubewp-framework"),
            "post_share" => esc_html__("Post Share Button", "cubewp-framework"),
            "post_save" => esc_html__("Post Save Button", "cubewp-framework"),
            "custom_fields" => esc_html__("CubeWP Custom Fields", "cubewp-framework")
        );
        foreach ( $single_tags as $tag => $label ) {
            $tag = 'CubeWp_Tag_'.ucfirst($tag);
            if(class_exists($tag)){
                $module->register( new $tag() );
            }
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
            'minimum_value'         =>   0,
            'maximum_value'         =>   100,
            'steps_count'           =>   1,
            'file_types'            =>   '',
            'placeholder'           =>    '',
            'label'                 =>    '',
            'description'           =>    '',
            'required'              =>    false,
            'conditional'           =>    0,
            'conditional_field'     =>    '',
            'conditional_operator'  =>    '',
            'conditional_value'     =>    '',
            'current_user_posts'    =>    0,
            'char_limit'            =>    '',
            'multiple'              =>    0,
            'select2_ui'            =>    0,
            'editor_media'          =>    0,
            'sub_fields'            =>    array(),
            'wrap'                  =>    true,
            'files_save'            =>    'ids',
            'files_save_separator'  =>    'array',
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
		$size_class = 'cwp-field-size-full';
		if (isset($args['admin_size']) && !empty($args['admin_size'])) {
			if ($args['admin_size'] == '1/2') {
				$size_class = 'cwp-field-size-two';
			}elseif ($args['admin_size'] == '1/3') {
				$size_class = 'cwp-field-size-three';
			}
		}
		if(isset($args['conditional']) &&
           !empty($args['conditional']) && 
           !empty($args['conditional_field']))
        {
            $condi_val = $args['conditional_operator'] != '!empty' && 'empty' !=  $args['conditional_operator'] ? $args['conditional_value'] : '';
            $options = get_field_options( $args['conditional_field'] );
            $attr = 'style="display:none"';
            $attr .= 'data-field="'.$args['conditional_field'].'"';
            $attr .= 'data-operator="'.$args['conditional_operator'].'"';
            $attr .= 'data-value="'.$condi_val.'"';
            $attr .= 'data-field-id="'.$options['id'].'"';
            $attr .= 'class="' . $size_class . ' conditional-logic '.$args['conditional_field'].$args['conditional_value'].' '.$args['container_class'].'"';
            return $attr;
        }elseif(!empty($args['container_class'])){
            $args['container_attrs'] = isset($args['container_attrs']) ? $args['container_attrs'] : '';
            $attr = 'class="' . $size_class . ' '.$args['container_class'].'"';
            $attr .= ' '. $args['container_attrs'] . ' ';
            return $attr;
		}else {
			return 'class="' . $size_class . '"';
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

    public function register_theme_builder_post_type($defaultCPT) {
        $defaultCPT['cubewp-tb']   = array(
            'label'                  => __('Theme Builder', 'cubewp-framework'),
            'singular'               => 'cwp-tb',
            'icon'                   => '',
            'slug'                   => 'cubewp-tb',
            'description'            => __('Custom post type for theme builder templates', 'cubewp-framework'),
            'supports'               => array('title'),
            'hierarchical'           => false,
            'public'                 => false,
            'show_ui'                => true,
            'menu_position'          => '',
            'show_in_menu'           => false,
            'show_in_nav_menus'      => false,
            'show_in_admin_bar'      => false,
            'can_export'             => true,
            'has_archive'            => false,
            'exclude_from_search'    => true,
            'publicly_queryable'     => true,
            'query_var'              => false,
            'rewrite'                => false,
            'rewrite_slug'           => '',
            'rewrite_withfront'      => false,
            'show_in_rest'           => true,
        );
        return $defaultCPT;
    }

    public function Cubewp_Menu($settings) {
        register_post_status( 'inactive', array(
            'label'                     => _x( 'Inactive ', 'Inactive', 'cubewp-framework' ),
            'public'                    => true,
            'label_count'               => _n_noop( 'Inactive s <span class="count">(%s)</span>', 'Inactive s <span class="count">(%s)</span>', 'cubewp-framework' ),
            'post_type'                 => array( 'cubewp-tb'), 
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => 'dashicons-businessman',
        ) );
        $settings[]	=	array(
            'id'        => 'cubewp-theme-builder', // Expected to be overridden if dashboard is enabled.
            'parent'    => 'cube_wp_dashboard',
            'title'     => esc_html__('Theme Builder', 'cubewp-framework').'<span class="new-feature-tag"></span>',
            'callback'  => 'cubewp-theme-builder',
            'position'     => 5
        );
		
        return $settings;
    }

    public function add_elementor_support($post_types) {
        add_post_type_support('cubewp-tb', 'elementor');
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