<?php
defined('ABSPATH') || exit;

use Elementor\Widgets_Manager;

/**
 * CubeWP load Elementor Widgets.
 *
 * @class CubeWp_Elementor
 */
final class CubeWp_Elementor {

	/**
	 * Addon Version
	 *
	 * @since 1.0.0
	 * @var string The addon version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @var CubeWp_Elementor The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		if ($this->is_compatible()) {
			add_action('elementor/init', array($this, 'init_elementor_widgets'));
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function is_compatible() {
		// Check if Elementor installed and activated
		if ( ! cubewp_check_if_elementor_active()) { 
			return false;
		}
		// Check for required Elementor version
		if ( ! version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
			$message = sprintf(/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */ esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-framework'), '<strong>' . esc_html__('CubeWP', 'cubewp-framework') . '</strong>', '<strong>' . esc_html__('Elementor', 'cubewp-framework') . '</strong>', self::MINIMUM_ELEMENTOR_VERSION);
			new CubeWp_Admin_Notice("elementor-version", $message, 'warning');
			
			return false;
		}
		// Check for required PHP version
		if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
			$message = sprintf(/* translators: 1: Plugin name 2: PHP 3: Required PHP version */ esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-framework'), '<strong>' . esc_html__('CubeWP', 'cubewp-framework') . '</strong>', '<strong>' . esc_html__('PHP', 'cubewp-framework') . '</strong>', self::MINIMUM_PHP_VERSION);
			new CubeWp_Admin_Notice("elementor-php-version", $message, 'warning');
			
			return false;
		}

		return true;
	}

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return CubeWp_Elementor An instance of the class.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init_elementor_widgets() {
		add_action('elementor/elements/categories_registered', array($this, 'elementor_widget_category'));
		add_action('elementor/widgets/register', array($this, 'register_widgets'));
		spl_autoload_register(array($this, 'require_widgets_files'));
	}

	/**
	 * Widget Categories
	 *
	 * Adding New Widgets Category.
	 */
	public function elementor_widget_category($widgets_manager) {
		$widgets_manager->add_category('cubewp', array('title' => __('CubeWP', 'cubewp-framework')));
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets($widgets_manager) {
		$classes = array(
			'Taxonomy_Widget',
			'Posts_Widget'
		);
		$classes = apply_filters("cubewp/elementor/widgets/classes", $classes);
		if (!empty($classes && is_array($classes))) {
			foreach ($classes as $class) {
				$class = 'CubeWp_Elementor_' . $class;
				if (class_exists($class)) {
					$widgets_manager->register(new $class());
				}else {
					wp_die(sprintf(esc_html__("%s Class Doesn't Exist.", "cubewp-framework"), $class));
				}
			}
		}
	}

	/**
	 * Auto Loading Widgets Files
	 *
	 * Load Elementor widgets files.
	 */
	private static function require_widgets_files($className) {
		// If class does not start with our prefix (Cubewp_Elementor), nothing will return.
		if (false === strpos($className, 'CubeWp_Elementor')) {
			return null;
		}
		// Replace _ with - to match the file name.
		$file_name = str_replace('_', '-', strtolower($className));
		// Calling class file.
		$files = array(
			CUBEWP_CLASSES . 'page-builders/elementor-widgets/class-' . $file_name . '.php'
		);
		$files = apply_filters("cubewp/elementor/widgets/files", $files, $file_name);
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