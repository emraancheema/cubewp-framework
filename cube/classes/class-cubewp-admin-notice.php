<?php
/**
 * CubeWp Admin Notice.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */
defined( 'ABSPATH' ) || exit;

/**
 * CubeWp Class for Admin notices.
 *
 * @class CubeWp_Admin_Notice
 */
class CubeWp_Admin_Notice {
	public $notice_name = '';
	public $message = '';
	public $status = '';
	public $dismissible = true;

	public function __construct( $notice_name = '', $message = '', $status = 'success', $dismissible = true ) {
		if ( ! empty( $message ) && ! empty( $notice_name ) ) {
			$proceed = true;
			if ( $dismissible && isset( $_COOKIE[ 'cubewp-notice-' . $notice_name ] ) ) {
				$proceed = false;
			}
			if ( $proceed ) {
				$this->notice_name = $notice_name;
				$this->message     = $message;
				$this->status      = $status;
				$this->dismissible = $dismissible;
				if ( $this->dismissible ) {
					add_action( 'admin_print_footer_scripts', array( $this, 'cubewp_admin_notice_script_event' ), 10 );
				}
				add_action( 'admin_notices', array( $this, 'cubewp_build_admin_notices_ui' ), 10 );
			}
		}
	}

	public function cubewp_admin_notice_script_event() {
		?>
        <script>
            jQuery(document).on("click", ".cubewp-notice .notice-dismiss", function () {
                var $this = jQuery(this),
                    parent = $this.closest('.cubewp-notice'),
                    notice = parent.attr("data-notice"),
                    cookie_duration = 30,
                    d = new Date(),
                    expires;

                d.setTime(d.getTime() + (cookie_duration * 24 * 60 * 60 * 1000));
                expires = "expires=" + d.toUTCString();
                document.cookie = notice + "=" + notice + ";" + expires + ";path=/";
				location.reload();
            });
        </script>
		<?php
	}

	public function cubewp_load_default_notices() {
		add_action( 'admin_print_footer_scripts', array( $this, 'cubewp_admin_notice_script_event' ), 10 );
		add_action( 'admin_notices', array( $this, 'cubewp_admin_notices' ), 10 );
	}

	/**
	 * Method cubewp_build_admin_notices_ui
	 *
	 * @since  1.0.0
	 */
	public function cubewp_build_admin_notices_ui() {
		$notice_classes = 'notice cubewp-notice';
		$notice_classes .= ' notice-' . $this->status;
		if ( $this->dismissible ) {
			$notice_classes .= ' is-dismissible';
		}
		$notice_ui = '<div class="' . esc_attr( $notice_classes ) . '" data-notice="cubewp-notice-' . esc_attr( $this->notice_name ) . '">';
		$notice_ui .= '<p>' . cubewp_core_data( $this->message ) . '</p>';
		$notice_ui .= '</div>';

		print( $notice_ui );
	}

	/**
	 * Method cubewp_admin_notices
	 *
	 * Admin notice printing if any of requirement not met.
	 *
	 * @since  1.0.0
	 */
	public function cubewp_admin_notices() {
		$notice_ui     = '';
		$version_check = self::cubewp_check_versions();
		if ( true !== $version_check && is_array( $version_check ) ) {
			foreach ( $version_check as $message ) {
				$notice_ui .= '<div class="notice notice-error">';
				$notice_ui .= '<p>' . cubewp_core_data( $message ) . '</p>';
				$notice_ui .= '</div>';
			}
		}
		if ( CWP()->is_admin_screen( 'cubewp' ) ) {
			$notice_ui .= '<div class="cwp-welcome-page-title-top-border clearfix"></div>
			
			<div class="cwp-welcome-page-title clearfix">';
			$notice_ui .= '
			<div class="cwp-welcome-page-section"><div class="flot-left cwp-logo">
				<a href="https://cubewp.com" target="_blank"><img src="' . CWP_PLUGIN_URI . 'cube/assets/admin/images/CubeWP-light-logo.png" alt="image" /></a>
			</div>
			<div class="cwp-title-menu flot-left">
				<ul>
					<li><a href="'.admin_url( 'admin.php?page=cubewp-settings' ).'" target="_blank"><span class="dashicons dashicons-admin-settings"></span>Settings</a></li>
				</ul> 
			</div>
			<div class="float-right cwp-update-plugin-btn">
				<a target="_blank" href="https://cubewp.com/store"><span class="dashicons dashicons-lock"></span>' . esc_attr__( 'Unlock All Premium Features', 'cubewp-framework' ) . '</a>
			</div>
			<div class="cwp-title-menu float-right">
				<ul>
					<li><a href="https://support.cubewp.com" target="_blank"><span class="dashicons dashicons-media-document"></span>Docs</a></li>
					<li><a href="https://support.cubewp.com/forums" target="_blank"><span class="dashicons dashicons-buddicons-community"></span>Community</a></li>
					<li><a href="https://support.cubewp.com/forums/forum/feedback" target="_blank"><span class="dashicons dashicons-feedback"></span>Feedback</a></li>
					<li><a href="https://help.cubewp.com/" target="_blank"><span class="dashicons dashicons-sos"></span>Helpdesk</a></li>
				</ul> 
			</div>	
			</div>	
			<div class="clearfix"></div>';
			$notice_ui .= '</div>';
			if ( current_cubewp_page() == 'cubewp_post_types' || current_cubewp_page() == 'cubewp_taxonomies'  || current_cubewp_page() == 'custom_fields'  ) {
				if ( ! isset($_COOKIE['cubewp-notice-' . current_cubewp_page() . '-info']) ) {
					$href = '';
                    $message = '';
                    if ( current_cubewp_page() == "cubewp_post_types" ) {
                        $href    = 'https://youtu.be/4z1wF5nBaek';
                        $message = esc_html__( 'Learn what are Custom Post Types.', 'cubewp-framework' );
                    } else if ( current_cubewp_page() == "cubewp_taxonomies" ) {
                        $href    = 'https://youtu.be/ibvrIkhGIyo';
                        $message = esc_html__( 'Learn what are Taxonomies.', 'cubewp-framework' );
                    }else if ( current_cubewp_page() == "custom_fields" ) {
                        $href    = 'https://youtu.be/zKDLb2o_cdA';
                        $message = esc_html__( 'Learn what are Custom Fields.', 'cubewp-framework' );
                    }
                    $videoText = esc_html__( 'Watch', 'cubewp-framework' );
                    $notice_ui .= '<div class="notice notice-info cwp-notic-video is-dismissible cubewp-notice" data-notice="cubewp-notice-' . esc_attr( current_cubewp_page() ) . '-info">';
                    $notice_ui .= '<p>';
                    $notice_ui .= '<span class="dashicons dashicons-editor-help" style="margin: 1px 5px 0 0"></span>' . $message . '<a class="cwp-watch-video-btn" target="_blank" href="' . $href . '"><span class="dashicons dashicons-youtube" style="margin: 1px 5px 0 0;"></span>' . $videoText . '</a>';
                    $notice_ui .= '</p>';
                    $notice_ui .= '</div>';
				}
			}
		}
		if ( empty( get_option( 'cubewp_framework_installed_on' ) ) ) {
			update_option( 'cubewp_framework_installed_on', strtotime( "NOW" ) );
		}
		$framework_installed_on = get_option( 'cubewp_framework_installed_on' );
		$max_age = 15 * 24 * 60 * 60;
		if (time() - $framework_installed_on > $max_age) {
			$permanently_removed_notices = get_option( 'permanently_removed_notices' );
			$permanently_removed_notices = ! empty( $permanently_removed_notices ) && is_array( $permanently_removed_notices ) ? $permanently_removed_notices : array();
			if ( ! isset( $_COOKIE['cubewp-notice-rating'] ) && ! in_array( 'cubewp-notice-rating', $permanently_removed_notices ) ) {
				$current_url = cubewp_get_current_url();
				$current_url = add_query_arg( array(
					'cubewp-remove-notice-permanently' => 'cubewp-notice-rating'
				), $current_url );
				$notice_ui .= '<div class="notice notice-info is-dismissible cubewp-notice" data-notice="cubewp-notice-rating">';
				$notice_ui .= '<p>';
				$notice_ui .= esc_html__( 'Hey, I noticed you\' have been using CubeWP Framework for a few weeks! Could you do me a favor and give the plugin 5-Star rating on WordPress?', 'cubewp-framework' );
				$notice_ui .= '<br />';
				$notice_ui .= '<a href="https://wordpress.org/support/plugin/cubewp-framework/reviews/#new-post" target="_blank">' . esc_html__( 'Yes, Take me there', 'cubewp-framework' ) . '</a>';
				$notice_ui .= '<br />';
				$notice_ui .= '<a href="' . esc_url( $current_url ) . '" class="cubewp-remove-notice-permanently">' . esc_html__( 'Already did it', 'cubewp-framework' ) . '</a>';
				$notice_ui .= '</p>';
				$notice_ui .= '<p>';
				$notice_ui .= '<a href="https://profiles.wordpress.org/cubewp1211/" target="_blank"><strong>' . esc_html__( '~ Emraan Cheema, CubeWP Framework co-founder', 'cubewp-framework' ) . '</strong></a>';
				$notice_ui .= '</p>';
				$notice_ui .= '<a class="notice-dismiss" style="display: flex;justify-content: center;align-items: center;">' . esc_html__( 'Maybe Later.', 'cubewp-framework' ) . '</a>';
				$notice_ui .= '</div>';
			}
		}
        self::cubewp_remove_notices_permanently();

		echo wp_kses_post( $notice_ui );
	}

	/**
	 * Method cubewp_remove_notices_permanently
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	private static function cubewp_remove_notices_permanently() {
		if ( isset( $_GET['cubewp-remove-notice-permanently'] ) && ! empty( $_GET['cubewp-remove-notice-permanently'] ) ) {
		   $permanently_removed_notices = get_option( 'permanently_removed_notices' );
		   $permanently_removed_notices = ! empty( $permanently_removed_notices ) && is_array( $permanently_removed_notices ) ? $permanently_removed_notices : array();
		   $permanently_removed_notices[] = sanitize_text_field( $_GET['cubewp-remove-notice-permanently'] );
		   update_option( 'permanently_removed_notices', $permanently_removed_notices );
		   $current_url = cubewp_get_current_url();
		   $current_url = remove_query_arg( 'cubewp-remove-notice-permanently', $current_url );
		   wp_redirect( esc_url( $current_url ) );
		   exit;
		}
	}

	/**
	 * Method cubewp_check_versions
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	private static function cubewp_check_versions() {
		/**
		 * Requirements are in array, 1: WordPress version 2: Php Version.
		 */
		$required_versions = [
			'wordpress' => [
				'version' => CubeWp_Load::$wp_req_version,
				'i18n'    => [
					'requirements' => sprintf( __( 'CubeWP requires WordPress version %1$s or higher. You are using version %2$s. Please upgrade WordPress to use CubeWP.', 'cubewp-framework' ), CubeWp_Load::$wp_req_version, $GLOBALS['wp_version'] ),
				],
			],
			'php'       => [
				'version' => CubeWp_Load::$php_req_version,
				'i18n'    => [
					'requirements' => sprintf( __( 'CubeWP requires PHP version %1$s or higher. You are using version %2$s. Please <a href="%3$s">upgrade PHP</a> to use CubeWP.', 'cubewp-framework' ), CubeWp_Load::$php_req_version, PHP_VERSION, 'https://wordpress.org/support/upgrade-php/' ),
				],
			],
		];
		$versions_met      = true;
		$messages          = array();
		if ( version_compare( $required_versions['wordpress']['version'], $GLOBALS['wp_version'], '>' ) ) {
			$versions_met = false;
			$messages[]   = $required_versions['wordpress']['i18n']['requirements'];
		}
		if ( version_compare( $required_versions['php']['version'], PHP_VERSION, '>' ) ) {
			$versions_met = false;
			$messages[]   = $required_versions['php']['i18n']['requirements'];
		}
		if ( $versions_met ) {
			return $versions_met;
		}

		return $messages;
	}
}