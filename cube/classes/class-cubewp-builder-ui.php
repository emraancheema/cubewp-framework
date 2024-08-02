<?php
/**
 * Builder UI trait is contains all markup for cubeWP builder.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 * @since  1.0.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait CubeWp_Builder_Ui {

	public static $tab_options = array();
	
	/**
	 * Method CubeWp_Form_Builder
	 *
	 * @param array $data form data for builder
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function CubeWp_Form_Builder(array $data) {
		if (empty($data['form_type'])) {
			return '';
		}
		$form_type  = $data['form_type'];
		$data      = self::builder_fields_parameters($data);
		
		self::CubeWp_build_tab_options($data);
        $builder_ui = '<div class="cubewp-content">';
		$builder_ui .= self::builder_header($data['page_title']);
		$builder_ui .= '<section id="cwpform-builder" class="cwpform-builder cubewp-builder-' . $form_type . '">';
		$builder_ui .= '<div class="cubewp-builder-sidebar">';
		$builder_ui .= self::CubeWp_build_post_type_switcher($data);
		$builder_ui .= self::CubeWp_build_content_switcher($data);
		$builder_ui .= '<div class="cubewp-builder-sidebar-groups-widgets">';
        $builder_ui .= self::cubewp_builder_widgets_ui($form_type);
		$builder_ui .= '</div>';
		$builder_ui .= '</div>';
		$builder_ui .= '<div class="cubewp-builder-container">';
		$builder_ui .= '<div class="cubewp-builder">';
		if ($data['form_type'] == 'single_layout') {
			$builder_ui .= self::builder_single_layout($data);
		} else {
			$builder_ui .= self::cubewp_builder_area($data);
		}
		$builder_ui .= '</div>';
		$builder_ui .= '</div>';
		$builder_ui .= '</section>';
		$builder_ui .= '</div>';

		return $builder_ui;
	}
	
	/**
	 * Method cubewp_builder_widgets_ui
	 *
	 * @param string $form_type
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function cubewp_builder_widgets_ui(string $form_type) {
		$output = '';
		$options    = self::$tab_options;
		if (isset($options) && count($options) != 0) {
			foreach ($options as $slug => $option) {
				$switcher = $option["switcher"];
				$nested_Switcher = array();
				if (!empty($switcher) && isset($switcher['options'])) {
					foreach ($switcher['options'] as $id => $value) {
						$nested_Switcher[] = $id;
					}
				}
				$output .= '<div class="cubewp-builder-widgets sidebar-type-'.$slug.' cubewp-tab-switcher-target cubewp-switcher-tab-' . $slug . '" data-form-type="' . $form_type . '" data-slug="' . $slug . '" data-child-switcher="' . implode(',',$nested_Switcher) . '">';
					//$output .= self::cubewp_builder_widgets_display('',$form_type,$slug);
				$output .= '</div>';
			}
		}
        return $output;
    }

	/**
	 * Method cubewp_builder_widgets_display
	 *
	 * @param string $form_type
	 * @param string $slug
	 *
	 * @return string
	 * @since  1.1.10
	 */
	public static function cubewp_builder_widgets_display(string $switcher, string $form_type, string $slug) {
		$output = '';
		if (!empty($switcher)) {
			$switcher = explode(',',$switcher);
			foreach ($switcher as $id) {
				$output .= '<div id="plan-' . $id . '" class="sidebar-plan-tab cubewp-tab-switcher-target cubewp-switcher-tab-' . $id . '" data-id="'.$id.'">';
					$output .= self::cubewp_builder_widgets($form_type, $slug);
				$output .= '</div>';
			}
		}
		else {
			$output .= self::cubewp_builder_widgets($form_type, $slug);
		}
		return $output;
    }
	
	/**
	 * Method cubewp_builder_widgets
	 *
	 * @param string $form_type
	 * @param string $slug
	 *
	 * @return html
	 * @since  1.0.0
	 */
	public static function cubewp_builder_widgets(string $form_type, string $slug) {
		$widgets_ui = '';
		$widgets_ui .= apply_filters("cubewp/builder/{$form_type}/default/fields", '', $slug);
		$widgets_ui .= apply_filters("cubewp/builder/{$form_type}/taxonomies/fields", '', $slug);
		if (class_exists("CubeWp_Frontend_Load")) {
			$widgets_ui .= apply_filters("cubewp/builder/{$form_type}/group/fields", '', $slug);
		}else {
			$widgets_ui .= self::cubewp_builder_pro_widgets_ui($slug);
		}

		return $widgets_ui;
    }

	/**
	 * Method cubewp_builder_pro_widgets_ui
	 *
	 * @param string $post_type
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public static function cubewp_builder_pro_widgets_ui($post_type) {
	$widgets_ui = '';
	$groups = cwp_get_groups_by_post_type($post_type);
	if (isset($groups) && !empty($groups) && count($groups) > 0) {
		foreach ($groups as $group) {
			$widgets_ui .= '<div id="group-' . rand(000000, 999999) . '" class="cubewp-builder-section cubewp-expand-container">';
			$widgets_ui .= '<div class="cubewp-builder-section-header">';
			$widgets_ui .= '<h3>' . esc_html(get_the_title($group)) . '</h3>';
			$widgets_ui .= '<a href="https://cubewp.com/cubewp-frontend-pro/" target="_blank"><span class="cubewp-pro-tag">' . esc_html__("PRO", "cubewp-framework") . '</span></a>';
			$widgets_ui .= '</div>';
			$widgets_ui .= '</div>';
		}
	}

	return $widgets_ui;
	}
	
	/**
	 * Method builder_fields_parameters
	 *
	 * @param array $args form arguments to parse with default args
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function builder_fields_parameters($args = array()) {
		$default = array(
			'form_type'      => '',
			'wrapper_class'  => '',
			'page_title'     => '',
			'switcher_types' => array(),
			'switcher_title' => '',
			'content_switcher'     => '',
		);

		return wp_parse_args($args, $default);
	}
	
	/**
	 * Method CubeWp_build_tab_options
	 *
	 * @param array $data form data
	 *
	 * @return set array
	 * @since  1.0.0
	 */
	protected static function CubeWp_build_tab_options(array $data) {
		$return = array();
		if (isset($data['switcher_types']) && is_array($data['switcher_types']) && count($data['switcher_types']) > 0) {
			$options = $data['switcher_types'];
			foreach ($options as $slug => $title) {
				$switcher = apply_filters("cubewp/builder/{$data['form_type']}/switcher",array(),$slug);
				$return[$slug]["title"] = $title;
                if ( ! empty($switcher) && is_array($switcher)) {
                    $return[$slug]["switcher"] = $switcher;
                } else {
                    $return[$slug]["switcher"] = false;
                }
			}
		}
		$taboptions = $return;
		global $taboptions;
		self::$tab_options = $return;
	}
	
	/**
	 * Method builder_header
	 *
	 * @param string $title
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_header($title = '') {
		return '
		<ul id="size-list" class="hidden">
            <li data-class="size-1-4" data-text="1 / 4" class="min"></li>
            <li data-class="size-1-3" data-text="1 / 3"></li>
            <li data-class="size-1-2" data-text="1 / 2"></li>
            <li data-class="size-2-3" data-text="2 / 3"></li>
            <li data-class="size-3-4" data-text="3 / 4"></li>
        	<li data-class="size-1-1" data-text="1 / 1" class="max"></li>
		</ul>
		<section id="cubewp-title-bar">
			' . self::cubewp_builder_title($title) . '
			' . self::builder_get_shortcode() . '
		</section>';
	}
	
	/**
	 * Method cubewp_builder_title
	 *
	 * @param string $title 
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function cubewp_builder_title($title = '') {
		return "<h1>{$title}</h1>";
	}
	
	/**
	 * Method CubeWp_build_post_type_switcher
	 *
	 * @param array $data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function CubeWp_build_post_type_switcher(array $data) {
		$title   = $data['switcher_title'];
		$options = self::$tab_options;
		if (empty($options)) {
			return '<h3 style="text-align: center;">' . esc_html__("No Custom Post Type Found.", "cubewp-framework") . '</h3>';
		 }
		$name    = "cubewp-builder-cpt";
		$class   = "cubewp-tab-switcher cubewp-tab-switcher-trigger-on-load cubewp-tab-switcher-have-child";
		$output  = '<div class="cubewp-builder-sidebar-option">';
		$output .= '<label for="' . $name . '">' . $title . '</label>';
		$output .= '<select name="' . $name . '" id="' . $name . '" class="' . $class . '">';
		foreach ($options as $slug => $option) {
			$output .= '<option data-switcher-target="cubewp-switcher-tab-' . $slug . '" value="' . $slug . '">' . $option["title"] . '</option>';
		}
		$output .= '</select>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Method CubeWp_build_plans_switcher
	 *
	 * @param array $data 
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function CubeWp_build_content_switcher(array $data) {
		$output = null;
		if (isset($data['form_type']) && $data['form_type'] == 'post_type') {
			$options = self::$tab_options;
			if (isset($options) && count($options) != 0) {
				foreach ($options as $slug => $option) {
					if ( ! $option['switcher']) {
						continue;
					}
					$name        = "cubewp-builder-" . $slug . "-plan";
					$class       = "cubewp-tab-switcher";
					$options = $option['switcher']['options'];
					$title = $option['switcher']['title'];
					if (isset($options) && ! empty($options)) {
						$output .= '<div class="cubewp-switcher-tab-' . $slug . ' cubewp-tab-switcher-target">';
						$output .= '<div class="cubewp-builder-sidebar-option">';
						$output .= '<label for="' . $name . '">' . $title . '</label>';
						$output .= '<select name="' . $name . '" id="' . $name . '" class="' . $class . '">';
						foreach ($options as $id => $value) {
							$output .= '<option data-switcher-target="cubewp-switcher-tab-' . $id . '" value="' . $id . '">' . $value . '</option>';
						}
						$output .= '</select>';
                        $output .= '</div>';
						$output .= '</div>';
					}
				}
			}
		}

		return $output;
	}
	
	/**
	 * Method cubewp_builder_area_topbar
	 *
	 * @param string $slug post type slug
	 * @param array $data form data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function cubewp_builder_area_topbar(string $slug = "", array $data = array()) {
        return '<div class="cubewp-builder-container-topbar">
            ' . self::builder_form_settings_btn($data['form_type']) . '
            ' . self::builder_add_Section() . '
            '.self::builder_hidden_fields($slug, $data['form_type']).'
        </div>';
    }
	
	/**
	 * Method cubewp_builder_area
	 *
	 * @param array $data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function cubewp_builder_area(array $data) {
        $output = '';
		$options = self::$tab_options;
		if (isset($options) && count($options) > 0) {
			foreach ($options as $slug => $option) {
				$output .= '<div id="type-' . esc_attr__($slug) . '" class="cubewp-type-container cubewp-switcher-tab-' . esc_attr__($slug) . ' cubewp-tab-switcher-target">';
                    if ( ! $option["switcher"]) {
	                    $output .= self::cubewp_builder_area_content($slug, $data);
                    }else {
						$switcher = $option["switcher"];
						if (!empty($switcher) && isset($switcher['options'])) {
							foreach ($switcher["options"] as $id => $val) {
								$data['content_switcher'] = $id;
								$data_type = self::cubewp_check_switcher_type($id);
								$output .= '<div id="plan-' . esc_attr__($id) . '" class="cubewp-plan-tab cubewp-switcher-tab-' . esc_attr__($id) . ' cubewp-tab-switcher-target" data-id="' . esc_attr__($id) . '" '.$data_type.'>';
								$output .= self::cubewp_builder_area_content($slug, $data);
								$output .= '</div>';
							}
						}
                    }
				$output .= '</div>';
			}
		}

        return $output;
	}

	/**
	 * Method cubewp_builder_area
	 *
	 * @param array $data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function cubewp_check_switcher_type($id = '') {
		if( is_numeric($id) && !is_null(get_post($id)) && get_post_type( $id ) == 'price_plan'){
			return 'data-type="price_plan"';
		}
		return 'data-type="'. $id .'"';
	}
	
	/**
	 * Method cubewp_builder_area_content
	 *
	 * @param string $slug post type slug
	 * @param array $data form data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function cubewp_builder_area_content(string $slug, array $data) {
		$output = '';
		if ($data['form_type'] == 'single_layout') {
			$output .= self::cubewp_builder_area_topbar($slug, $data) . '
            <div class="cubewp-builder-area">
			' . self::builder_settings($slug,$data) . '
                <div class="cubewp-single-layout-builder-container single-layout-builder">
                    <div class="cubewp-single-layout-builder-content">
                        <div class="cubewp-builder-sections">
                            ' . apply_filters("cubewp/builder/single/right/content/section", "", $slug, $data) . '
							<div class="cubewp-single-builder-section-placeholder"><p>' . esc_html__("Create Sections For Content Area", "cubewp-framework") . '</p></div>
                        </div>
                    </div>
                    <div class="cubewp-single-layout-builder-sidebar">
                        <div class="cubewp-builder-sections">
                            ' . apply_filters("cubewp/builder/single/right/sidebar/section", "", $slug, $data) . '
							<div class="cubewp-single-builder-section-placeholder"><p>' . esc_html__("Create Sections For Sidebar", "cubewp-framework") . '</p></div>
                        </div>
                    </div>
                </div>
				' . self::cubewp_builder_no_section( false, $slug, $data ) . '
            </div>';
        }else {
			$output .= self::cubewp_builder_area_topbar($slug, $data) . '
            <div class="cubewp-builder-area">
                ' . self::builder_settings($slug,$data) . '
                <div class="cubewp-builder-sections">
                    ' . apply_filters("cubewp/builder/default/right/section", '', $slug, $data) . '
                </div>
				' . self::cubewp_builder_no_section( false, $slug, $data ) . '
            </div>';
		}

        return $output;
    }
	
	/**
	 * Method builder_hidden_fields
	 *
	 * @param string $key post type slug
	 * @param string $FormType form type
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_hidden_fields($key, $FormType) {
		$output        = '';
		$hidden_fields = array(
			array(
				'class' => 'form-relation',
				'name'  => 'form_relation',
				'value' => $key,
			),
			array(
				'class' => 'form-type',
				'name'  => 'form_type',
				'value' => $FormType,
			),
		);
		foreach ($hidden_fields as $field) {
			$output .= cwp_render_hidden_input($field);
		}

		return $output;
	}
	
	/**
	 * Method builder_single_layout
	 *
	 * @param array $data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_single_layout(array $data) {
		$output = '';
        $options = self::$tab_options;
		if (isset($options) && count($options) > 0) {
			foreach ($options as $slug => $option) {
				$output .= '<div id="type-' . esc_attr__($slug) . '" class="cubewp-type-container cubewp-switcher-tab-' . esc_attr__($slug) . ' cubewp-tab-switcher-target">';
				$output .= self::cubewp_builder_area_content($slug, $data);
				$output .= '</div>';
            }
        }else {
			$output .= '<div id="type-temp" class="cubewp-type-container cubewp-switcher-tab-temp cubewp-tab-switcher-target active-tab">';
			$output .= '<div class="cubewp-builder-area">';
			$output .= self::cubewp_builder_no_section(true);
			$output .= '</div>';
			$output .= '</div>';
		 }

        return $output;
	}
	
	/**
	 * Method builder_add_Section
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_add_Section() {
		if ( self::$FORM_TYPE != 'search_filters' && self::$FORM_TYPE != 'search_fields' ) {
		return '<button class="button cwpform-add-section">
			<span class="dashicons dashicons-plus"></span>
			' . esc_html__( "Create Section", "cubewp-framework" ) . '
		</button>';
		}

		return '';
	}
	
	/**
	 * Method builder_settings
	 *
	 * @param string $slug
	 * @param array $data
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_settings(string $slug, array $data) {
		return '<div class="form-settings" style="display:none;">
            ' . apply_filters("cubewp/builder/right/settings", '', $slug,$data) . '
        </div>';
	}
	
	/**
	 * Method builder_get_shortcode
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public static function builder_get_shortcode() {
		if (empty(self::$tab_options)) {
			return '';
		}
		$button_text = esc_html__("Save Changes", "cubewp-framework");
		return '<div class="shoftcode-area">
			<div class="cwpform-shortcode"></div>
			<button class="button-primary cwpform-get-shortcode">
				'. $button_text .'
			</button>
		</div>';
	 }
	
	/**
	 * Method builder_form_settings_btn
	 *
	 * @param string $FormType
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	protected static function builder_form_settings_btn($FormType) {
		if ( (! cubewp_check_if_elementor_active() || cubewp_check_if_elementor_active(true)) && $FormType == 'single_layout') {
			return '';
		}
		$setting_text = esc_html__("Form Settings", "cubewp-framework");
		if ($FormType == 'single_layout') {
		   $setting_text = esc_html__("Single Page Settings", "cubewp-framework");
		}
	 
		return '<button class="button form-settings-form">
				<span class="dashicons dashicons-admin-generic"></span>
				' . $setting_text . '
			</button>';
	}

	/**
	 * Method cubewp_builder_no_section
	 *
	 * @return string html
	 * @since  1.0.0
	 */	
	protected static function cubewp_builder_no_section( $no_cpt = FALSE, $post_type = '', $data = array() ) {
		$FormType = self::$FORM_TYPE;
		$output   = '';
		if ( $FormType != 'search_filters' && $FormType != 'search_fields' ) {
		   $output .= '<div class="cubewp-builder-no-section hidden">
			 <img src="' . CWP_PLUGIN_URI . 'cube/assets/admin/images/no-section.png" alt="' . esc_html__( "No Section Image", "cubewp-framework" ) . '">';
		   if ( ! $no_cpt ) {
			  $output .= '<h3>' . esc_html__( "Let's build something awesome today!", "cubewp-framework" ) . '</h3>';
			  $output .= '<div class="cubewp-builder-no-section-steps">';
			  if ( $FormType == 'post_type' || $FormType == 'single_layout' ) {
				 $output .= '<p><span>' . esc_html__( "1", "cubewp-framework" ) . '</span>' . esc_html__( "Select a Post Type", "cubewp-framework" ) . '</p>';
			  } else {
				 $output .= '<p><span>' . esc_html__( "1", "cubewp-framework" ) . '</span>' . esc_html__( "Select a User Role", "cubewp-framework" ) . '</p>';
			  }
			  $output .= '<p><span>' . esc_html__( "2", "cubewp-framework" ) . '</span>' . esc_html__( "Create a Section", "cubewp-framework" ) . '</p>
				 <p><span>' . esc_html__( "3", "cubewp-framework" ) . '</span>' . esc_html__( "Drag a Form Field", "cubewp-framework" ) . '</p>
				 <p><span>' . esc_html__( "4", "cubewp-framework" ) . '</span>' . esc_html__( "Hit Save Changes", "cubewp-framework" ) . '</p>
			  </div>
			  <button class="button button-primary cubewp-trigger-add-section">
				 <span class="dashicons dashicons-plus"></span>
				 ' . esc_html__( "Create Section", "cubewp-framework" ) . '
			  </button>';
	
			  if ( ! empty( $data ) ) {
				 $plans = apply_filters( "cubewp/builder/{$FormType}/switcher", array(), $post_type );
				 if ( $FormType == 'post_type' && isset( $plans['options'] ) &&  !empty( $plans['options'] )) {
					$plans_options  = '';
					$post_type_form = CWP()->get_form( 'post_type' );
					foreach ( $plans['options'] as $plan => $title ) {
					   if ( $plan == $data['content_switcher'] || ! isset( $post_type_form[ $post_type ][ $plan ]['groups'] ) || empty( $post_type_form[ $post_type ][ $plan ]['groups'] ) ) {
						  continue;
					   }
					   if ( is_numeric( $plan ) ) {
						  $plan_title = get_the_title( $plan );
					   }else {
						  $plan_title = $title;
					   }
					   $plans_options .= '<option value="' . $plan . '">' . esc_html( $plan_title ) . '</option>';
					}
					if ( ! empty( $plans_options ) ) {
					   $output .= '<div class="cubewp-builder-sections-importer">
						  <label for="cubewp-builder-section-import-' . $data['content_switcher'] . '">' . esc_html__( "Or Copy Content From", "cubewp-framework" ) . '</label>
						  <select id="cubewp-builder-section-import-' . $data['content_switcher'] . '" class="cubewp-builder-section-import">';
						  $output .= $plans_options;
						  $output .= '</select>
						  <button class="button cwpform-import-sections">
						  <span class="dashicons dashicons-admin-page"></span>
						  ' . esc_html__( "Copy", "cubewp-framework" ) . '
						  </button>
					   </div>';
					}
				 }
			  }
	
		   } else {
			  $output .= '<h3>' . esc_html__( "No Custom Post Type Found.", "cubewp-framework" ) . '</h3>';
		   }
		   $output .= '</div>';
		}
	
		return $output;
	}
}