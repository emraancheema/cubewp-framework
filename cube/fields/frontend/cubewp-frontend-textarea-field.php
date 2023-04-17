<?php
/**
 * CubeWp admin textarea field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Textarea_Field
 */
class CubeWp_Textarea_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/textarea/field', array($this, 'render_textarea_field'), 10, 2);
        
        add_filter('cubewp/user/registration/textarea/field', array($this, 'render_textarea_field'), 10, 2);
        add_filter('cubewp/user/profile/textarea/field', array($this, 'render_textarea_field'), 10, 2);
    }
        
    /**
     * Method render_textarea_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_textarea_field( $output = '', $args = array() ) {

        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);

            $output .= self::cwp_frontend_field_label($args);
            $input_attrs = array( 
                'id'           =>    $args['id'],
                'class'        =>    'form-control '. $args['class'].' '.$required,
                'name'         =>    !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                'value'        =>    $args['value'],
                'placeholder'  =>    $args['placeholder'],
                'extra_attrs'  =>    $args['extra_attrs']
            );
            if (isset($args['char_limit']) && ! empty($args['char_limit']) && is_numeric($args['char_limit'])) {
                $input_attrs['extra_attrs'] .= ' maxlength="' . $args['char_limit'] . '" ';
            }
            $output .= cwp_render_textarea_input( $input_attrs );

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Textarea_Field();