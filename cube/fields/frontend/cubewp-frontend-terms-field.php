<?php
/**
 * CubeWp admin terms field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Terms_Field
 */
class CubeWp_Frontend_Terms_Field extends CubeWp_Frontend {

    public function __construct( ) {
        add_filter('cubewp/search_filters/checkbox/taxonomy/field', array($this, 'render_search_filters_checkbox_taxonomy_field'), 10, 2);
        add_filter('cubewp/search_filters/dropdown/taxonomy/field', array($this, 'render_search_filters_dropdown_taxonomy_field'), 10, 2);
        
        add_filter('cubewp/frontend/search/checkbox/taxonomy/field', array($this, 'render_search_checkbox_taxonomy_field'), 10, 2);
        add_filter('cubewp/frontend/search/dropdown/taxonomy/field', array($this, 'render_search_filters_dropdown_taxonomy_field'), 10, 2);
        add_filter('cubewp/frontend/checkbox/taxonomy/field', array($this, 'render_checkbox_taxonomy_field'), 10, 2);
        add_filter('cubewp/frontend/dropdown/taxonomy/field', array($this, 'render_dropdown_field'), 10, 2);
    }

        
    /**
     * Method render_checkbox_taxonomy_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_checkbox_taxonomy_field( $output = '', $args = array() ){

        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options        = cwp_convert_choices_to_array($args['options']);
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);
            $output .= '<div class="cwp-checkbox-container">';
                $output .= self::cwp_frontend_field_label($args);
                $output .= '<div class="cwp-field-checkbox-container">';
                    foreach($options as $value => $label){
                        if(!empty($label)){
                            $value = isset($label['term_id']) && !empty($label['term_id']) ? $label['term_id'] : 0;
                            $output .= '<div class="cwp-field-checkbox">';
                                $input_attrs = array(
                                    'type'         =>  'checkbox',
                                    'id'           =>  esc_attr($value),
                                    'name'         =>  !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
                                    'value'        =>  $value,
                                    'class'        =>  'custom-control-input '. $args['class'].' '.$required,
                                );
                                if(isset($args['value']) && is_array($args['value']) && in_array($value, $args['value'])){
                                    $input_attrs['extra_attrs'] = ' checked="checked"';
                                } else if(isset($args['value']) && $args['value'] == $value){
                                    $input_attrs['extra_attrs'] = ' checked="checked"';
                                }
                                
                                $output .= cwp_render_text_input( $input_attrs );
                                $output .= '<label for="'. esc_attr($value) .'">'. esc_html($label['term_name']) .'</label>';
                            $output .= '</div>';
                            if(isset($label['childern']) && !empty($label['childern'])){
                                foreach($label['childern'] as $value => $label){
                                    $value = $label['term_id'];
                                    $output .= '<div class="cwp-field-checkbox">';
                                        $input_attrs = array(
                                            'type'         =>  'checkbox',
                                            'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                            'name'         =>  !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
                                            'value'        =>  $value,
                                            'class'        =>  'custom-control-input '. $args['class'],
                                        );
                                        if(isset($args['value']) && is_array($args['value']) && in_array($value, $args['value'])){
                                            $input_attrs['extra_attrs'] = ' checked="checked"';
                                        } else if(isset($args['value']) && $args['value'] == $value){
                                            $input_attrs['extra_attrs'] = ' checked="checked"';
                                        }

                                        $output .= cwp_render_text_input( $input_attrs );
                                        $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                    $output .= '</div>';
                                    if(isset($label['childern']) && !empty($label['childern'])){
                                        foreach($label['childern'] as $value => $label){
                                            $value = $label['term_id'];
                                            $output .= '<div class="cwp-field-checkbox">';
                                                $input_attrs = array(
                                                    'type'         =>  'checkbox',
                                                    'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                                    'name'         =>  !empty($args['custom_name']) ? $args['custom_name'].'[]' : $args['name'],
                                                    'value'        =>  $value,
                                                    'class'        =>  'custom-control-input '. $args['class'],
                                                );
                                                if(isset($args['value']) && is_array($args['value']) && in_array($value, $args['value'])){
                                                    $input_attrs['extra_attrs'] = ' checked="checked"';
                                                } else if(isset($args['value']) && $args['value'] == $value){
                                                    $input_attrs['extra_attrs'] = ' checked="checked"';
                                                }

                                                $output .= cwp_render_text_input( $input_attrs );
                                                $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                            $output .= '</div>';

                                        }
                                    }
                                }
                            }
                        }
                    }
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);

        return $output;
        
    }
        
    /**
     * Method render_search_filters_checkbox_taxonomy_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_filters_checkbox_taxonomy_field( $output = '', $args = array() ){
        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options        = cwp_convert_choices_to_array($args['options']);
        $values         =  !empty($args['value']) ? explode(',', $args['value']) : $args['value'];
        $name           =  '';
        $output         = self::cwp_frontend_post_field_container($args);
        $args['custom_name']  =  !empty($args['name']) ? '_ST_'.$args['name'] : $args['name'];

            $output .= '<div class="cwp-search-field cwp-search-field-checkbox '. $args['container_class'] .'">';
                $output .= self::cwp_frontend_search_field_label($args);
                $output .= '<ul class="cwp-field-checkbox-container">';
                    if (is_array($options) && count($options) > 0) {
                        foreach($options as $value => $label){
                            if(!empty($label)){
                                $output .= '<li class="'. $args['class'] .'">';
                                $output .= '<div class="cwp-field-checkbox">';
                                    $input_attrs = array(
                                        'type'         =>  'checkbox',
                                        'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                        'name'         =>  $name,
                                        'value'        =>  $label['term_id'],
                                        'class'        =>  'custom-control-input '. $args['class'],
                                    );
                                    if(isset($args['value']) && is_array($values) && in_array($label['term_id'], $values)){
                                        $input_attrs['extra_attrs'] = ' checked="checked"';
                                    } else if(isset($args['value']) && $args['value'] == $label['term_id']){
                                        $input_attrs['extra_attrs'] = ' checked="checked"';
                                    }
                                    if(is_tax() && !is_search() && !is_page()){
                                        $queried_object = get_queried_object();
                                        if (is_object($queried_object) && !empty($queried_object) && !is_wp_error($queried_object)) {
                                            $CurrentSlug = $queried_object->term_id;
                                            if(isset($CurrentSlug) && $CurrentSlug == $label['term_id']){
                                                $input_attrs['extra_attrs'] = ' checked="checked"';
                                                $currentVal = $CurrentSlug;
                                            }
                                        }
                                    }
                                    $output .= cwp_render_text_input( $input_attrs );
                                    $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                $output .= '</div>';
                                $output .= '</li>';
                                if(isset($label['childern']) && !empty($label['childern'])){
                                    $output .= '<ul>';
                                    foreach($label['childern'] as $value => $label){
                                        $output .= '<li class="'. $args['class'] .'">';
                                        $output .= '<div class="cwp-field-checkbox">';
                                            $input_attrs = array(
                                                'type'         =>  'checkbox',
                                                'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                                'name'         =>  $name,
                                                'value'        =>  $label['term_id'],
                                                'class'        =>  'custom-control-input '. $args['class'],
                                            );
                                            if(isset($args['value']) && is_array($args['value']) && in_array($label['term_id'], $args['value'])){
                                                $input_attrs['extra_attrs'] = ' checked="checked"';
                                            } else if(isset($args['value']) && $args['value'] == $label['term_id']){
                                                $input_attrs['extra_attrs'] = ' checked="checked"';
                                            }
                                        

                                            $output .= cwp_render_text_input( $input_attrs );
                                            $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                        $output .= '</div>';
                                        $output .= '</li>';
                                        if(isset($label['childern']) && !empty($label['childern'])){
                                            $output .= '<ul>';
                                            foreach($label['childern'] as $value => $label){
                                                $output .= '<li class="'. $args['class'] .'">';
                                                $output .= '<div class="cwp-field-checkbox">';
                                                    $input_attrs = array(
                                                        'type'         =>  'checkbox',
                                                        'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                                        'name'         =>  $name,
                                                        'value'        =>  $label['term_id'],
                                                        'class'        =>  'custom-control-input '. $args['class'],
                                                    );
                                                    if(isset($args['value']) && is_array($args['value']) && in_array($label['term_id'], $args['value'])){
                                                        $input_attrs['extra_attrs'] = ' checked="checked"';
                                                    } else if(isset($args['value']) && $args['value'] == $label['term_id']){
                                                        $input_attrs['extra_attrs'] = ' checked="checked"';
                                                    }

                                                    $output .= cwp_render_text_input( $input_attrs );
                                                    $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                                $output .= '</div>';
                                                $output .= '</li>';
                                            }
                                            $output .= '</ul>';
                                        }
                                    }
                                    $output .= '</ul>';
                                }
                            }
                        }
                    }
                    $currentVal = isset($currentVal) ? $currentVal : '';
                    $input_attrs = array( 
                        'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                        'class'        => !empty($currentVal) ? 'is_tax': '',
                        'value'        => !empty($currentVal) ? $currentVal : $args['value'],
                        'extra_attrs'  => 'data-current-tax="'.$currentVal.'"',
                    );

                    $output .= cwp_render_hidden_input( $input_attrs );
                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);

        return $output;
        
    }
        
    /**
     * Method render_search_checkbox_taxonomy_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_checkbox_taxonomy_field( $output = '', $args = array() ){
        
        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $options        = cwp_convert_choices_to_array($args['options']);
        $name           =  '';
        $output         = self::cwp_frontend_search_field_container($args);
        $args['custom_name']  =  !empty($args['name']) ? '_ST_'.$args['name'] : $args['name'];

            $output .= '<div class="cwp-search-field cwp-search-field-checkbox '. $args['container_class'] .'">';
                $output .= self::cwp_frontend_search_field_label($args);
                $output .= '<ul class="cwp-field-checkbox-container">';
                    if (!empty($options) && (is_array($options) || is_object($options))) {
                        foreach($options as $value => $label){
                            if(!empty($label)){
                                $output .= '<li class="'. $args['class'] .'">';
                                $output .= '<div class="cwp-field-checkbox">';
                                    $input_attrs = array(
                                        'type'         =>  'checkbox',
                                        'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                        'name'         =>  $name,
                                        'value'        =>  $label['term_id'],
                                        'class'        =>  'custom-control-input '. $args['class'],
                                    );
                                    $output .= cwp_render_text_input( $input_attrs );
                                    $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                $output .= '</div>';
                                $output .= '</li>';
                                if(isset($label['childern']) && !empty($label['childern'])){
                                    $output .= '<ul>';
                                    foreach($label['childern'] as $value => $label){
                                        $output .= '<li class="'. $args['class'] .'">';
                                        $output .= '<div class="cwp-field-checkbox">';
                                            $input_attrs = array(
                                                'type'         =>  'checkbox',
                                                'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                                'name'         =>  $name,
                                                'value'        =>  $label['term_id'],
                                                'class'        =>  'custom-control-input '. $args['class'],
                                            );
                                            $output .= cwp_render_text_input( $input_attrs );
                                            $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                        $output .= '</div>';
                                        $output .= '</li>';
                                        if(isset($label['childern']) && !empty($label['childern'])){
                                            $output .= '<ul>';
                                            foreach($label['childern'] as $value => $label){
                                                $output .= '<li class="'. $args['class'] .'">';
                                                $output .= '<div class="cwp-field-checkbox">';
                                                    $input_attrs = array(
                                                        'type'         =>  'checkbox',
                                                        'id'           =>  esc_attr($args['id'] .' '. $label['term_name']),
                                                        'name'         =>  $name,
                                                        'value'        =>  $label['term_id'],
                                                        'class'        =>  'custom-control-input '. $args['class'],
                                                    );
                                                    $output .= cwp_render_text_input( $input_attrs );
                                                    $output .= '<label for="'. esc_attr($args['id'] .' '. $label['term_name']) .'">'. esc_html($label['term_name']) .'</label>';
                                                $output .= '</div>';
                                                $output .= '</li>';
                                            }
                                            $output .= '</ul>';
                                        }
                                    }
                                    $output .= '</ul>';
                                }
                            }
                        }
                    }
                    $input_attrs = array( 
                        'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                        'value'        => '',
                    );

                    $output .= cwp_render_hidden_input( $input_attrs );
                $output .= '</ul>';
            $output .= '</div>';
        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);

        return $output;
        
    }
        
    /**
     * Method render_dropdown_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_dropdown_field( $output = '', $args = array() ) {
        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $options        = cwp_convert_choices_to_array($args['options']);
        $output         = self::cwp_frontend_post_field_container($args);
            $output .= self::cwp_frontend_field_label($args);
                $input_attrs = array( 
                    'id'           => $args['id'],
                    'class'        => $args['class'].' '.$required,
                    'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                    'value'        => $args['value'],
                    'placeholder'  => !empty($args['placeholder']) ? $args['placeholder'] : esc_html__( 'Choose your option', 'cubewp-framework' ),
                    'options'      => isset($args['options']) ? self::cwp_sub_terms_dropdown_output_id($args['options']) : array(),
                    'extra_attrs'  => isset($args['extra_attrs']) ? $args['extra_attrs'] : '',
                    'select2_ui'   => isset($args['select2_ui']) ? $args['select2_ui'] : false
                );
                if( (isset($args['multi']) && $args['multi'] == true) || (isset($args['multiple']) && $args['multiple'] == 1)){
                    $output .= cwp_render_multi_dropdown_input( $input_attrs );
                }else{
                    $output .= cwp_render_dropdown_input( $input_attrs );
                }

        $output .= '</div>';

        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        return $output;
    }
         
    /**
     * Method render_search_filters_dropdown_taxonomy_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_filters_dropdown_taxonomy_field( $output = '', $args = array() ){
        
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
		
		if(is_tax() && !is_search() && !is_page()){
            $queried_object = get_queried_object();
            if (is_object($queried_object) && !empty($queried_object) && !is_wp_error($queried_object)) {
                $CurrentSlug = $queried_object->term_id;
                if(isset($CurrentSlug)){
                    $args['value'] = $CurrentSlug;
                }
            }
        }
		
        $values  =  !empty($args['value']) ? explode(',', $args['value']) : $args['value'];
        $args['custom_name']  =  !empty($args['name']) ? '_ST_'.$args['name'] : $args['name'];

        if(isset($args['options']) && !empty($args['options'])){
            $output  = self::cwp_frontend_search_field_container($args);
                $output .= self::cwp_frontend_search_field_label($args);
                $input_attrs = array( 
                    'id'           => !empty($args['id']) ? $args['id'] : $args['name'],
                    'class'        => $args['class'],
                    'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                    'value'        => $values,
                    'placeholder'  => !empty($args['placeholder']) ? $args['placeholder'] : esc_html__( 'Choose your option', 'cubewp-framework' ),
                    'options'      => isset($args['options']) ? self::cwp_sub_terms_dropdown_output_id($args['options']) : array(),
                );
                if( (isset($args['multi']) && $args['multi'] == true) || (isset($args['multiple']) && $args['multiple'] == 1)){
                    unset($input_attrs['name']);
                    $input_attrs['hidden_input'] = false;
                    $input_attrs['class']  = $args['class'].' '.$args['multi'].' multi-select';
                    $output .= cwp_render_multi_dropdown_input( $input_attrs );
                    $input_attrs = array( 
                        'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                        'value'        => isset($args['value']) ? $args['value'] : '',
                    );
                    $output .= cwp_render_hidden_input( $input_attrs );
                }else{
                    $output .= cwp_render_dropdown_input( $input_attrs );
                }
                
            $output .= '</div>';
        }
        
        $output = apply_filters("cubewp/search_filters/{$args['name']}/field", $output, $args);
        
        return $output;
    }
        
    /**
     * Method cwp_sub_terms_dropdown_output
     *
     * @param array $args
     *
     * @return array
     * @since  1.0.0
     */
    private function cwp_sub_terms_dropdown_output($args = array()){
        $output = array();
            foreach($args as $k => $v){
                    $output[$k] = $v['term_name'];
                foreach($v['childern'] as $k2 => $v2){
                    $output[$k2] = '-'.$v2['term_name'];
                    foreach($v2['childern'] as $k3 => $v3){
                        $output[$k3] = '--'.$v3['term_name'];
                    }
                }
            }
        return $output;
    }
        
    /**
     * Method cwp_sub_terms_dropdown_output_id
     *
     * @param array $args
     *
     * @return array
     * @since  1.0.0
     */
    private function cwp_sub_terms_dropdown_output_id($args = array()){
        $output = array();
            foreach($args as $k => $v){
                $output[$v['term_id']] = $v['term_name'];
                if ( isset( $v['childern'] ) && ! empty( $v['childern'] ) ) {
                    foreach($v['childern'] as $k2 => $v2){
                        $output[$v2['term_id']] = '-'.$v2['term_name'];
                        foreach($v2['childern'] as $k3 => $v3){
                            $output[$v3['term_id']] = '--'.$v3['term_name'];
                        }
                    }
                }
            }
        return $output;
    }

}
new CubeWp_Frontend_Terms_Field();