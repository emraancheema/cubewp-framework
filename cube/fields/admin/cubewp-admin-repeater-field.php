<?php
/**
 * CubeWp admin repeater field
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined('ABSPATH')) {
	exit;
}

class CubeWp_Admin_Repeater_Field extends CubeWp_Admin {

	public function __construct() {
		add_filter('cubewp/admin/post/repeating_field/field', array($this, 'render_post_repeating_field'), 10, 2);
		add_action('wp_ajax_cwp_add_repeating_field', array($this, 'cwp_add_repeating_field'));
	}

	/**
	 * Method render_repeating_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_post_repeating_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/admin/field/parametrs', $args);
		
		$output = $this->cwp_field_wrap_start($args);
		$output .= '<div id="cwp-repeating-field-' . $args['id'] . '" class="cwp-custom-field cwp-repeating-field" data-id="' . $args['id'] . '">';
		$output .= '<div class="cwp-field">';
		$output .= '<input type="hidden" name="cwp_meta[' . $args['name'] . '][]" value="">';
		$output .= '<table class="form-table cwp-repeating-table"><tbody>';
		
		if (isset($args['sub_fields']) && ! empty($args['sub_fields'])) {
			if (isset($args['value']) && ! empty($args['value'])) {
				for ($i = 0; $i < count($args['value']); $i ++) {

					$output .= '<tr class="cwp-repeating-field">
                        <td class="cwp-repeating-count"><span class="count">' . ($i + 1) . '</span> <a class="cwp-remove-repeating-field" href="javascript:void(0);">-</a></td>
                        <td class="cwp-repeating-fields">
                            <table class="form-table">
                                <tbody class="ui-sortable">';
					foreach ($args['sub_fields'] as $sub_field) {
						$sub_field['custom_name'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '][]';
						$sub_field['value']       = isset($args['value'][$i][$sub_field['name']]) ? $args['value'][$i][$sub_field['name']] : '';
						$sub_field['id']          = 'cwp_' . rand(123456789, 1111111111);
						$sub_field['wrap']        = true;
						if ($sub_field['type'] == 'google_address') {
							$sub_field['custom_name_lat'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '_lat' . '][]';
							$sub_field['custom_name_lng'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '_lng' . '][]';
							$sub_field['lat']             = isset($args['value'][$i][$sub_field['name'] . '_lat']) ? $args['value'][$i][$sub_field['name'] . '_lat']: '';
							$sub_field['lng']             = isset($args['value'][$i][$sub_field['name'] . '_lng']) ? $args['value'][$i][$sub_field['name'] . '_lng']: '';
						}
						if ($sub_field['type'] == 'radio' || $sub_field['type'] == 'checkbox' || ($sub_field['type'] == 'dropdown' && (isset($sub_field['multiple']) && $sub_field['multiple'] == true))) {
							$sub_field['custom_name'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '][' . uniqid() . ']';
						}
						if ($sub_field['type'] == 'post' && ($sub_field['appearance'] == 'multi_select' || $sub_field['appearance'] == 'checkbox')) {
							$sub_field['custom_name'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '][' . uniqid() . ']';
						}
						if ($sub_field['type'] == 'gallery') {
							$sub_field['custom_name'] = 'cwp_meta[' . $args['name'] . '][' . $sub_field['name'] . '][' . uniqid() . ']';
						}
						$output .= apply_filters("cubewp/admin/post/{$sub_field['type']}/field", '', $sub_field);
					}
					$output .= '</tbody>
                            </table>
                        </td>
                    </tr>';
				}
			}
		}
		$output .= '</tbody>
                    </table>';
		$output .= '<a href="javascript:void(0);" class="button button-primary cwp-add-row-btn" data-id="' . $args['name'] . '">' . esc_html__('Add Row', 'cubewp-framework') . '</a>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= $this->cwp_field_wrap_end($args);

		$output = apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);

		return $output;
	}

	/**
	 * Method cwp_add_repeating_field
	 *
	 * @return array Json to ajax
	 * @since  1.0.0
	 */
	public function cwp_add_repeating_field() {
		$field_id      = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
		$field_options = get_field_options($field_id);
		$field_of      = 'post';
		if (empty($field_options) && count($field_options) == 0) {
			$field_options = get_user_field_options($field_id);
			$field_of      = 'user';
		}
		$sub_fields = isset($field_options['sub_fields']) && ! empty($field_options['sub_fields']) ? explode(',', $field_options['sub_fields']) : array();
		$output = '';
		$i = 1;
		if (isset($sub_fields) && ! empty($sub_fields)) {
			$output .= '<tr class="cwp-repeating-field">
                <td class="cwp-repeating-count"><span class="count">' . ($i + 1) . '</span> <a class="cwp-remove-repeating-field" href="javascript:void(0);">-</a></td>
                <td class="cwp-repeating-fields">
                    <table class="form-table">
                        <tbody class="ui-sortable">';
			foreach ($sub_fields as $sub_field) {
				if ($field_of == 'post') {
					$sub_field_options = get_field_options($sub_field);
				} else if ($field_of == 'user') {
					$sub_field_options = get_user_field_options($sub_field);
				}


				$sub_field_options['custom_name'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '][]';
				$sub_field_options['value']       = isset($sub_field_options['default_value']) ? $sub_field_options['default_value'] : '';

				$sub_field_options['id']   = 'cwp_' . rand(123456789, 1111111111);
				$sub_field_options['wrap'] = true;
				if ($sub_field_options['type'] == 'google_address') {
					$sub_field_options['custom_name_lat'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '_lat' . '][]';
					$sub_field_options['custom_name_lng'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '_lng' . '][]';
				}
				if ($sub_field_options['type'] == 'radio' || $sub_field_options['type'] == 'checkbox' || ($sub_field_options['type'] == 'dropdown' && isset($sub_field_options['multiple']) &&  $sub_field_options['multiple'] == true)) {
					$sub_field_options['custom_name'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '][' . uniqid() . ']';
				}
				if ($sub_field_options['type'] == 'post' && ($sub_field_options['appearance'] == 'multi_select' || $sub_field_options['appearance'] == 'checkbox')) {
					$sub_field_options['custom_name'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '][' . uniqid() . ']';
				}
				if ($sub_field_options['type'] == 'gallery') {
					$sub_field_options['custom_name'] = 'cwp_meta[' . $field_options['name'] . '][' . $sub_field_options['name'] . '][' . uniqid() . ']';
				}
				$output .= apply_filters("cubewp/admin/post/{$sub_field_options['type']}/field", '', $sub_field_options);

				$i++;
			}
			$output .= '</tbody>
                    </table>
                </td>
            </tr>';

		}

		wp_send_json(array('sub_field_html' => $output));
	}
}

new CubeWp_Admin_Repeater_Field();