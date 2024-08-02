<?php
/**
 * CubeWp admin whsiwyg editor field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Frontend_Wysiwyg_Editor_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        
        add_filter('cubewp/frontend/wysiwyg_editor/field', array($this, 'render_wysiwyg_editor_field'), 10, 2);
        
        add_filter('cubewp/user/registration/wysiwyg_editor/field', array($this, 'render_wysiwyg_editor_field'), 10, 2);
        add_filter('cubewp/user/profile/wysiwyg_editor/field', array($this, 'render_wysiwyg_editor_field'), 10, 2);
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
                'editor_media' =>    $args['editor_media']
            );
            
            $output .= cwp_render_editor_input( $input_attrs );

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Frontend_Wysiwyg_Editor_Field();