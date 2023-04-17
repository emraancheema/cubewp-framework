<?php
/**
 * CubeWp admin password field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Password_Field
 */
class CubeWp_Frontend_Password_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/password/field', array($this, 'render_password_field'), 10, 2);
        
        add_filter('cubewp/user/registration/password/field', array($this, 'render_password_field'), 10, 2);
        add_filter('cubewp/user/profile/password/field', array($this, 'render_password_field'), 10, 2);
    }
        
    /**
     * Method render_text_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_password_field( $output = '', $args = array() ) {
        
        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);
        $output .= self::cwp_frontend_field_label($args);
        $input_attrs = array( 
            'type'         =>    !empty($args['type']) ? $args['type'] : 'password',
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
        $output .= cwp_render_text_input( $input_attrs );
        $output .= '<span class="dashicons dashicons-visibility show-password"></span>';

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);

        return $output;
    }
    
}
new CubeWp_Frontend_Password_Field();