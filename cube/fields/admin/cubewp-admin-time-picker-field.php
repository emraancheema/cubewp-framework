<?php
/**
 * CubeWp admin time picker field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Admin_Time_Picker_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/time_picker/field', array($this, 'render_time_picker_field'), 10, 2);
    }
    
   
        
    /**
     * Method render_time_picker_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_time_picker_field( $output = '', $args = array() ) {

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('cwp-timepicker');
        wp_enqueue_style('cwp-timepicker');

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        
        if($args['value'] != '' && is_numeric($args['value'])){
            $args['value'] = date(get_option('time_format'), $args['value']);
        }
        
        $output = $this->cwp_field_wrap_start($args);
            $output .= '<div class="cwp-time-picker">';
                $input_attrs = array( 
                    'id'           => $args['id'],
                    'class'        => $args['class']. '',
                    'name'         => '',
                    'value'        => $args['value'],
                    'placeholder'  => $args['placeholder']
                );
                $extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
                if(isset($args['required']) && $args['required'] == 1){
                    $input_attrs['class'] .= ' required';
                    $validation_msg = isset($args['validation_msg']) ? $args['validation_msg'] : '';
                    $extra_attrs .= ' data-validation_msg="'. $validation_msg .'"';
                }
                $input_attrs['extra_attrs'] = $extra_attrs;

                $output .= cwp_render_text_input( $input_attrs );

                $input_attrs = array( 
                    'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                    'value'        => $args['value']
                );
                $output .= cwp_render_hidden_input( $input_attrs );
            
            $output .= '</div>';
        $output .= $this->cwp_field_wrap_end($args);
        
        $output =  apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
}
new CubeWp_Admin_Time_Picker_Field();