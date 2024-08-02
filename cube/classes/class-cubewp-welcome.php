<?php
/**
 * Creates the Welcome page for the CubeWP.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Welcome
 */
class CubeWp_Welcome {

    /**
     * Initializes all of the partial classes.
     *
     * @param Submenu_Page $submenu_page A reference to the class that renders the page for the plugin.
     */
    public function __construct() {
        add_action( 'cube_wp_dashboard', array( $this, 'cwp_welcome' ) );
        add_action('cubewp_libraries', array($this, 'cwp_templates_library'));
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
    
    public function cwp_welcome(){
        $field_path = CWP_PLUGIN_PATH . "cube/templates/welcome.php";
            if(file_exists($field_path)){
                include_once $field_path;
            }
    }
    public function  cwp_templates_library(){
        $field_path = CWP_PLUGIN_PATH . "cube/templates/template-library.php";
        if (file_exists($field_path)) {
            include_once $field_path;
        }
    }

    
    
}