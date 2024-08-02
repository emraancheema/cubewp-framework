<?php
/**
 * CubeWp Initialization.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 */
defined('ABSPATH') || exit;

/**
 * CubeWp load Class.
 *
 * @class CubeWp_Load
 */
final class CubeWp_Load {
    
    
    /**
     * Wordpress required version.
     *
     * @var string
     */
    public static $CubeWp_version = '1.1.16';
    
    /**
     * Wordpress required version.
     *
     * @var string
     */
    public static $wp_req_version = '5';

    /**
     * PHP required version.
     *
     * @var string
     */
    public static $php_req_version = '7.4';
    
     /**
	 * The single instance of the class.
	 *
	 * @var CubeWp_Load
	 */
    protected static $Load = null;
    
    /**
     * Settings instance.
     *
     * @var CubeWp_settings
     */
    public $settings = null;
    
    /**
     * prefix of cubewp data.
     *
     * @var $prefix
     */
    
    public static $prefix = 'cwp';
    
    /**
     * plugin base of cubewp framework.
     *
     * @var $base
     */
    
    public $base = 'cubewp-framework/cube.php';

    /**
     * plugin slug of cubewp framework.
     *
     * @var $slug
     */
    
    public $slug = 'cubewp-framework';

    public static function instance() {
		if ( is_null( self::$Load ) ) {
			self::$Load = new self();
		}
		return self::$Load;
	}
    /**
     * CubeWp_Load Constructor.
     */
    public function __construct() {
        /* CUBEWP_VERSION is defined for current cubewp version */
        if (!defined('CUBEWP_VERSION')) {
            define('CUBEWP_VERSION', self::$CubeWp_version);
        }
		self::init_hooks();
        self::includes();
    }
        
    /**
     * Method init_hooks
     *
     * @since  1.0.0
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), -1);
        add_action('init', array($this, 'init'), 0);
        add_filter( 'plugin_row_meta', array( $this, 'plugin_view_info' ), 80, 3 );
        add_action('init', array('CubeWp_Add_Ons', 'init'), 9);
        add_action( 'rest_api_init', array('CubeWp_Rest_API', 'init' ));
    }
    
    /**
     * Include required core files used in admin and on the frontend.
     * Method includes
     *
     * @since  1.0.0
     */
    public function includes() {
        add_action('cubewp_loaded', array('CubeWp_Admin', 'init'), 9);
        add_action('cubewp_loaded', array('CubeWp_Enqueue', 'init'), 9);
        add_action( 'elementor/dynamic_tags/register', array('CubeWp_Admin','register_elementor_tags'), 10);

	    add_action('cubewp_loaded', array('CubeWp_Shortcodes', 'init'));
	    // Frontend Page Builders
	    add_action('cubewp_loaded', array('CubeWp_Elementor', 'init'));
        add_action('cubewp_loaded', array('CubeWp_Vc_Elements', 'init'));
        add_action('cubewp_loaded', array('CubeWp_Relationships', 'init'));
        if (self::is_request('frontend')) {
            self::frontend_includes();
        }
    }
    
    /**
     * Include required frontend files.
     * Method frontend_includes
     *
     * @since  1.0.0
     */
    public function frontend_includes() {
        add_action('cubewp_loaded', array('CubeWp_Frontend', 'init'), 9);
    }
    
    /**
     * Init CubeWp when WordPress Initialises.
     * Method init
     *
     * @since  1.0.0
     */
    public function init() {
        // Set up localisation.
        self::load_plugin_textdomain();
        // Set Cubewp settings.
        self::cwp_get_option();
    }
    
        
    /**
     * Method prefix
     *
     * @return string
     * @since  1.0.0
     */
    public function prefix() {
        return self::$prefix;
    }
        
    /**
     * Method cwp_get_option
     *
     * @return array
     * @since  1.0.0
     */
    public function cwp_get_option() {
        $cwpOption = get_option('cwpOptions');
        $GLOBALS['cwpOptions'] = $cwpOption;
    }
        
    /**
     * Method cubewp_options
     *
     * @param string $optName
     *
     * @return array
     * @since  1.0.0
     */
    public function cubewp_options( string $optName ){
        if(empty($optName)) return '';
        
        $options = get_option($optName);
        $options =   isset($options) ? $options  : '';
        return $options;
    }

    public function update_cubewp_options( string $optName, $optionSet='' ){
        if(empty($optName)) return '';

        update_option($optName,$optionSet);
    }
        
    /**
     * Method get_custom_fields
     *
     * @param string $type
     *
     * @return array
     * @since  1.0.0
     */
    public function get_custom_fields( string $type ){
        if(empty($type)) return '';
        
        $fields = $this->cubewp_options(cwp_get_opt_hook($type));
        $fields =   isset($fields) ? $fields  : array();
        return $fields;
    }
        
    /**
     * Method update_custom_fields
     *
     * @param string $type 
     * @param array $fields_data
     *
     * @return void
     * @since  1.0.0
     */
    public function update_custom_fields( string $type , $fields_data= array() ){
        if(empty($type)) return '';
        update_option(cwp_get_opt_hook($type), $fields_data);
    }
        
    /**
     * Method get_form
     *
     * @param string $type
     *
     * @return array
     * @since  1.0.0
     */
    public function get_form( string $type ){
        if(empty($type)) return '';
        
        $form = $this->cubewp_options(self::$prefix.'_'.$type.'_form');
        $form =   !empty($form) ? $form  : array();
        return $form;
    }
        
    /**
     * Method update_form
     *
     * @param string $type 
     * @param array $cwp_forms
     *
     * @return void
     * @since  1.0.0
     */
    public function update_form( string $type, $cwp_forms=array() ){
        if(empty($type)) return '';
        return update_option(self::$prefix.'_'.$type.'_form', $cwp_forms);
    }
    
    /**
     * cubewp_plugins_loaded action hook to load something on plugin_loaded action.
     * Method on_plugins_loaded
     *
     * @since  1.0.0
     */
    public function on_plugins_loaded() {
        do_action('cubewp_loaded');
    }
    

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     * - WP_LANG_DIR/cubewp/cubewp-LOCALE.mo
     * - WP_LANG_DIR/plugins/cubewp-LOCALE.mo
     * 
     * Method load_plugin_textdomain
     *
     * @since  1.0.0
     */
    public function load_plugin_textdomain() {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }

        $locale = apply_filters('plugin_locale', $locale, 'cubewp-framework');

        unload_textdomain('cubewp-framework');
        load_textdomain('cubewp-framework', WP_LANG_DIR . '/cubewp-framework/cubewp-framework' . $locale . '.mo');
        load_plugin_textdomain('cubewp-framework', false, plugin_basename(dirname(CWP_PLUGIN_FILE)) . '/languages');
    }
    
    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

    /**
     * Method is_admin_screen
     *
     * @param string $is cubewp, or any cubewp custom page slug
     *
     * @return bool
     * @since  1.0.0
     */
    public function is_admin_screen($is) {
        if(self::is_request('admin')):
        if($is == 'cubewp' && current_cubewp_page() != null ){
            return true;
        } else if(current_cubewp_page() == $is){
                return true;
        }else{
            return false;
        }
        endif;
	}
    
    public function plugin_view_info( $plugin_meta, $file, $plugin_data ) {

        if ( $file != plugin_basename( $this->base ) ) return $plugin_meta;
        $cwp_plugin_meta = array(
            '<a href="https://cubewp.com/store/" target="_blank">Add-Ons</a>',
            '<a href="https://support.cubewp.com/" target="_blank">CubeWP Documentation</a>',
            '<a href="https://support.cubewp.com/forums/forum/community/" target="_blank">CubeWP Community</a>',
            '<a href="https://support.cubewp.com/forums/forum/feedback/" target="_blank">Feedback</a>',
            '<a href="https://www.youtube.com/channel/UCKGX3FHQv7xFylXQZOPOy7w" target="_blank">Video Tutorials</a>',
        );
        $plugin_meta = array_merge($plugin_meta,$cwp_plugin_meta);

        return $plugin_meta;

    }
}