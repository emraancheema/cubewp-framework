<?php
/**
 * CubeWp admin datepicker field 
 *
 * @version 1.0
 * @package cubewp/cube/fields/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Frontend_Date_Picker_Field
 */
class CubeWp_Frontend_Date_Picker_Field extends CubeWp_Frontend {
    
    public function __construct( ) {
        add_filter('cubewp/frontend/date_picker/field', array($this, 'render_date_picker_field'), 10, 2);
        
        add_filter('cubewp/user/registration/date_picker/field', array($this, 'render_date_picker_field'), 10, 2);
        add_filter('cubewp/user/profile/date_picker/field', array($this, 'render_date_picker_field'), 10, 2);
        
        add_filter('cubewp/search_filters/date_picker/field', array($this, 'render_search_date_picker_field'), 10, 2);
        add_filter('cubewp/frontend/search/date_picker/field', array($this, 'render_search_date_picker_field'), 10, 2);
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

        $args           =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
        $required       = self::cwp_frontend_field_required($args['required']);
        $required       = !empty($required['class']) ? $required['class'] : '';
        $output         = self::cwp_frontend_post_field_container($args);

        if($args['value'] != '' && is_numeric($args['value'])){
            $args['value'] = date(get_option('date_format'), $args['value']);
        }
            $output .= self::cwp_frontend_field_label($args);
            $input_attrs = array( 
                'type'         =>    'text',
                'id'           =>    $args['id'],
                'class'        =>    'form-control '. $args['class'].' '.$required,
                'name'         =>    !empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
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
        
        $output =  apply_filters("cubewp/frontend/{$args['name']}/field", $output, $args);
        
        return $output;
    }

        
    /**
     * Method render_search_date_picker_field
     *
     * @param string $output
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_search_date_picker_field( $output = '', $args = array() ) {
	    wp_enqueue_style( 'cwp-jquery-ui' );
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('cubewp-datepicker');

	    $value = $args['value'] ?? '';
	    $value = explode('-', $value);
		$fromdate = $todate = '';
        if(isset($value[0])){
	        $fromdate = sanitize_text_field($value[0]);
        }
	    if(isset($value[1])){
	        $todate = sanitize_text_field($value[1]);
        }

        $args   =  apply_filters( 'cubewp/frontend/field/parametrs', $args );
	    $output = self::cwp_frontend_search_field_container($args);
	    $output .= self::cwp_frontend_search_field_label($args);
	    $output .= '<div class="cubewp-date-range-picker">'."\n";
	    $output .= '<div class="cubewp-date-range-picker-field">'."\n";
	    $output .= '<input value="' . $fromdate . '" type="cwp-date-range" class="cubewp-date-range-picker-from '. $args['class'].'" placeholder="'.esc_html__( 'From', 'cubewp-framework' ).'" id="'. esc_attr($args['name']) .'-from">'."\n";
	    $output .= '</div>'."\n";
	    $output .= '<span class="cubewp-date-range-picker-field-seprator"> - </span>'."\n";
	    $output .= '<div class="cubewp-date-range-picker-field">'."\n";
	    $output .= '<input value="' . $todate . '" type="cwp-date-range" class="cubewp-date-range-picker-to '. $args['class'].'" placeholder="'.esc_html__( 'To', 'cubewp-framework' ).'" id="'. esc_attr($args['name']) .'-to">'."\n";
	    $output .= '</div>'."\n";
	    $output .= '<input class="cubewp-date-range-picker-input" type="hidden" name="'. esc_attr($args['name']) .'" value="'. esc_attr($args['value']) .'">'."\n";
	    $output .= '</div>'."\n";
	    $output .= '</div>';
        $output =  apply_filters("cubewp/frontend/search/{$args['name']}/field", $output, $args);

        return $output;
    }
}
new CubeWp_Frontend_Date_Picker_Field();