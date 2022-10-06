<?php
/**
 * CubeWp admin radio field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Radio_Field
 */
class CubeWp_Admin_Radio_Field extends CubeWp_Admin {
    public function __construct( ) {
        add_filter('cubewp/admin/post/radio/field', array($this, 'render_radio_field'), 10, 2);
    }
        
    /**
     * Method render_radio_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_radio_field($output = '', $args = array()) {
        $args = apply_filters('cubewp/admin/field/parametrs', $args);
        $args['extra_attrs'] = $args['extra_attrs'] ?? '';
        if (isset($args['required']) && $args['required'] == 1) {
            $args['container_class'] .= ' required';
            $validation_msg          = isset($args['validation_msg']) ? $args['validation_msg'] : '';
            $args['extra_attrs']     .= ' data-validation_msg="' . $validation_msg . '" ';
        }
        $output      = $this->cwp_field_wrap_start($args);
        $input_attrs = array(
            'options' => isset($args['options']) ? $args['options'] : '',
            'id'      => $args['id'],
            'class'   => $args['class'],
            'name'    => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
            'value'   => $args['value'],
            'extra_attrs' => $args['extra_attrs']
        );
        $output      .= cwp_render_radio_input($input_attrs);
        $output      .= $this->cwp_field_wrap_end($args);

        return apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
    }
}
new CubeWp_Admin_Radio_Field();