<?php
/**
 * CubeWp admin date picker field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Date_Picker_Field
 */
class CubeWp_Admin_Date_Picker_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/date_picker/field', array($this, 'render_date_picker_field'), 10, 2);
    }
    
       
    /**
     * Method render_date_picker_field
     *
     * @param string $output 
     * @param array $args 
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_date_picker_field( $output = '', $args = array() ) {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('cubewp-datepicker');

        $args   =  apply_filters( 'cubewp/admin/field/parametrs', $args );
        
        if($args['value'] != '' && is_numeric($args['value'])){
            $args['value'] = date(get_option('date_format'), $args['value']);
        }
        
        $output = $this->cwp_field_wrap_start($args);
            $output .= '<div class="cwp-date-picker">';
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
new CubeWp_Admin_Date_Picker_Field();