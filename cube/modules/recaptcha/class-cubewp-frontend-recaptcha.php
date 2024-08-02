<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * CubeWp_Frontend_Recaptcha
 */
class CubeWp_Frontend_Recaptcha {

	private static $recaptcha = false;
	private static $recaptcha_type = '';
	private static $site_key = '';
	private static $secret_key = '';

	public function __construct() {
		global $cwpOptions;
		if(isset($cwpOptions['recaptcha']) && $cwpOptions['recaptcha'] == '1') {
			self::$recaptcha = true;
			self::$recaptcha_type = isset($cwpOptions['recaptcha_type']) && !empty($cwpOptions['recaptcha_type']) ? $cwpOptions['recaptcha_type'] : '';
			self::$site_key = isset($cwpOptions['google_recaptcha_sitekey']) && !empty($cwpOptions['google_recaptcha_sitekey']) ? $cwpOptions['google_recaptcha_sitekey'] : '';
			self::$secret_key = isset($cwpOptions['google_recaptcha_secretkey']) && !empty($cwpOptions['google_recaptcha_secretkey']) ? $cwpOptions['google_recaptcha_secretkey'] : '';

			add_filter( 'frontend/script/register', array( $this, 'cubewp_register_captcha_scripts' ) );
			add_filter( 'get_frontend_script_data', array( $this, 'cubewp_localize_captcha_scripts' ), 10, 2 );
			add_filter( 'frontend/script/enqueue', array( $this, 'cubewp_enqueue_captcha_scripts' ) );
		}
	}

	public function cubewp_register_captcha_scripts($scripts) {
		$register_scripts = array();
		if (self::$recaptcha_type == 'google_v2') {
			$register_scripts['cubewp-google-recaptcha-v2'] = array(
				'src'     => 'https://www.google.com/recaptcha/api.js?onload=cubewpCaptchaLoaded&render=explicit',
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
			);
		}
		$register_scripts['cubewp-recaptcha'] = array(
			'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/cubewp-recaptcha.js',
			'deps'    => array("jquery"),
			'version' => CUBEWP_VERSION,
		);

		return array_merge( $register_scripts, $scripts );
	}

	public function cubewp_localize_captcha_scripts ( $data, $handle ) {
		if ( $handle == 'cubewp-recaptcha' ) {
			return array(
				'recaptcha_type' => self::$recaptcha_type,
				'site_key' => self::$site_key,
			);
		}

		return $data;
	}

	public function cubewp_enqueue_captcha_scripts( $data ) {
		if (self::$recaptcha_type == 'google_v2') {
			CubeWp_Enqueue::enqueue_script( 'cubewp-google-recaptcha-v2' );
			CubeWp_Enqueue::enqueue_script( 'cubewp-recaptcha' );
		}
	}

	public static function cubewp_captcha_form_attributes($form_option = "enabled") {
		if ( ! self::$recaptcha || $form_option != 'enabled') {
			return '';
		}
		$output = '';
		if (self::$recaptcha_type == 'google_v2') {
			$output .= '<div class="cubewp-form-recaptcha" id="cubewp-form-recaptcha-' . rand( 000000000, 999999999 ) . '"></div>';
		}
		return $output;
	}

	public static function cubewp_captcha_verification($verification_of, $response) {
		if (empty($response)) {
			wp_send_json(
				array(
					'type' => 'error',
					'msg'  => esc_html__('Error! Please Check Captcha.', 'cubewp-frontend'),
				)
			);
		}
		$verify_captcha = CubeWp_Frontend_Recaptcha::cubewp_verify_captcha_response($response);
		if ( ! $verify_captcha) {
			wp_send_json(
				array(
					'type' => 'error',
					'msg'  => esc_html__('Captcha Error! Please Disable VPN Or Try Again Later.', 'cubewp-frontend'),
				)
			);
		}
	}

	public static function cubewp_verify_captcha_response($response) {
		if ( ! self::$recaptcha) {
			return true;
		}
		if (self::$recaptcha_type == 'google_v2') {
			$url          = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode( self::$secret_key ) . '&response=' . urlencode( $response );
			$response     = file_get_contents( $url );
			$responseKeys = json_decode( $response, true );
			if ( $responseKeys["success"] ) {
				return true;
			}
		}

		return false;
	}

	public static function init() {
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}