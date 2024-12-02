<?php
/**
 * CubeWp Admin Enqueue.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * CubeWp_Admin_Enqueue
 */
class CubeWp_Admin_Enqueue{
    
    /**
	 * Contains an array of script handles registered by CWP.
	 *
	 * @var array
	 */
	public static $scripts = array();

	/**
	 * Contains an array of style handles registered by CWP.
	 *
	 * @var array
	 */
	public static $styles = array();

	/**
	 * Contains an array of script handles localized by CWP.
	 *
	 * @var array
	 */
	public static $wp_localize_scripts = array();
    
    
	/**
	 * Method get_registered_types_types
	 *
	 * @return void
	 * * @since  1.0.0
	 */
	private static function get_registered_types_types() {
		$core                  = get_post_types( [ '_builtin' => true ] );
        $public                = get_post_types( [ '_builtin' => false, 'public' => true ] );
        $private               = get_post_types( [ '_builtin' => false, 'public' => false ] );
        return array_merge( $core, $public, $private );
	}

	private static function get_all_cwp_group_fields_values() {
		$post_types = ['cwp_form_fields', 'cwp_settings_fields', 'cwp_user_fields'];
		$group_fields_values = [];
	
		foreach ($post_types as $post_type) {
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);
	
			$query = new WP_Query($args);
	
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
	
					// Get the _cwp_group_fields metabox value
					$group_fields_value = get_post_meta(get_the_ID(), '_cwp_group_fields', true);
	
					if (!empty($group_fields_value)) {
						$group_fields_values[] = $group_fields_value;
					}
				}
			}
	
			wp_reset_postdata();
			wp_reset_query();
		}
	
		return $group_fields_values;
	}

	/**
	 * Method get_cf_types_types
	 *
	 * @return void
	 * * @since  1.0.0
	 */
	private static function get_cf_types_types() {
		$default = array('' => 'Select Post Type');
        $post_types = get_post_types( [ '_builtin' => false, 'public' => true, 'show_in_menu' => true ] );
        return array_merge($default,$post_types);
	}


    /**
     * Method get_registered_taxonomies
     *
     * @return void
	 * * @since  1.0.0
     */
    private static function get_registered_taxonomies() {
		$core                  = get_taxonomies( [ '_builtin' => true ] );
        $public                = get_taxonomies( [ '_builtin' => false, 'public' => true ] );
        $private               = get_taxonomies( [ '_builtin' => false, 'public' => false ] );
        return array_merge( $core, $public, $private, array("categories" => "categories") );
	}
    
	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @param  string   $handle    Name of the script. Should be unique.
	 * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps      An array of registered script handles this script depends on.
	 * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	public static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = CUBEWP_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @param  string   $handle    Name of the script. Should be unique.
	 * @param  string   $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps      An array of registered script handles this script depends on.
	 * @param  string   $version   String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	public static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = CUBEWP_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @param  string   $handle  Name of the stylesheet. Should be unique.
	 * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	public static function register_style( $handle, $path, $deps = array(), $version = CUBEWP_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @param  string   $handle  Name of the stylesheet. Should be unique.
	 * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	public static function enqueue_style( $handle, $path = '', $deps = array(), $version = CUBEWP_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register all CWP scripts.
	 */
	private static function register_scripts() {
		$register_scripts = array(
            'cwp_vars'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cubewp-admin.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-metaboxes'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cubewp-metaboxes.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-block'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/blocks.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'google_map_api'      => array(
				'src'     => 'https://maps.googleapis.com/maps/api/js?key='. cwp_google_api_key() .'&libraries=places',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-google-address-field'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/google-admin-address-field.js',
				'deps'    => array( 'google_map_api' ),
				'version' => CUBEWP_VERSION,
			),
			'cubewp-business-hour'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/frontend/js/business-hour-field.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-term-meta'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cwp-term-meta.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-custom-fields'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/custom-fields.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-metaboxes-validation'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cubewp-metaboxes-validation.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cwp-timepicker'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/timepicker/jquery-ui-timepicker-addon.min.js',
				'deps'    => array( 'jquery-ui-datepicker' ),
				'version' => CUBEWP_VERSION,
			),
            'select2'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/select2/select2.full.min.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			), 
			'cwp-form-builder'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cwp-form-builder.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            
            // JS for cubewp Settings
            'ace-editor'      => array(
				'src'     => '//' . 'cdnjs' . '.cloudflare' . '.com/ajax/libs/ace/1.4.2/ace.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cubewp-settings'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cwp-options.js',
				'deps'    => array( 'jquery' ),
				'version' => CUBEWP_VERSION,
			),
            'cwp-options-required'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/cwp-options-required.js',
				'deps'    => array( 'cubewp-settings' ),
				'version' => CUBEWP_VERSION,
			),
            'serializeForm'      => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/js/jquery.serializeForm.js',
				'deps'    => array( 'cubewp-settings' ),
				'version' => CUBEWP_VERSION,
			)
            
		);
        $register_scripts = apply_filters( 'admin/script/register', $register_scripts);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all CWP sty;es.
	 */
	private static function register_styles() {
		$register_styles = array(
			'cwp-form-builder'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cwpform-builder.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
            'cubewp-admin'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cubewp-admin.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
            'cubewp-metaboxes'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cubewp-metaboxes.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
            'cubewp-custom-fields'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/custom-fields.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cubewp-welcome'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cwp-welcome.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
				'has_rtl' => false,
			),
			'cubewp-template-library'                => array(
                'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cwp-template-library.css' ,
                'deps'    => array(),
                'version' => CUBEWP_VERSION,
                'has_rtl' => false,
            ),
            'cwp-timepicker'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/timepicker/jquery-ui-timepicker-addon.min.css' ,
				'deps'    => array(),
				'version' => '1.6.1',
				'has_rtl' => true,
			),
            'select2'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/select2/select2.min.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
                'media'   => 'all',
				'has_rtl' => false,
			),
            'cubewp-datepicker'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/lib/datepicker/jquery-ui.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
                'media'   => 'all',
				'has_rtl' => false,
			),
            
		);
        $register_styles = apply_filters( 'admin/style/register', $register_styles);
		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}
    
    /**
	 * Register all CWP settings sty;es.
	 */
	private static function get_settings_style() {
		$register_styles = array(			
            'cwp-options-css'                  => array(
				'src'     => CWP_PLUGIN_URI . 'cube/assets/admin/css/cwp-options.css' ,
				'deps'    => array(),
				'version' => CUBEWP_VERSION,
                'media'   => 'all',
				'has_rtl' => false,
			),
            
		);
		return $register_styles;
	}
    
   
    /**
	 * Register/queue Admin scripts.
	 */
	public static function load_admin_scripts() {
		global $post,$pagenow, $cwpOptions;

		self::register_scripts();
		self::register_styles();
        
        self::enqueue_script( 'jquery-ui-sortable' );

		if(CWP()->is_admin_screen('cubewp') || $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'edit.php' || $pagenow == 'user-new.php' || $pagenow == 'user-edit.php' || $pagenow == 'profile.php')
       {
		self::enqueue_script( 'cwp_vars' );
        }
		self::enqueue_style( 'cubewp-admin' );

		if ( CWP()->is_admin_screen('cubewp_loop_builder') ) {
			self::enqueue_script( 'ace-editor' );
		}

       if(CWP()->is_admin_screen('cubewp_admin_search_filters') || 
	   	  CWP()->is_admin_screen('cubewp_loop_builder')||
          CWP()->is_admin_screen('cubewp_admin_search_fields'))
       {
            self::enqueue_style( 'cwp-form-builder' );
            self::enqueue_script( 'cwp-form-builder' );
            
        }
		

        if(CWP()->is_admin_screen('custom_fields') || CWP()->is_admin_screen('user_custom_fields') || CWP()->is_admin_screen('settings_custom_fields') || CWP()->is_admin_screen('taxonomy_custom_fields')){
            self::enqueue_script('cubewp-custom-fields');
            self::enqueue_style('cubewp-custom-fields');
            self::enqueue_script('cubewp-metaboxes-validation');
        }

        if(CWP()->is_admin_screen('cubewp_post_types') || 
			CWP()->is_admin_screen('cubewp_taxonomies') ||
			CWP()->is_admin_screen('cubewp_settings') ||
			$pagenow == 'user-new.php' || $pagenow == 'user-edit.php' || $pagenow == 'profile.php' ||
			$pagenow == 'post.php' || $pagenow == 'post-new.php'
		)
       {
			wp_enqueue_media();
			self::enqueue_style('cubewp-custom-fields');
			self::enqueue_style('cubewp-metaboxes');
			self::enqueue_script('cubewp-metaboxes-validation');
			self::enqueue_script('cubewp-metaboxes');
            
        }
		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php') {
			// Enqueue the necessary scripts for the REST API
			wp_enqueue_script('wp-api');
			wp_enqueue_script('wp-api-fetch');
			//self::enqueue_script( 'cubewp-block' );
		}
        
        if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'user-new.php' || $pagenow == 'user-edit.php' || $pagenow == 'profile.php' ) {
			self::enqueue_script( 'jquery-ui-datepicker' );
			self::enqueue_script( 'cwp-timepicker' );
			self::enqueue_style( 'cwp-timepicker' );
			self::enqueue_style('select2');
			self::enqueue_script('select2');
		}
        
        if ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' ) {
            self::enqueue_style('cubewp-custom-fields');
            self::enqueue_style( 'wp-color-picker' );
			self::enqueue_style('cubewp-metaboxes');
            self::enqueue_script( 'wp-color-picker' );
            self::enqueue_script('cubewp-term-meta');
            wp_enqueue_media();
        }
        
        if(CWP()->is_admin_screen('cubewp_settings'))
        {
            wp_enqueue_media();
            self::enqueue_style('select2');
            $enqueue_styles = self::get_settings_style();
            if ( $enqueue_styles ) {
                foreach ( $enqueue_styles as $handle => $args ) {
                    self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
                }
            }
            self::enqueue_script( 'wp-color-picker' );            
            self::enqueue_script('select2');
            self::enqueue_script('ace-editor');
            self::enqueue_script('cubewp-settings');
            self::enqueue_script('cwp-options-required');
            self::enqueue_script('serializeForm');
            
        }
		
        
		
        
        
        echo apply_filters( 'admin/script/enqueue', '');
				
	}
    
	/**
	 * Localize a CWP script once.
	 *
	 * @since 1.0.0 
	 * @param string $handle Script handle the data will be attached to.
	 */
	public static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
            if(CWP()->is_request('admin')){
                $data = self::get_admin_script_data( $handle);
            }elseif(CWP()->is_request('frontend')){
                $data = CubeWp_Enqueue::get_frontend_script_data( $handle);
            }

			if ( ! $data ) {
				return;
			}

			$name  = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters($name,$data) );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 * @return array|bool
	 */
	public static function get_admin_script_data( $handle) {
		global $wp;

        switch ( $handle ) {
			case 'cwp_vars':
				$confirm_text = array();
                if (CWP()->is_admin_screen("cubewp_post_types")) {
                   $confirm_text['single'] = esc_html__("Deletion of this post type cannot be undone.", "cubewp-framework");
                   $confirm_text['multiple'] = esc_html__("Deletion of these post type cannot be undone.", "cubewp-framework");
                }elseif (CWP()->is_admin_screen("cubewp_taxonomies")) {
                   $confirm_text['single'] = esc_html__("Deletion of this taxonomy cannot be undone.", "cubewp-framework");
                   $confirm_text['multiple'] = esc_html__("Deletion of these taxonomies cannot be undone.", "cubewp-framework");
                }elseif (CWP()->is_admin_screen("custom_fields") || CWP()->is_admin_screen("taxonomy_custom_fields") || CWP()->is_admin_screen("user_custom_fields")) {
                   $confirm_text['single'] = esc_html__("Deletion of this custom field group cannot be undone.", "cubewp-framework");
                   $confirm_text['multiple'] = esc_html__("Deletion of these custom field group cannot be undone.", "cubewp-framework");
                }else {
					$confirm_text['single'] = esc_html__("Deletion of this record cannot be undone.", "cubewp-framework");
					$confirm_text['multiple'] = esc_html__("Deletion of these records cannot be undone.", "cubewp-framework");
				}
                $params = array(
                   'ajax_url'     => admin_url('admin-ajax.php'),
                   'admin_url'    => admin_url(),
                   'nonce'        => wp_create_nonce("cubewp-admin-nonce"),
				   'nonce_option' => wp_create_nonce("cubewp_dynamic_options"),
                   'confirm_text' => $confirm_text
                );
                break;
            case 'cubewp-metaboxes-validation':
				$params = array(
                    'name_exist_msg'   =>   esc_html__( 'This Name already exists, Please choose something different', 'cubewp-framework' ),
                    'num_value_msg'   =>   esc_html__( 'Only Numeric values are not allowed.', 'cubewp-framework' ),
					'existing_post_types'    =>   self::get_registered_types_types(),
                    'existing_taxonomies'    =>   self::get_registered_taxonomies(),
                );
				if(CWP()->is_admin_screen('custom_fields') || CWP()->is_admin_screen('user_custom_fields') || CWP()->is_admin_screen('settings_custom_fields'))
				{
					$params['existing_custom_fields'] = self::get_all_cwp_group_fields_values();
				}
				break;
			case 'cubewp-metaboxes':
				$params = array(
					'confirm_remove_relation' =>   esc_html__("Are you sure? You want to remove this relation.", "cubewp-framework"),
					'remove_relation_nonce'   =>   wp_create_nonce("cubewp_remove_nonce")
							);
				break;
			case 'cubewp-block':
				$params = array(
					'cf_post_types'    		 =>   self::get_cf_types_types(),
					'cf_user_roles'    		 =>   cwp_get_user_roles_name(),
				);
				break;
            case 'cubewp-custom-fields':
                $params = array(
                    'url'   => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( "cubewp_custom_fields_nonce" ),
                );
                break;
            case 'cubewp-settings':
                $params = array(
                    'ajax_url'         => admin_url( 'admin-ajax.php' ),
                    'admin_url'        => admin_url(),
                );
                break;
			default:
				$params = false;
		}

		return apply_filters( 'cubewp_get_admin_script', $params, $handle);
	}
    

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_admin_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle);
		}
	}
}