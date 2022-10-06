<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
class CubeWp_Add_On {

	// API route
	public static $route   = 'http://192.168.100.3/cube';

	// Cubewp CONST
	const CUBEWP   = 'cubewp';

	const ADDON   = 'addon';

	const ACTI   = 'acti';

	const VATION   = 'vation';

	// API Action
	public static $action   = 'edd_action';

	public function __construct() {
		//license system
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ), 9 );
		add_action( self::CUBEWP.'/'.self::ADDON.'/'.self::ACTI.self::VATION, array($this,'_plugins'), 9, 1 );
	}

	/**
	 * all Add ons
	 * @since 1.0
	 * @version 1.0
	 */
	public static function cubewp_add_ons() {

		return array(
			'cubewp-builder' => array(
				'slug' => 'cubewp-builder',
				'id'   => 9,
				'base' => 'cubewp-builder/cubewp-frontend.php',
				'path' => plugin_dir_path( dirname(dirname(__DIR__)) ).'cubewp-builder/cube/',
				'load' => CUBEWP.'_Frontend_Load',
			),
			'cubewp-payments' => array(
				'slug' => 'cubewp-payments',
				'id'   => 30,
				'base' => 'cubewp-payments/cubewp-payments.php',
				'path' => plugin_dir_path( dirname(dirname(__DIR__)) ).'cubewp-payments/cube/',
				'load' => CUBEWP.'Payments_Load',
			),
		);

	}

	/**
	 * _plugins
	 * @since 1.0
	 * @version 1.0
	 */
	public static function _plugins($plugin) {
		
		global $wpdb;

		$message = array();

		// WordPress check
		$wp_version = $GLOBALS['wp_version'];
;
		if ( version_compare( $wp_version, '5.8', '<' ) )
			$message[] = __( 'This CubeWP Add-on requires WordPress 4.0 or higher. Version detected:', 'cubewp-frontend' ) . ' ' . $wp_version;

		// PHP check
		$php_version = phpversion();
		if ( version_compare( $php_version, '5.3', '<' ) )
			$message[] = __( 'This CubeWP Add-on requires PHP 5.3 or higher. Version detected: ', 'cubewp-frontend' ) . ' ' . $php_version;

		// SQL check
		$sql_version = $wpdb->db_version();
		if ( version_compare( $sql_version, '5.0', '<' ) )
			$message[] = __( 'This CubeWP Add-on requires SQL 5.0 or higher. Version detected: ', 'cubewp-frontend' ) . ' ' . $sql_version;

		// Not empty $message means there are issues
		if ( ! empty( $message ) ) {

			$error_message = implode( "\n", $message );
			die( __( 'Sorry but your WordPress installation does not reach the minimum requirements for running this add-on. The following errors were given:', 'cubewp-frontend' ) . "\n" . $error_message );

		}

		return self::add_on_management($plugin);

	}

	/**
	 * add_on_management
	 * @since 1.0
	 * @version 1.0
	 */

	public static function add_on_management($plugin) {

		$add_ons = self::cubewp_add_ons();
		if(function_exists('CWP')){

			if(isset($add_ons[$plugin])){

				$path = $add_ons[$plugin]['path'];
				$id = $add_ons[$plugin]['id'];
				$file = $path . "config.txt";

				if(empty(CWP()->cubewp_options($id))){
					
					$lic_is_not_valid 	= utf8_encode("\x53\x6f\x72\x72\x79\x21\x20\x59\x6f\x75\x72\x20\x6c\x69\x63\x65\x6e\x73\x65\x20\x69\x73\x20\x6e\x6f\x74\x20\x76\x61\x6c\x69\x64\x2c\x20\x45\x72\x72\x6f\x72\x20\x63\x6f\x64\x65\x20\x69\x73\x3a");
					$file_is_not_valid 	= utf8_encode("\x53\x6f\x72\x72\x79\x21\x20\x54\x68\x69\x73\x20\x70\x6c\x75\x67\x69\x6e\x20\x66\x69\x6c\x65\x20\x69\x73\x20\x6e\x6f\x74\x20\x76\x61\x6c\x69\x64");
					$need_fresh_file 	= utf8_encode("\x53\x6f\x72\x72\x79\x21\x20\x54\x68\x69\x73\x20\x70\x6c\x75\x67\x69\x6e\x20\x66\x69\x6c\x65\x20\x68\x61\x73\x20\x61\x6c\x72\x65\x61\x64\x79\x20\x75\x73\x65\x64\x2c\x20\x50\x6c\x65\x61\x73\x65\x20\x64\x6f\x77\x6e\x6c\x6f\x61\x64\x20\x66\x72\x65\x73\x68\x20\x66\x69\x6c\x65\x20\x66\x6f\x72\x20\x66\x72\x65\x73\x68\x20\x69\x6e\x73\x74\x61\x6c\x6c\x61\x74\x69\x6f\x6e\x2e");
					$not_our_plugin 	= utf8_encode("\x53\x6f\x72\x72\x79\x21\x20\x54\x68\x69\x73\x20\x69\x73\x20\x6e\x6f\x74\x20\x22\x43\x75\x62\x65\x57\x50\x22\x20\x70\x6c\x75\x67\x69\x6e");
					if ( file_exists ( $file ) ) {

						$key = file_get_contents ( $file );
						$response = self::cubewp_check_licence( $key, $id, 'activate_license' );

						if(is_object($response) && $response->success != true){
							$message = isset($response->error) ? $response->error : '';
							//Lic not good
							die( $lic_is_not_valid.' '.$message );

						}elseif($response == 'no-response'){
							//file not good
							die( $file_is_not_valid );

						}else{
							$message = isset($response->license) ? $response->license : '';
							
							if($message != 'valid'){
								//Lic not good
								die( $lic_is_not_valid.' '.$message );
							}

							CWP()->update_cubewp_options($response->item_id, $response);
							CWP()->update_cubewp_options($response->item_id.'_key', $key);

						}
						unlink ( $license_file );

					}else{
						//file not good
						die( $need_fresh_file );
					}
				}

			}else{
				//Plugin not good
				die( $not_our_plugin );
			}
		}
		
	}


	/**
	 * cubewp_check_licence
	 * @since 1.0
	 * @version 1.0
	 */

	private static function cubewp_check_licence($license = '', $id = 0, $action_type = ''){

		$siteURL = get_bloginfo( 'url' );

		$response = self::$route."/?" .self::$action. "={$action_type}&item_id=" .$id. "&license={$license}&url={$siteURL}";

		$response = wp_remote_get($response );

		if(!is_wp_error($response)){
			
			$response_data = json_decode($response['body']);

			if(is_object($response_data)){

				return $response_data;

			}
		}

		return 'no-response';

	}
	/**
	 * Plugin Update Check
	 * @since 1.0
	 * @version 1.1
	 */
	public function check_for_plugin_update( $checked_data ) {

		global $wp_version;
			
		if ( empty( $checked_data->checked ) )
			return;


		$add_ons = self::cubewp_add_ons();
		
		if(function_exists('CWP')){

			foreach($add_ons as $key => $add_on){
				
				$id = $add_on['id'];
				$slug = $add_on['slug'];
				$base = $add_on['base'];
				$key = CWP()->cubewp_options($id.'_key');

				$response = self::cubewp_check_licence( $key, $id, 'get_version' );

				if(is_object($response) && $response->slug == $slug){
					
					if ( is_object( $response ) && ! empty( $response ) ){
						$response = (object) array(
							'id' => 'cubewp.com/plugins/'.$response->slug,
							'slug' => $response->slug,
							'plugin' => $base,
							'new_version' => $response->new_version,
							'url' => $response->url,
							'package' => $response->package,
							'icons' => Array(),
							'banners' => Array(),
							'banners_rtl' => Array(),
							'requires' => '',
							'tested' => '6.0.2',
							'requires_php' => '7.0',
						);
						
						
						$checked_data->response[ $base ] = $response;

					}

				}
			}

		}

		return $checked_data;

	}

    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}