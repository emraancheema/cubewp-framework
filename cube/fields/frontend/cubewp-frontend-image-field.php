<?php

class CubeWp_Frontend_Image_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/image/field', array($this, 'render_image_field'), 10, 2);

		add_filter('cubewp/user/registration/image/field', array($this, 'render_image_field'), 10, 2);
		add_filter('cubewp/user/profile/image/field', array($this, 'render_image_field'), 10, 2);
	}

	function render_image_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/frontend/field/parametrs', $args);
		$args['container_class'] .= ' cubewp-have-image-field';
		$args['type'] = 'file';
		if (isset($args["file_types"]) && !empty($args["file_types"])) {
			$accept = 'accept="' . $args["file_types"] . '"';
		}else {
			$accept = 'accept="image/png,image/jpg,image/jpeg,image/gif"';
		}
		$args['extra_attrs'] = $accept . ' data-error-msg="' . esc_html__("is not acceptable in this field.", "cubewp-framework") . '"';
		if (isset($args["upload_size"]) && !empty($args["upload_size"]) && is_numeric($args["upload_size"])) {
			$args['extra_attrs'] .= ' data-max-upload="' . esc_attr( $args["upload_size"] ) . '"';
		}
		return apply_filters("cubewp/frontend/file/field", $output, $args);
    }

}

new CubeWp_Frontend_Image_Field();