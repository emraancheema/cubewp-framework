<?php
/**
 * CubeWp admin dropdown field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Dropdown_Field
 */
class CubeWp_Frontend_Dropdown_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/dropdown/field', array($this, 'render_dropdown_field'), 10, 2);
        
        add_filter('cubewp/user/registration/dropdown/field', array($this, 'render_dropdown_field'), 10, 2);
        add_filter('cubewp/user/profile/dropdown/field', array($this, 'render_dropdown_field'), 10, 2);
        
        add_filter('cubewp/search_filters/dropdown/field', array($this, 'render_search_filters_dropdown_field'), 10, 2);
        add_filter('cubewp/frontend/search/dropdown/field', array($this, 'render_search_dropdown_field'), 10, 2);
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
        if( (isset($args['multi']) && $args['multi'] == true) || (isset($args['multiple']) && $args['multiple'] == 1)) {
            $args['not_formatted_value'] = $args['value'];
            $args['value']               = cwp_handle_data_format( $args );
        }
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $options        = cwp_convert_choices_to_array($args['options']);
        $output         = self::cwp_frontend_post_field_container($args);

            $output .= self::cwp_frontend_field_label($args);
                $input_attrs = array( 
                    'id'           => $args['id'],
                    'class'        => $args['class'] . ' ' . $required,
                    'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                    'value'        => $args['value'],
                    'placeholder'  => $args['placeholder'],
                    'options'      => isset($args['options']) ? $args['options'] : '',
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
     * Method render_search_filters_dropdown_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_filters_dropdown_field( $output = '', $args = array() ){
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $value   = isset($args['value']) ? $args['value'] : '';
        $args['value'] =  isset($value) ? explode(',', $value) : array();
       
        
        $options =  cwp_convert_choices_to_array($args['options']);
        if(isset($options) && !empty($options)){
            $output   = self::cwp_frontend_search_field_container($args);
                $output .= self::cwp_frontend_search_field_label($args);  
                $input_attrs = array( 
                    'id'           => !empty($args['id']) ? $args['id'] : $args['name'],
                    'class'        => $args['class'],
                    'name'         => $args['name'],
                    'value'        => $args['value'],
                    'placeholder'  => $args['placeholder'],
                    'options'      => isset($args['options']) ? $args['options'] : '',
                );
                if(isset($args['multiple']) && $args['multiple'] == 1){
                    unset($input_attrs['name']);
                    $input_attrs['class']  = ' multi-select';
                    $input_attrs['hidden_input'] = false;
                    $output .= cwp_render_multi_dropdown_input( $input_attrs );
                    $input_attrs = array( 
                        'name'         => $args['name'],
                        'value'        => $value,
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
     * Method render_search_dropdown_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_dropdown_field( $output = '', $args = array() ){
        
        $args    =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        
        $class = '';
        
        
        $options =  cwp_convert_choices_to_array($args['options']);
        if(isset($options) && !empty($options)){
            $output   = self::cwp_frontend_search_field_container($args);
                $output .= self::cwp_frontend_search_field_label($args);
              
                $input_attrs = array( 
                    'id'           => !empty($args['id']) ? $args['id'] : $args['name'],
                    'class'        => $args['class'],
                    'name'         => $args['name'],
                    'value'        => $args['value'],
                    'placeholder'  => $args['placeholder'],
                    'options'      => isset($args['options']) ? $args['options'] : '',
                );
                if( (isset($args['multi']) && $args['multi'] == true) || (isset($args['multiple']) && $args['multiple'] == 1)){
                    unset($input_attrs['name']);
                    $input_attrs['class']  = isset($args['multi']) ? $args['multi'].' multi-select' : $args['multiple'].' multi-select' ;
                    $input_attrs['hidden_input'] = false;
                    $output .= cwp_render_multi_dropdown_input( $input_attrs );
                    $input_attrs = array( 
                        'name'         => $args['name'],
                        'value'        => isset($args['value']) ? $args['value'] : '',
                    );
                    $output .= cwp_render_hidden_input( $input_attrs );
                }else{
                    $output .= cwp_render_dropdown_input( $input_attrs );
                }
                
            $output .= '</div>';
        }
        
        $output = apply_filters("cubewp/frontend/search/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Frontend_Dropdown_Field();