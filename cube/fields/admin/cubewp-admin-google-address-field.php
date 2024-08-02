<?php
/**
 * CubeWp admin google address field
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined('ABSPATH')) {
	exit;
}

/**
 * CubeWp_Admin_Google_Address_Field
 */
class CubeWp_Admin_Google_Address_Field extends CubeWp_Admin {

	public function __construct() {
		add_filter('cubewp/admin/post/google_address/field', array($this, 'render_google_address_field'), 10, 2);
	}

	/**
	 * Method render_google_address_field
	 *
	 * @param string $output
	 * @param array  $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_google_address_field($output = '', $args = array()) {
        
		wp_enqueue_script('cubewp-google-address-field');

		$args = apply_filters('cubewp/admin/field/parametrs', $args);

		$output = $this->cwp_field_wrap_start($args);
		$output .= '<div class="cwp-google-address">';

		$input_attrs = array(
			'id'          => $args['id'],
			'class'       => $args['class'] . ' address',
			'name'        => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
			'value'       => isset($args['value']) ? $args['value'] : '',
			'placeholder' => $args['placeholder']
		);
		$extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
		if (isset($args['required']) && $args['required'] == 1) {
			$input_attrs['class'] .= ' required';
			$validation_msg       = isset($args['validation_msg']) ? $args['validation_msg'] : '';
			$extra_attrs         .= ' data-validation_msg="' . $validation_msg . '"';
		}
		$extra_attrs  .= ' autocomplete="off" data-placeholder="' . esc_html__("Enter a Location", "cubewp-framework") . '"';
		$input_attrs['extra_attrs'] = $extra_attrs;

		$output .= '<div class="cwp-field-google-address-input-container">';
		$output .= cwp_render_text_input($input_attrs);
		$output .= '
                    <svg xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" class="cwp-get-current-location" viewBox="0 0 28.278 28.278">
                      <path d="M17.995,12.854a5.141,5.141,0,1,0,5.141,5.141,5.14,5.14,0,0,0-5.141-5.141ZM29.486,16.71A11.561,11.561,0,0,0,19.28,6.5V3.856H16.71V6.5A11.561,11.561,0,0,0,6.5,16.71H3.856V19.28H6.5A11.561,11.561,0,0,0,16.71,29.486v2.648H19.28V29.486A11.561,11.561,0,0,0,29.486,19.28h2.648V16.71H29.486ZM17.995,26.992a9,9,0,1,1,9-9A9,9,0,0,1,17.995,26.992Z" transform="translate(-3.856 -3.856)"/>
                    </svg>
                </div>';
		$input_attrs = array(
			'id'    => $args['id'] . '_latitude',
			'class' => 'latitude',
			'name'  => ! empty($args['custom_name_lat']) ? $args['custom_name_lat'] : $args['name'],
			'value' => isset($args['lat']) ? $args['lat'] : '',
			'placeholder' => esc_html__("Enter Latitude", "cubewp-framework")
		);
		$output      .= cwp_render_hidden_input($input_attrs);
		$input_attrs = array(
			'id'    => $args['id'] . '_longitude',
			'class' => 'longitude',
			'name'  => ! empty($args['custom_name_lng']) ? $args['custom_name_lng'] : $args['name'],
			'value' => isset($args['lng']) ? $args['lng'] : '',
			'placeholder' => esc_html__("Enter Longitude", "cubewp-framework")
		);
		$output      .= cwp_render_hidden_input($input_attrs);
		$id     = isset($args['id']) ? $args['id'] : $args['name'];
		$output .= '<button class="button cubewp-address-manually" type="button">' . esc_html__("Enter Coordinates Manually", "cubewp-framework") . '</button>';

		$output .= '<div class="cwp-map-holder" id="map-' . esc_attr($id) . '"></div>';
		$output .= '</div>';
		$output .= $this->cwp_field_wrap_end($args);

		$output = apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);

		return $output;

	}

}

new CubeWp_Admin_Google_Address_Field();