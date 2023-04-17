<?php
/**
 * CubeWp shortcode generates cubewp related wordpress default content shortocdes.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWP load Shortcodes.
 *
 * @class CubeWp_Shortcodes
 */
class CubeWp_Shortcodes {

	public function __construct() {
		self::load_shortcodes();
		spl_autoload_register(array($this, 'require_shortcodes_files'));
		add_shortcode( 'cubewp_post_field', array($this, 'post_type_custom_fields_shortcode') );
	}
	
	/**
	 * Method load_shortcodes
	 *
	 * @return void
	 * @since  1.0.0
	 */
	private static function load_shortcodes() {
		// Frontend Shortcodes
		add_action('init', array('CubeWp_Shortcode_Taxonomy', 'init'), 10);
		add_action('init', array('CubeWp_Shortcode_Posts', 'init'), 10);
	}
	
	/**
	 * Method require_shortcodes_files
	 *
	 * @param  $className class name
	 *
	 * @return void
	 * 
	 */
	private static function require_shortcodes_files($className) {
		// If class does not start with our prefix (Cubewp_Elementor), nothing will return.
		if (false === strpos($className, 'CubeWp_Shortcode')) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace('_', '-', strtolower($className));
		// Calling class file.
		$files = array(
			CUBEWP_CLASSES . 'shortcodes/class-' . $file_name . '.php'
		);
		// Checking if exists then include.
		foreach ($files as $file) {
			if (file_exists($file)) {
				require $file;
			}
		}

		return $className;
	}
	/**
     * Method post type custom fields shortcode
     *
     * @return void
     * @since  1.0.0
     */
    public function post_type_custom_fields_shortcode($params = array(), $content = null){
        // default parameters
        extract(shortcode_atts(array(
            'field' => '',
            'post_id' => '',
			), $params)
		);

		return get_field_value($field,true,$post_id);
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