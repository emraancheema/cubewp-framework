<?php
/**
 * CubeWp admin file field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_File_Field
 */
class CubeWp_Admin_File_Field extends CubeWp_Admin {
    public function __construct( ) {
        add_filter('cubewp/admin/post/file/field', array($this, 'render_file_field'), 10, 2);
    }
        
    /**
     * Method render_file_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_file_field($output = '', $args = array()) {
		$args          = apply_filters('cubewp/admin/field/parametrs', $args);
		$output        = $this->cwp_field_wrap_start($args);
		$remove_button = 'style="display:none;"';
		$input_name = ! empty($args['custom_name']) ? $args['custom_name'] : 'cwp_meta[' . $args['name'] . ']';
		$class      = '';
		$attr       = '';
        $args['not_formatted_value'] = $args['value'];
        $args['value'] = cwp_get_attachment_id( $args['value'] );
		if (isset($args['value']) && ! empty($args['value'])) {
			$remove_button = '';
		}
		if (isset($args['required']) && $args['required'] == 1) {
			$class          = 'required';
			$validation_msg = isset($args['validation_msg']) ? $args['validation_msg'] : '';
			$attr .= 'required data-validation_msg="' . $validation_msg . '"';
		}
        $accept_types = '';
        if (isset($args["file_types"]) && !empty($args["file_types"])) {
            $accept = $args["file_types"];
            $accept_types = $accept;
        }else {
            $accept = esc_html__("PDF, zip, txt, word and other file types", 'cubewp-framework');
        }
        $attr   .= 'placeholder="'.sprintf(esc_html__("Allowed only: '%s'.", 'cubewp-framework'), $accept).'"';
		$output .= '<div class="cwp-custom-field cwp-upload-field">
            <div class="cwp-field">
                <input type="text" class="' . $class . '" id="' . $args['id'] . '" value="' . wp_get_attachment_url($args['value']) . '" readonly="readonly" ' . $attr . ' />
                <input type="hidden" name="' . $input_name . '" value="' . $args['value'] . '"/>
                <a href="javascript:void(0);" class="button cwp-file-upload-button" data-allowed-types="' . $accept_types . '">' . esc_html__('Insert File', 'cubewp-framework') . '</a>
                <a href="javascript:void(0);" class="button cwp-remove-upload-button" ' . $remove_button . '><span class="dashicons dashicons-trash"></span></a>
            </div>
        </div>';
		$output .= $this->cwp_field_wrap_end($args);

		return apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
	}
}
new CubeWp_Admin_File_Field();