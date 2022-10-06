<?php
/**
 * CubeWp admin radio field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Radio_Field
 */
class CubeWp_Frontend_Radio_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/radio/field', array($this, 'render_radio_field'), 10, 2);
        
        add_filter('cubewp/user/registration/radio/field', array($this, 'render_radio_field'), 10, 2);
        add_filter('cubewp/user/profile/radio/field', array($this, 'render_radio_field'), 10, 2);
        
        add_filter('cubewp/search_filters/radio/field', array($this, 'render_search_radio_field'), 10, 2);
        add_filter('cubewp/frontend/search/radio/field', array($this, 'render_search_radio_field'), 10, 2);
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
    public function render_radio_field( $output = '', $args = array() ) {

        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options = cwp_convert_choices_to_array($args['options']);
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);
        
            $output .= '<div class="cwp-radio-container">';
                $output .= self::cwp_frontend_field_label($args);
                $output .= '<div class="cwp-field-radio-container">';
                    foreach($options as $value => $label){
                        $output .= '<div class="cwp-field-radio">';
                            $input_attrs = array(
                                'type'         =>  'radio',
                                'id'           =>  esc_attr($args['id'] . $label),
                                'name'         =>  !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                                'value'        =>  $value,
                                'class'        =>  'custom-control-input '. $args['class'].' '.$required,
                            );
                            if(isset($args['value']) && $args['value'] == $value){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            }
                            
                            $output .= cwp_render_text_input( $input_attrs );
                            $output .= '<label for="'. esc_attr($args['id'] . $label) .'">'. esc_html($label) .'</label>';
                        $output .= '</div>';
                    }
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        return $output;
    }
        
    /**
     * Method render_search_radio_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_radio_field( $output = '', $args = array() ) {

        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options = cwp_convert_choices_to_array($args['options']);
        $output      = self::cwp_frontend_search_field_container($args);
        
            $output .= '<div class="cwp-radio-container">';
                $output .= self::cwp_frontend_search_field_label($args);
                $output .= '<ul class="cwp-field-radio-container">';
                    foreach($options as $value => $label){
                        $output .= '<li><div class="cwp-field-radio">';
                            $input_attrs = array(
                                'type'         =>  'radio',
                                'id'           =>  esc_attr($args['id'] . $label),
                                'name'         =>  '',
                                'value'        =>  $value,
                                'class'        =>  'custom-control-input '. $args['class'],
                            );
                            if(isset($args['value']) && $args['value'] == $value){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            }
                            
                            $output .= cwp_render_text_input( $input_attrs );
                            $output .= '<label for="'. esc_attr($args['id'] . $label) .'">'. esc_html($label) .'</label>';
                        $output .= '</div></li>';
                    }
                    $input_attrs = array( 
                        'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                        'value'        => isset($args['value']) ? $args['value'] : '',
                    );
                    $output .= cwp_render_hidden_input( $input_attrs );
                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        return $output;
    }
    
}
new CubeWp_Frontend_Radio_Field();