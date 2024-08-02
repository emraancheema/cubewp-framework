<?php
/**
 * CubeWp admin wysiwyg editor field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Wysiwyg_Editor_Field
 */
class CubeWp_Admin_Wysiwyg_Editor_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/wysiwyg_editor/field', array($this, 'render_wysiwyg_editor_field'), 10, 2);
    }
        
    /**
     * Method render_wysiwyg_editor_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_wysiwyg_editor_field( $output = '', $args = array() ) {
        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
	    $args['container_attrs'] = $args['container_attrs'] ?? '';
	    if (isset($args['required']) && $args['required'] == 1) {
		    $args['container_class'] .= ' required';
		    $validation_msg          = isset($args['validation_msg']) ? $args['validation_msg'] : '';
		    $args['container_attrs']     .= ' data-validation_msg="' . $validation_msg . '" ';
	    }
        $output = $this->cwp_field_wrap_start($args);
            $input_attrs = array( 
                'id'           => $args['id'],
                'class'        => $args['class'],
                'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                'value'        => $args['value'],
                'placeholder'  => $args['placeholder'],
                'editor_media' => $args['editor_media'],
            );
            $output .= cwp_render_editor_input( $input_attrs );
        $output .= $this->cwp_field_wrap_end($args);
        $output =  apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Admin_Wysiwyg_Editor_Field();