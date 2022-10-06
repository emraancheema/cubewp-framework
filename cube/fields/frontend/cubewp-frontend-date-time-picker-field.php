<?php
/**
 * CubeWp admin date time picker field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Date_Time_Picker_Field
 */
class CubeWp_Frontend_Date_Time_Picker_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/date_time_picker/field', array($this, 'render_date_time_picker_field'), 10, 2);
        
        add_filter('cubewp/user/registration/date_time_picker/field', array($this, 'render_date_time_picker_field'), 10, 2);
        add_filter('cubewp/user/profile/date_time_picker/field', array($this, 'render_date_time_picker_field'), 10, 2);

    }
        
    /**
     * Method render_date_time_picker_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_date_time_picker_field( $output = '', $args = array() ) {
        
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('cwp-timepicker');
        wp_enqueue_style('cwp-timepicker');
        
        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);

        if($args['value'] != '' && is_numeric($args['value'])){
            $args['value'] = date(get_option('date_format') .' '. get_option('time_format'), $args['value']);
        }

            $output .= self::cwp_frontend_field_label($args);
            $input_attrs = array( 
                'type'         =>    'text',
                'id'           =>    $args['id'],
                'class'        =>    'form-control '. $args['class'].' '.$required,
                'name'         =>    '',
                'value'        =>    $args['value'],
                'placeholder'  =>    $args['placeholder'],
                'extra_attrs'  =>    'autocomplete="off"'
            );
            $output .= cwp_render_text_input( $input_attrs );

            $input_attrs = array( 
                'name'         => !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
                'value'        => $args['value']
            );
            $output .= cwp_render_hidden_input( $input_attrs );

        $output .= '</div>';
        
        $output = apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }
    
    
    
}
new CubeWp_Frontend_Date_Time_Picker_Field();