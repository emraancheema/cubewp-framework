<?php

class CubeWp_Frontend_Image_Field extends CubeWp_Frontend {

	public function __construct() {
		add_filter('cubewp/frontend/image/field', array($this, 'render_image_field'), 10, 2);

		add_filter('cubewp/user/registration/image/field', array($this, 'render_image_field'), 10, 2);
		add_filter('cubewp/user/profile/image/field', array($this, 'render_image_field'), 10, 2);
	}

	function render_image_field($output = '', $args = array()) {
		$args['container_class'] = 'cubewp-have-image-field';
		$args['type'] = 'file';
		$args['extra_attrs'] = 'accept="image/png,image/jpg,image/jpeg,image/gif" data-error-msg="' . esc_html__("is not acceptable in this field.", "cubewp-framework") . '"';

		return apply_filters("cubewp/frontend/file/field", $output, $args);
    }

}

new CubeWp_Frontend_Image_Field();