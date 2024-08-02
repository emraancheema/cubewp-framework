<?php
/**
 * CubeWp admin checkbox field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Checkbox_Field
 */
class CubeWp_Frontend_Checkbox_Field extends CubeWp_Frontend {

    public function __construct( ) {
        add_filter('cubewp/frontend/checkbox/field', array($this, 'render_checkbox_field'), 10, 2);

        add_filter('cubewp/user/registration/checkbox/field', array($this, 'render_checkbox_field'), 10, 2);
        add_filter('cubewp/user/profile/checkbox/field', array($this, 'render_checkbox_field'), 10, 2);

        add_filter('cubewp/search_filters/checkbox/field', array($this, 'render_search_checkbox_field'), 10, 2);
        add_filter('cubewp/frontend/search/checkbox/field', array($this, 'render_search_checkbox_field'), 10, 2);
    }
    
    /**
     * Method render_checkbox_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_checkbox_field( $output = '', $args = array() ) {

        $args       =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options    =  cwp_convert_choices_to_array($args['options']);
        $args['not_formatted_value'] = $args['value'];
        $args['value'] = cwp_handle_data_format( $args );
        $required   = self::cwp_frontend_field_required($args['required']);
        $required   = !empty($required['class']) ? $required['class'] : '';
        $output     = self::cwp_frontend_post_field_container($args);
        $output .= '<div class="cwp-checkbox-container">';
                $output .= self::cwp_frontend_field_label($args);
                $output .= '<div class="cwp-field-checkbox-container">';
                if ( ! empty( $options ) && is_array( $options ) ) {
                    foreach($options as $value => $label){
                        $output .= '<div class="cwp-field-checkbox">';
                            $input_attrs = array(
                                'type'         =>  'checkbox',
                                'id'           =>  esc_attr($args['id'] . $label),
                                'name'         =>  !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
                                'value'        =>  $value,
                                'class'        =>  'custom-control-input form-control '. $args['class'].' '.$required,
                            );
                            if(isset($args['value']) && is_array($args['value']) && in_array($value, $args['value'])){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            } else if(isset($args['value']) && $args['value'] == $value){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            }
                            
                            $output .= cwp_render_text_input( $input_attrs );
                            $output .= '<label for="'. esc_attr($args['id'] . $label) .'">'. esc_html($label) .'</label>';
                        $output .= '</div>';
                    }
                    $output .= '<input type="hidden" name="' . $input_attrs['name'] . '" value="">';
                }
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);

        return $output;
    }
        
    /**
     * Method render_search_checkbox_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_checkbox_field( $output = '', $args = array() ) {

        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options        =  cwp_convert_choices_to_array($args['options']);
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_search_field_container($args);
        $args['values'] =  isset($args['value']) ? explode(',', $args['value']) : array();

        $output .= '<div class="cwp-checkbox-container">';
                $output .= self::cwp_frontend_search_field_label($args);
                $output .= '<ul class="cwp-field-checkbox-container">';
                    foreach($options as $value => $label){
                        $output .= '<li><div class="cwp-field-checkbox">';
                            $input_attrs = array(
                                'type'         =>  'checkbox',
                                'id'           =>  esc_attr($args['id'] . $label),
                                'name'         =>  !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
                                'value'        =>  $value,
                                'class'        =>  'custom-control-input form-control '. $args['class'].' '.$required,
                            );
                            if(isset($args['values']) && is_array($args['values']) && in_array($value, $args['values'])){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            } else if(isset($args['values']) && $args['values'] == $value){
                                $input_attrs['extra_attrs'] = ' checked="checked"';
                            }else if(is_tax()){
                                $queried_object = get_queried_object();
                                $CurrentSlug = $queried_object->slug;
                                if(isset($CurrentSlug) && $CurrentSlug == $value){
                                    $input_attrs['extra_attrs'] = ' checked="checked"';
                                    $args['value'] = $CurrentSlug;
                                }
                            }
                            
                            $output .= cwp_render_text_input( $input_attrs );
                            $output .= '<label for="'. esc_attr($args['id'] . $label) .'">'. esc_html($label) .'</label>';
                        $output .= '</div></li>';
                    }
                    $input_attrs = array( 
                        'name'         => $args['name'],
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
new CubeWp_Frontend_Checkbox_Field();