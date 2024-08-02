<?php
/**
 * CubeWp admin checkbox conatins all functions 
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Checkbox_Field
 */
class CubeWp_Admin_Checkbox_Field extends CubeWp_Admin {
    
    public function __construct( ) {
        add_filter('cubewp/admin/post/checkbox/field', array($this, 'render_checkbox_field'), 10, 2);
        
        add_filter('cubewp/admin/conditional/customfield', array($this, 'render_conditional_custom_field'), 10, 2);
    }
	
	/**
	 * Method render_checkbox_field
	 *
	 * @param string $output
	 * @param array  $args 
	 *
	 * @return string html
     * @since  1.0.0
	 */
	public function render_checkbox_field($output = '', $args = array()) {
		$args = apply_filters('cubewp/admin/field/parametrs', $args);
		$args['extra_attrs'] = $args['extra_attrs'] ?? '';
        $args['not_formatted_value'] = $args['value'];
        $args['value'] = cwp_handle_data_format( $args );
		if (isset($args['required']) && $args['required'] == 1) {
			$args['container_class'] .= ' required';
			$validation_msg          = isset($args['validation_msg']) ? $args['validation_msg'] : '';
			$args['extra_attrs']     .= ' data-validation_msg="' . $validation_msg . '" ';
		}
		$output      = $this->cwp_field_wrap_start($args);
		$input_attrs = array(
			'options'     => isset($args['options']) ? $args['options'] : '',
			'id'          => $args['id'],
			'class'       => $args['class'],
			'name'        => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
			'value'       => $args['value'],
			'extra_attrs' => $args['extra_attrs']
		);
		$output      .= cwp_render_checkbox_input($input_attrs);
		$output      .= $this->cwp_field_wrap_end($args);
		$output = apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);

		return $output;
	}
        
    /**
     * Method render_conditional_custom_field
     *
     * @param string $output 
     * @param array $FieldData
     *
     * @return string html
     * @since  1.0.0
     */
    public function render_conditional_custom_field($output = '', $FieldData = array()){
        
        $output = '<tr class="'.$FieldData['tr_class'].'" >';  
        $output .= self::cwp_td_start().self::cwp_label( $FieldData['id'], $FieldData['label'], '', '' ).self::cwp_td_end();
        $output .= self::cwp_td_start();
        $output .= '<table><tbody>';
        $output .= '<tr>';  
        $output .= '<td class="conditional-rule-field">
                    <select name="'.$FieldData['name'].'" '.$FieldData['select_extra_attr'].'>
                    </select>';
        $output .= '<p>'.esc_html__('This dropdown will only show the previously saved fields.', 'cubewp-framework' ).'</p>';
        $output .= self::cwp_td_end();
        $output .= '<td class="conditional-rule-operator">';

        $dropdown_args = array(
            'id'                => '',
            'name'              => $FieldData['name_operator'],
            'placeholder'       => '',
            'class'             => '',
            'value'             => $FieldData['value_operator'],
            'options'           => $FieldData['options'],
            'extra_attrs'       => '',
        );        
        $output .= cwp_render_dropdown_input($dropdown_args);
        $output .= self::cwp_td_end();
        $output .= '<td class="conditional-rule-value">';
        $text_args = array(
            'type'              => 'text',
            'id'                => '',
            'name'              => $FieldData['name_value'],
            'placeholder'       => esc_html__('Put here value to compare', 'cubewp-framework' ),
            'class'             => '',
            'value'             => $FieldData['value_value'],
            'extra_attrs'       => '',
            'description'       => esc_html__('For user field you need to add user ID and for Date_picker you need to add date in strtotime.', 'cubewp-framework' )
        );
        $output .= cwp_render_text_input($text_args);
        $output .= self::cwp_field_description($text_args);
        $output .= self::cwp_td_end();
        $output .= self::cwp_tr_end();
        $output .= '</tbody></table>';
        $output .= self::cwp_td_end();
        $output .= self::cwp_tr_end();
        return $output;
    }
    
}
new CubeWp_Admin_Checkbox_Field();