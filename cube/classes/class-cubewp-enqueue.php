<?php
/**
 * CubeWp Enqueue.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CubeWp_Enqueue extends CubeWp_Admin_Enqueue {

	/**
	 * Contains an array of script handles registered by CWP.
	 *
	 * @var array
	 */
	//private static $scripts = array();

	/**
	 * Contains an array of script handles registered by CWP.
	 *
	 * @var array
	 */
	//private static $styles = array();

	/**
	 * Contains an array of script handles localized by CWP.
	 *
	 * @var array
	 */
	//private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_admin_scripts' ), 10 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'localize_admin_printed_scripts' ), 10 );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		self::register_scripts();
		self::register_styles();

		if ( ! is_admin() ) {
			CubeWp_Enqueue::enqueue_style( 'cwp-alert-ui' );
			CubeWp_Enqueue::enqueue_script( 'cwp-alert-ui' );
			$file_path = CUBEWP_POST_CARDS_DIR . '/cubewp-post-cards.css';
			if (file_exists(dirname($file_path))) {
				CubeWp_Enqueue::enqueue_style( 'cwp-post-cards' );
			}
		}

		// CSS Styles.
		$enqueue_styles = self::get_styles();
		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}

		echo apply_filters( 'frontend/script/enqueue', '' );
		self::cubewp_enqueue_settings_css_js();

	}

	/**
	 * Register all CWP scripts.
	 */
	private static function register_scripts() {
		global $cwpOptions;
		$register_scripts = array(
			'cwp-alert-ui'             => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/cubewp-alerts.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-search'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/cwp-search.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cwp-search-filters'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/search-filters.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-load-more'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/load-more.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-tabs'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/cwp-tabs.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-timepicker'         => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/timepicker/jquery-ui-timepicker-addon.min.js',
				'deps'    => array( 'jquery-ui-datepicker' ),
				'version' => CUBEWP_VERSION,
			),
			'select2'                => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/select2/select2.full.min.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'google_map_api'           => array(
				'src'     => 'https://maps.googleapis.com/maps/api/js?key=' . cwp_google_api_key() . '&libraries=places',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-map'             => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/cwp-map.js',
				'deps'    => array( 'google_map_api' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-leaflet'         => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/leaflet/leaflet.min.js',
				'deps'    => array(),
				'version' => '',
			),
			'cubewp-leaflet-cluster' => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/leaflet/leaflet.markercluster.min.js',
				'deps'    => array( 'cubewp-leaflet' ),
				'version' => '',
			),
			'cubewp-leaflet-fullscreen' => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/leaflet/Leaflet.fullscreen.min.js',
				'deps'    => array('cubewp-leaflet'),
				'version' => '',
			 ),
			'cwp-form-validation'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/frontend-form-validation.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-frontend-fields'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/frontend-fields.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-repeating-fields'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/repeatable-fields.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-business-hours-fields'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/business-hour-field.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cwp-google-address-field' => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/google-address-field.js',
				'deps'    => array( 'google_map_api' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-pretty-photo'         => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/pretty-photo/js/jquery.prettyPhoto.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
		);
		$register_scripts = apply_filters( 'frontend/script/register', $register_scripts );
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all CWP sty;es.
	 */
	private static function register_styles() {
		$register_styles = array(
			'cwp-alert-ui'           => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cubewp-alerts.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'archive-cpt-styles'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cubewp-archive-cpt.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'author-style'     => array(
                'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cwp-author.css',
                'deps'    => array(),
                'version' => CUBEWP_VERSION,
                'has_rtl' => false,
      		),
			'cwp-map-cluster'        => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/map-cluster.css',
				'deps'    => array( 'cwp-leaflet-css' ),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cwp-leaflet-css'        => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/leaflet/leaflet.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'frontend-fields'        => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/frontend-fields.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'single-cpt-styles'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cubewp-single-cpt.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cwp-jquery-ui'         => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/jquery-ui/jquery-ui.css',
				'deps'    => array(),
				'version' => '1.12.1',
				'has_rtl' => true,
			),
			'cwp-timepicker'         => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/timepicker/jquery-ui-timepicker-addon.min.css',
				'deps'    => array(),
				'version' => '1.6.1',
				'has_rtl' => true,
			),
			'select2'                => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/select2/select2.min.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'media'   => 'all',
				'has_rtl' => false,
			),
			'cwp-taxonomy-shortcode' => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cubewp-taxonomy-shortcode.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cubewp-pretty-photo'  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/pretty-photo/css/prettyPhoto.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'media'   => 'all',
				'has_rtl' => false,
			),
			'cwp-post-cards'     => array(
				'src'     => CUBEWP_POST_CARDS_URL . '/cubewp-post-cards.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
		);
		$register_styles = apply_filters( 'frontend/style/register', $register_styles );
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		$assets = array(
            'cwp-styles'             => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/cubewp-styles.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
                'media'   => 'all',
				'has_rtl' => false,
			),
			'loop-style'     => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/css/loop.css',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'media'   => 'all',
				'has_rtl' => false,
			),
		);

		return apply_filters( 'cubewp_enqueue_styles', $assets );
	}

	/**
	 * Return data for script handles.
	 *
	 * @param string $handle Script handle the data will be attached to.
	 *
	 * @return array|bool
	 */
	public static function get_frontend_script_data( $handle ) {
		global $wp, $cwpOptions;

		switch ( $handle ) {
			case 'cwp-search-filters':
				$params = array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'admin_url' => admin_url(),
				);
				break;
			case 'cwp-alert-ui':
				$params = array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'admin_url' => admin_url(),
					'nonce' 	=> wp_create_nonce("cubewp-alert-nonce"),
				);
				break;
			case 'cubewp-map':
				$params = _get_map_settings();
				break;
			default:
				$params = false;
		}


		return apply_filters( 'get_frontend_script_data', $params, $handle );
	}

	private static function cubewp_enqueue_settings_css_js() {
		if ( cwp()->is_request( 'frontend' ) ) {
		   add_action( 'wp_print_styles', function(){
			  global $cwpOptions;
			  $cwpOptions = ! empty( $cwpOptions ) && is_array( $cwpOptions ) ? $cwpOptions : get_option( 'cwpOptions' );
			  $cubewp_css = isset( $cwpOptions['cubewp-css'] ) && ! empty( $cwpOptions['cubewp-css'] ) ? $cwpOptions['cubewp-css'] : '';
			  echo '<style type="text/css">
				 ' . $cubewp_css . '
				</style>';
		   } );
	
		   add_action( 'wp_footer', function(){
			  global $cwpOptions;
			  $cwpOptions = ! empty( $cwpOptions ) && is_array( $cwpOptions ) ? $cwpOptions : get_option( 'cwpOptions' );
			  $cubewp_js = isset( $cwpOptions['cubewp-js'] ) && ! empty( $cwpOptions['cubewp-js'] ) ? $cwpOptions['cubewp-js'] : '';
			  wp_enqueue_script( 'jquery' );
			  echo '<script type="text/javascript">
				 ' . $cubewp_js . '
				</script>';
		   } );
		}
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle, 'frontend' );
		}
	}
}