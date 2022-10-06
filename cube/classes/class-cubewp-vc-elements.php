<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWP Posts Element.
 *
 * Visual Composer Element For Posts By CubeWP.
 *
 * @since 1.0.0
 */
class CubeWp_Vc_Elements {

	public function __construct() {
		if ( class_exists('WPBakeryVisualComposerAbstract') ) {
			add_action( 'init', array( $this, 'cubewp_register_vc_elements' ) );
			spl_autoload_register(array($this, 'require_widgets_files'));
		}
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	public function cubewp_register_vc_elements() {
		$classes = array(
			'Posts_Widget',
			'Taxonomy_Widget'
		);
		$classes = apply_filters("cubewp/vc/elements/classes", $classes);
		if (!empty($classes && is_array($classes))) {
			foreach ($classes as $class) {
				$class = 'CubeWp_VC_' . $class;
				if (class_exists($class)) {
					new $class();
				}else {
					wp_die(sprintf(esc_html__("%s Class Doesn't Exist.", "cubewp-framework"), $class));
				}
			}
		}
	}

	/**
	 * Auto Loading Elements Files
	 *
	 * Load Visual Composer elements files.
	 */
	private static function require_widgets_files($className) {
		// If class does not start with our prefix (CubeWp_VC), nothing will return.
		if (false === strpos($className, 'CubeWp_VC')) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace('_', '-', strtolower($className));
		// Calling class file.
		$files = array(
			CUBEWP_CLASSES . 'page-builders/vc-elements/class-' . $file_name . '.php'
		);
		$files = apply_filters("cubewp/vc/elements/files", $files, $file_name);
		// Checking if exists then include.
		if (!empty($files) && is_array($files)) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					require $file;
				}
			}
		}

		return $className;
	}
}