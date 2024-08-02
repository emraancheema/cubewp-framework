<?php
/**
 * CubeWp Form Fields Functions
 *
 * @version 1.0
 * @package cubewp/cube/functions
 */
if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * Convert choices string to array list
 *
 * @param string $options string of choices.
 *
 * @return array $options_arr List of Array.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_convert_choices_to_array")) {
	function cwp_convert_choices_to_array($options = array()) {
		if($options == null || empty($options)) return array();

		if (is_array($options)) {
			return $options;
		}
		$options = json_decode($options, true);
		if (isset($options['label']) && ! empty($options['label'])) {
			$options_arr = array();
			foreach ($options['label'] as $key => $label) {
				$value = isset($options['value'][$key]) ? $options['value'][$key] : '';
				if ($label && $value) {
					$options_arr[$value] = $label;
				}
			}

			return $options_arr;
		}
		$options_arr = array();
		$options     = explode("\n", $options);
		foreach ($options as $option) {
			$key = trim($option);
			$val = trim($option);
			if (is_string($option) && strpos($option, " : ") !== false) {
				$option = explode(' : ', $option);
				$key    = trim($option[0]);
				$val    = trim($option[1]);
			}
			$options_arr[$key] = $val;
		}

		return $options_arr;
	}
}

/**
 * Generated valid HTML from an array of field attrs.
 *
 * @param array $attrs The array of attrs.
 *
 * @return    string
 * @since  1.0.0
 */
if ( ! function_exists("cwp_esc_field_attrs")) {
	function cwp_esc_field_attrs($attrs = array()) {
		$html = '';
		foreach ($attrs as $k => $v) {
			// Boolean
			if (is_bool($v)) {
				$v = $v ? 1 : 0;

				// Object
			} else if (is_array($v) || is_object($v)) {
				$v = json_encode($v);
			}
			if ($k == 'value' || $v) {
				$html .= sprintf(' %s="%s"', esc_attr($k), esc_attr($v));
			}
		}

		return trim($html);
	}
}

/**
 * Method cwp_get_terms
 *
 * @param string $taxonomy
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_terms")) {
	function cwp_get_terms($taxonomy = '') {
		$options = array();
		$terms   = get_terms($taxonomy, array('hide_empty' => false, 'parent' => 0));
		if (isset($terms) && ! empty($terms) && ! is_wp_error($terms)) {
			foreach ($terms as $term) {
				$options[$term->slug] = array(
					'term_name' => $term->name,
					'term_id'   => $term->term_id,
					'childern'  => cwp_get_child_terms($term),
				);
			}

			return $options;
		}
	}
}

/**
 * Method cwp_get_child_terms
 *
 * @param array $term
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_child_terms")) {
	function cwp_get_child_terms($term = '') {
		$options  = array();
		$children = get_terms($term->taxonomy, array(
			'parent'     => $term->term_id,
			'hide_empty' => false
		));
		if ($children) { // get_terms will return false if tax does not exist or term wasn't found.
			foreach ($children as $child) {
				$options[$child->slug] = array(
					'term_name' => $child->name,
					'term_id'   => $child->term_id,
					'childern'  => cwp_get_child_terms_level2($child),
				);
			}
		}

		return $options;
	}
}

/**
 * Method cwp_get_child_terms_level2
 *
 * @param array $term
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cwp_get_child_terms_level2")) {
	function cwp_get_child_terms_level2($term = '') {
		$options  = array();
		$children = get_terms($term->taxonomy, array(
			'parent'     => $term->term_id,
			'hide_empty' => false
		));
		if ($children) { // get_terms will return false if tax does not exist or term wasn't found.
			foreach ($children as $child) {
				$options[$child->slug] = array(
					'term_name' => $child->name,
					'term_id'   => $child->term_id,
					'childern'  => '',
				);
			}
		}

		return $options;
	}
}

/**
 * Method cubewp_post_type_default_fields
 *
 * @param string $postType [explicite description]
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cubewp_post_type_default_fields")) {
	function cubewp_post_type_default_fields($postType = '') {
		$field = array();
		if (post_type_supports($postType, 'title')) {
			$field['the_title'] = array(
				'label' => __("Title", "cubewp-framework"),
				'name'  => 'the_title',
				'type'  => 'text',
			);
		}
		if (post_type_supports($postType, 'editor')) {
			$field['the_content'] = array(
				'label' => __("Content", "cubewp-framework"),
				'name'  => 'the_content',
				'type'  => 'wysiwyg_editor',
			);
		}
		if (post_type_supports($postType, 'thumbnail')) {
			$field['featured_image'] = array(
				'label'           => __("Featured Image", "cubewp-framework"),
				'name'            => 'featured_image',
				'type'            => 'file',
				'container_class' => 'cubewp-have-image-field',
				'extra_attrs'     => 'accept="image/png,image/jpg,image/jpeg,image/gif,image/webp" data-error-msg="' . esc_html__("is not acceptable in this field.", "cubewp-framework") . '"',
			);
		}
		if (post_type_supports($postType, 'excerpt')) {
            $field['the_excerpt'] = array(
                'label' => __("Excerpt", "cubewp-framework"),
                'name'  => 'the_excerpt',
                'type'  => 'textarea',
            );
        }

		return $field;
	}
}

/**
 * Method cubewp_search_default_fields
 *
 * @param string $form_type [explicite description]
 *
 * @return array
 * @since  1.0.0
 */
if ( ! function_exists("cubewp_search_default_fields")) {
	function cubewp_search_default_fields($form_type = '') {
		$search_btn = array();
		if ($form_type == 'search_fields') {

			$wp_default_fields = array(
				'keyword' => array(
					'label' => __("Keyword", "cubewp-framework"),
					'name'  => 's',
					'type'  => 'text',
				),
				'button'  => array(
					'label' => esc_html__("Search", "cubewp-framework"),
					'name'  => 'search_button',
					'type'  => 'button',
				)
			);

		} else {

			$wp_default_fields = array(
				'keyword' => array(
					'label' => __("Keyword", "cubewp-framework"),
					'name'  => 's',
					'type'  => 'text',
				)
			);

		}


		return $wp_default_fields;
	}
}

/**
 * Render input text field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Text Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_text_input")) {
	function cwp_render_text_input($attrs = array()) {

		$defaults = array(
			'type'        => 'text',
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render input hidden field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Hidden Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_hidden_input")) {
	function cwp_render_hidden_input($attrs = array()) {

		$defaults = array(
			'type'        => 'hidden',
			'id'          => '',
			'name'        => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render text-area field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Text-Area Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_textarea_input")) {
	function cwp_render_textarea_input($attrs = array()) {

		$defaults = array(
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
			'rows'        => '8',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = $value = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}
		if (isset($attrs['value'])) {
			$value = $attrs['value'];
			unset($attrs['value']);
		}

		return sprintf('<textarea %s %s>%s</textarea>', cwp_esc_field_attrs($attrs), $extra_attrs, esc_textarea($value));

	}
}

/**
 * Render input number field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Number Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_number_input")) {
	function cwp_render_number_input($attrs = array()) {

		$defaults = array(
			'type'        => 'number',
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render input email field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Email Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_email_input")) {
	function cwp_render_email_input($attrs = array()) {

		$defaults = array(
			'type'        => 'email',
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render input url field
 *
 * @param array $attrs Field Options.
 *
 * @return string The URL Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_url_input")) {
	function cwp_render_url_input($attrs = array()) {

		$defaults = array(
			'type'        => 'url',
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render upload field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Upload Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_file_input")) {
	function cwp_render_file_input($attrs = array()) {

		$defaults = array(
			'type'        => 'file',
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$extra_attrs = '';
		if (isset($attrs['extra_attrs'])) {
			$extra_attrs = $attrs['extra_attrs'];
			unset($attrs['extra_attrs']);
		}
		if (isset($attrs['options'])) {
			unset($attrs['options']);
		}
		if (isset($attrs['placeholder'])) {
			unset($attrs['placeholder']);
		}

		return sprintf('<input %s %s />', cwp_esc_field_attrs($attrs), $extra_attrs);
	}
}

/**
 * Render switch field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Switch Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_switch_input")) {
	function cwp_render_switch_input($attrs = array()) {

		$defaults = array(
			'type'        => 'checkbox',
			'id'          => 'id-'.rand(),
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '1',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);
		$checked = '';
		if (isset($attrs['value']) && !empty($attrs['value']) && $attrs['value'] == '1') {
			$checked = ' checked="checked"';
		}

		$html = '<label class="cwp-switch" for="' . $attrs['id'] . '">
		<input type="checkbox" id="' . $attrs['id'] . '" name="' . $attrs['name'] . '" class="switch-field checkbox cwp-switch-check cwp-switch-field ' . $attrs['class'] . '" value="1" '.$attrs['extra_attrs'] .$checked. '>
		<span class="cwp-switch-slider"></span>
		<span class="cwp-switch-text-no">No</span>
		<span class="cwp-switch-text-yes">Yes</span>
		</label>';
		return $html;
	}
}

/**
 * Render radio field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Radio Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_radio_input")) {
	function cwp_render_radio_input($attrs = array()) {

		$defaults = array(
			'type'        => 'radio',
			'id'          => '',
			'name'        => '',
			'class'       => '',
			'value'       => '',
			'options'     => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$class = '';
		if (isset($attrs['class']) && $attrs['class'] != '') {
			$class = ' class="' . $attrs['class'] . '"';
		}

		$html = '';
		if (isset($attrs['options']) && ! empty($attrs['options'])) {
			$options_arr = cwp_convert_choices_to_array($attrs['options']);
			foreach ($options_arr as $key => $val) {

				$checked = '';
				if (isset($attrs['value']) && is_array($attrs['value']) && in_array($key, $attrs['value'])) {
					$checked = ' checked="checked"';
				} else if (isset($attrs['value']) && ! is_array($attrs['value']) && $key == $attrs['value']) {
					$checked = ' checked="checked"';
				}

				$html .= '<label class="custom-radio-container" for="' . $attrs['id'] . '-' . str_replace(array(
						' ',
						'_'
					), array('-'), $key) . '">' . $val . '
                <input' . $checked . $class . ' type="' . $attrs['type'] . '" id="' . $attrs['id'] . '-' . str_replace(array(
						' ',
						'_'
					), array('-'), $key) . '" name="' . $attrs['name'] . '" value="' . $key . '" ' . $attrs['extra_attrs'] . '>
                <span class="checkmark"></span>
            </label>';
			}
		}

		return $html;
	}
}

/**
 * Render checkbox field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Checkbox Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_checkbox_input")) {
	function cwp_render_checkbox_input($attrs = array()) {

		$defaults = array(
			'type'        => 'checkbox',
			'id'          => '',
			'value'       => '',
			'name'        => '',
			'class'       => '',
			'options'     => '',
			'extra_attrs' => '',
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$class = '';
		if (isset($attrs['class']) && $attrs['class'] != '') {
			$class = ' class="' . $attrs['class'] . '"';
		}

		$html = '';
		if (isset($attrs['options']) && ! empty($attrs['options'])) {
			$options_arr = cwp_convert_choices_to_array($attrs['options']);
			foreach ($options_arr as $key => $val) {

				$checked = '';
				if (isset($attrs['value']) && is_array($attrs['value']) && in_array($key, $attrs['value'])) {
					$checked = ' checked="checked"';
				} else if (isset($attrs['value']) && ! is_array($attrs['value']) && $key == $attrs['value']) {
					$checked = ' checked="checked"';
				}

				$html .= '<label class="custom-checkbox-container" for="' . $attrs['id'] . '-' . str_replace(array(
						' ',
						'_'
					), array('-'), $key) . '">' . $val . '
                <input ' . $checked . $class . ' type="' . $attrs['type'] . '" id="' . $attrs['id'] . '-' . str_replace(array(
						' ',
						'_'
					), array('-'), $key) . '" name="' . $attrs['name'] . '[]" value="' . $key . '" ' . $attrs['extra_attrs'] . '>
                <span class="checkmark"></span>
            </label>';
			}
			$html .= '<input type="hidden" name="' . $attrs['name'] . '[]" value="">';
		}

		return $html;
	}
}

/**
 * Render dropdown field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Dropdown Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_dropdown_input")) {
	function cwp_render_dropdown_input($attrs = array()) {

		$defaults    = array(
			'id'           => '',
			'name'         => '',
			'placeholder'  => '',
			'class'        => '',
			'option_class' => '',
			'value'        => '',
			'options'      => '',
			'extra_attrs'  => '',
		);
		$attrs       = wp_parse_args($attrs, $defaults);
		$placeholder = esc_html__("Select Option", "cubewp-framework");
		if (isset($attrs['placeholder']) && ! empty($attrs['placeholder'])) {
			$placeholder = $attrs['placeholder'];
		}
		$html = '<select id="' . $attrs['id'] . '" class="' . $attrs['class'] . '" name="' . $attrs['name'] . '"' . $attrs['extra_attrs'] . ' placeholder="' . $placeholder . '">';
		if ( ! empty($placeholder) && (isset($attrs['select2_ui']) && $attrs['select2_ui'] == true)) {
			$html .= '<option></option>';
		} else if ( ! empty($placeholder)) {
			$html .= '<option value>' . $placeholder . '</option>';
		}
		if (isset($attrs['options']) && ! empty($attrs['options'])) {
			$options_arr = cwp_convert_choices_to_array($attrs['options']);
			foreach ($options_arr as $key => $val) {
				if ( is_array( $val ) ) {
					if ( ! empty( $val ) ) {
						$html .= '<optgroup label="' . $key . '">';
						foreach ( $val as $value => $label ) {
							$s = '';
							if (is_array($attrs['value']) && in_array($value, $attrs['value'])) {
								$s = 'selected';
							} else if ($value == $attrs['value']) {
								$s = 'selected';
							}
							$html .= '<option class="option" ' . $s . ' value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
						}
						$html .= '</optgroup>';
					}
				}else {
					$s = '';
					if (is_array($attrs['value']) && in_array($key, $attrs['value'])) {
						$s = 'selected';
					} else if ($key == $attrs['value']) {
						$s = 'selected';
					}
					$html .= '<option class="option" ' . $s . ' value="' . esc_attr($key) . '">' . esc_html($val) . '</option>';
				}
			}
		}

		$html .= '</select>';

		return $html;
	}
}

/**
 * Render multi dropdown field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Dropdown Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_multi_dropdown_input")) {
	function cwp_render_multi_dropdown_input($attrs = array()) {
		$defaults    = array(
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'options'     => '',
			'extra_attrs' => '',
			'hidden_input' => true,
		);
		$attrs       = wp_parse_args($attrs, $defaults);
		$field_attrs = $attrs['extra_attrs'] ?? '';
		if (isset($attrs['id']) && ! empty($attrs['id'])) {
			$field_attrs .= ' id="' . $attrs['id'] . '"';
		}
		if (isset($attrs['name']) && ! empty($attrs['name'])) {
			$field_attrs .= ' name="' . $attrs['name'] . '[]"';
		}
		if (isset($attrs['class']) && ! empty($attrs['class'])) {
			$field_attrs .= ' class="' . $attrs['class'] . '"';
		}
		$placeholder = esc_html__("Select Options", "cubewp-framework");
		if (isset($attrs['placeholder']) && ! empty($attrs['placeholder'])) {
			$placeholder = $attrs['placeholder'];
		}
		$html = '<select' . $field_attrs . ' multiple placeholder="' . $placeholder . '">';
		if (isset($attrs['options']) && ! empty($attrs['options'])) {
			$options_arr = cwp_convert_choices_to_array($attrs['options']);
			foreach ($options_arr as $key => $val) {
				$s = '';
				if (is_array($attrs['value']) && in_array($key, $attrs['value'])) {
					$s = 'selected';
				} else if ($key == $attrs['value']) {
					$s = 'selected';
				}
				if ( ! empty($key)) {
					$html .= '<option ' . $s . ' value="' . esc_attr($key) . '">' . esc_html($val) . '</option>';
				}
			}
		}
		$html .= '</select>';

		if ( isset( $attrs['hidden_input'] ) && ! empty( $attrs['hidden_input'] ) ) {
			$html .= '<input type="hidden" name="' . $attrs['name'] . '[]" value="">';
		}
		return $html;
	}
}

/**
 * Render editor field
 *
 * @param array $attrs Field Options.
 *
 * @return string The Editor Field HTML.
 * @since  1.0.0
 */
if ( ! function_exists("cwp_render_editor_input")) {
	function cwp_render_editor_input($attrs = array()) {

		$defaults = array(
			'id'          => '',
			'name'        => '',
			'placeholder' => '',
			'class'       => '',
			'value'       => '',
			'extra_attrs' => '',
			'rows'        => '8',
			'editor_media' => 0,
		);
		$attrs    = wp_parse_args($attrs, $defaults);

		$settings = array(
			'wpautop'       => true,
			'textarea_name' => $attrs['name'],
			'textarea_rows' => $attrs['rows'],
			'media_buttons' => $attrs['editor_media'],
			'quicktags'     => false,
			'editor_class'  => $attrs['class'],
			'tinymce'       => array(
				'theme_advanced_buttons1' => '',
				'theme_advanced_buttons2' => false,
				'theme_advanced_buttons3' => false,
				'theme_advanced_buttons4' => false,
			),
		);

		ob_start();
		wp_editor($attrs['value'], $attrs['id'], $settings);
		$output = ob_get_contents();
		ob_end_clean();
		ob_flush();

		return $output;

	}
}

/**
 * Method include_fields
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists("include_fields")) {
	function include_fields() {
		$admin_fields = array(
			'text',
			'number',
			'email',
			'url',
			'password',
			'textarea',
			'wysiwyg-editor',
			'oembed',
			'file',
			'gallery',
			'dropdown',
			'checkbox',
			'radio',
			'switch',
			'google-address',
			'date-picker',
			'date-time-picker',
			'time-picker',
			'post',
			'taxonomy',
			'user',
			'repeater'
		);
		foreach ($admin_fields as $admin_field) {
			$field_path = CWP_PLUGIN_PATH . "cube/fields/admin/cubewp-admin-{$admin_field}-field.php";
			if (file_exists($field_path)) {
				include_once $field_path;
			}
		}
	}
}

if ( ! function_exists("cubewp_dynamic_options")) {
	function cubewp_dynamic_options() {
		if ( ! wp_verify_nonce($_POST['security_nonce'], "cubewp_dynamic_options")) {
			wp_send_json_error(array(
				'msg' => esc_html__('Sorry! Security Verification Failed.', 'cubewp-frontend'),
			), 404);
		}

		$dropdown_type   = sanitize_text_field($_POST['dropdown_type']);
		$dropdown_values = sanitize_text_field($_POST['dropdown_values']);
		$keyword         = sanitize_text_field($_POST['keyword']);
		$options         = array();
		if ( ! empty($dropdown_type) && ! empty($dropdown_values) && ! empty($keyword)) {
			if ($dropdown_type == 'post' || $dropdown_type == 'user-posts') {
				$query_args = array(
					'post_type'      => $dropdown_values,
					'post_status'    => 'publish',
					's'              => $keyword,
					'posts_per_page' => - 1,
					'fields'         => 'ids'
				);
				if ( $dropdown_type == 'user-posts' ) {
					if ( is_user_logged_in() ) {
						$query_args['author'] = get_current_user_id();
					}else {
						$query_args = array();
					}
				}
				if ( ! empty( $query_args ) ) {
					$posts      = get_posts($query_args);
					if ( ! empty($posts) && is_array($posts)) {
						foreach ($posts as $post_id) {
							$options[] = array(
								"label" => esc_html(get_the_title($post_id)),
								"value" => $post_id
							);
						}
					}
				}
			} else if ($dropdown_type == 'user') {
				$query_args = array(
					'search' => "*{$keyword}*",
					'role__in'       => array($dropdown_values),
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'display_name'
					),
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key' => 'first_name',
							'value' => $keyword,
							'compare' => 'LIKE'
						),
						array(
							'key' => 'last_name',
							'value' => $keyword,
							'compare' => 'LIKE'
						),
						array(
							'key' => 'nickname',
							'value' => $keyword,
							'compare' => 'LIKE'
						)
					)
				);
				$users      = get_users($query_args);
				if ( ! empty($users) && is_array($users)) {
					foreach ($users as $user) {
						$options[] = array(
							"label" => esc_html($user->display_name),
							"value" => esc_html($user->ID)
						);
					}
				}
			} else if ($dropdown_type == 'taxonomy') {
				$query_args = array(
					'taxonomy'   => $dropdown_values,
					'hide_empty' => false,
					'fields'     => 'id=>name',
					'name__like' => $keyword
				);
				$terms      = get_terms($query_args);
				if ( ! empty($terms) && is_array($terms)) {
					foreach ($terms as $term_id => $term_name) {
						$options[] = array(
							"label" => esc_html($term_name),
							"value" => esc_html($term_id)
						);
					}
				}
			}
		}

		if (empty($options) || ! is_array($options)) {
			wp_send_json_error(array(
				'msg' => esc_html__('No Result Found.', 'cubewp-frontend'),
			), 404);
		}

		wp_send_json_success($options, 200);
	}

	add_action('wp_ajax_cubewp_dynamic_options', 'cubewp_dynamic_options');
	add_action('wp_ajax_nopriv_cubewp_dynamic_options', 'cubewp_dynamic_options');
}