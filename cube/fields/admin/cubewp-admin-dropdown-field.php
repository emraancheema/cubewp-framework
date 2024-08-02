<?php
/**
 * CubeWp admin dropdown field
 *
 * @version 1.0
 * @package cubewp/cube/fields/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Admin_Dropdwon_Field
 */
class CubeWp_Admin_Dropdwon_Field extends CubeWp_Admin {

	public function __construct( ) {
		add_filter('cubewp/admin/post_type/dropdown/field', array($this, 'render_dropdwon_field'), 10, 2);
		add_filter('cubewp/admin/taxonomy/dropdown/field', array($this, 'render_dropdwon_field'), 10, 2);
		add_filter('cubewp/admin/post/dropdown/field', array($this, 'render_dropdwon_field'), 10, 2);
		add_filter('cubewp/admin/dashboard/dropdown/field', array($this, 'render_dropdwon_field'), 10, 2);

		add_filter('cubewp/admin/dropdown/customfield', array($this, 'render_dropdown_custom_field'), 10, 2);
		add_filter('cubewp/admin/taxonomies/dropdown/customfield', array($this, 'render_dropdown_custom_field'), 10, 2);

		add_filter('cubewp/admin/options/customfield', array($this, 'render_options_custom_field'), 10, 2);
	}

	/**
	 * Method render_dropdwon_field
	 *
	 * @param string $output
	 * @param array $args
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_dropdwon_field($output = '', $args = array()) {
		$args  = apply_filters('cubewp/admin/field/parametrs', $args);
		$class = 'cwp-select';
		if( (isset($args['multi']) && $args['multi'] == true) || (isset($args['multiple']) && $args['multiple'] == 1)) {
			$args['not_formatted_value'] = $args['value'];
			$args['value']               = cwp_handle_data_format( $args );
		}
		if (isset($args['select2_ui']) && $args['select2_ui'] == 1) {
			wp_enqueue_style('select2');
			wp_enqueue_script('select2');
			$class = 'cwp-select2';
		}
		$output      = $this->cwp_field_wrap_start($args);
		$output      .= '<div class="' . esc_attr($class) . '">';
		$input_attrs = array(
			'options'     => isset($args['options']) ? $args['options'] : '',
			'id'          => $args['id'],
			'class'       => $args['class'],
			'name'        => ! empty($args['custom_name']) ? $args['custom_name'] : $args['name'],
			'value'       => $args['value'],
			'placeholder' => $args['placeholder']
		);
		$extra_attrs = isset($args['extra_attrs']) ? $args['extra_attrs'] : '';
		if (isset($args['required']) && $args['required'] == 1) {
			$input_attrs['class'] .= ' required';
			$validation_msg       = isset($args['validation_msg']) ? $args['validation_msg'] : '';
			$extra_attrs          .= ' data-validation_msg="' . $validation_msg . '" ';
		}
		$input_attrs['extra_attrs'] = $extra_attrs;
		if (isset($args['multiple']) && $args['multiple'] == 1) {
			$output .= cwp_render_multi_dropdown_input($input_attrs);
		} else {
			$output .= cwp_render_dropdown_input($input_attrs);
		}
		$output .= '</div>';
		$output .= $this->cwp_field_wrap_end($args);

		return apply_filters("cubewp/admin/{$args['name']}/field", $output, $args);
	}

	/**
	 * Method render_dropdown_custom_field
	 *
	 * @param string $output
	 * @param array $FieldData
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_dropdown_custom_field($output = '', $FieldData = array()){
		if(isset($FieldData['tr_class'])){
			$output = '<tr class="'.$FieldData['tr_class'].'" '.$FieldData['tr_extra_attr'].' >';
		}else{
			$output = self::cwp_tr_start($FieldData);
		}

		$tooltip = isset($FieldData['tooltip']) && !empty($FieldData['tooltip']) ? $FieldData['tooltip'] : '';
		$required = isset($FieldData['required']) && !empty($FieldData['required']) ? $FieldData['required'] : '';
		$output .= self::cwp_td_start().self::cwp_label( $FieldData['id'], $FieldData['label'], $required, $tooltip ).self::cwp_td_end();
		$input_attrs = array(
			'type'         => isset($FieldData['type']) ? $FieldData['type'] : 'text',
			'options'      => isset($FieldData['options']) ? $FieldData['options'] : '',
			'id'           => $FieldData['id'],
			'class'        => $FieldData['class'],
			'option-class' => isset($FieldData['option-class']) ? $FieldData['option-class'] : '',
			'name'         => !empty($FieldData['custom_name']) ? $FieldData['custom_name'] : $FieldData['name'],
			'value'        => $FieldData['value'],
			'placeholder'  => $FieldData['placeholder'],
			'extra_attrs'  => isset($FieldData['extra_attrs']) && !empty($FieldData['extra_attrs']) ? $FieldData['extra_attrs'] : '',
		);

		$extra_attrs = isset($FieldData['extra_attrs']) ? $FieldData['extra_attrs'] : '';
		if(isset($FieldData['required']) && $FieldData['required'] == 1){
			$input_attrs['class'] .= ' required';
			$validation_msg = isset($FieldData['validation_msg']) ? $FieldData['validation_msg'] : '';
			$extra_attrs .= ' data-validation_msg="'. $validation_msg .'"';
		}
		$input_attrs['extra_attrs'] = $extra_attrs;
		$output .= self::cwp_td_start().cwp_render_dropdown_input( $input_attrs ).self::cwp_td_end();
		$output .= self::cwp_tr_end();
		return $output;

	}


	/**
	 * Method render_options_custom_field
	 *
	 * @param string $output
	 * @param array $FieldData
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	public function render_options_custom_field($output = '', $FieldData = array()){
		if(isset($FieldData['options']) && !empty($FieldData['options'])){
			$options = json_decode($FieldData['options'], true);
		}

		$output = '<tr class="'.$FieldData['tr_class'].'" '.$FieldData['tr_extra_attr'].' >';
		$output .= self::cwp_td_start().self::cwp_label( $FieldData['id'], $FieldData['label'], '', '' ).self::cwp_td_end();
		$output .= self::cwp_td_start();
		$output .= '<table class="field-options-table"><tbody><tr><th></th>
                        <th>'. esc_html__('Label', 'cubewp-framework') .'</th>
                        <th>'. esc_html__('Value', 'cubewp-framework') .'</th>
                    </tr>';
		if(isset($options['label']) && !empty($options['label'])){
			$options['label'] = array_filter($options['label']);
		}
		if(isset($options['value']) && !empty($options['value'])){
			$options['value'] = array_filter($options['value']);
		}

		if(isset($options['label']) && !empty($options['label'])){
			foreach($options['label'] as $key => $label){
				$value = isset($options['value'][$key]) ? $options['value'][$key] : '';
				if( $label || $value ){
					$checked = '';
					if( $value == $FieldData['default_value'] ){
						$checked = 'checked';
					}
					$output .= self::_option_field_process($FieldData,'sortable',$key,$value,$label,$checked);
				}
			}
		}else{
			$output .= self::_option_field_process($FieldData,'sortable');
		}
		$output .= self::_option_field_process($FieldData,'clone-option');
		$output .= '</tbody></table>';
		$output .= self::cwp_td_end();
		$output .= self::cwp_tr_end();
		return $output;
	}


	/**
	 * Method _option_field_process
	 *
	 * @param array $FieldData
	 * @param string $tr_class
	 * @param array $value
	 * @param string $label
	 * @param string $checked
	 *
	 * @return string html
	 * @since  1.0.0
	 */
	private static function _option_field_process($FieldData,$tr_class = '',$key = 0,$value='',$label='',$checked=''){
		$output = '<tr class="'.$tr_class.'">';
		$output .= '<td class="move-option"><span class="dashicons dashicons-move"></span></td>';
		$label_arg = array(
			'type'              => 'text',
			'name'              => $FieldData['name'].'[options][label][]',
			'class'             => 'option-label',
			'value'             => $label,
		);
		$value_arg = array(
			'type'              => 'text',
			'name'              => $FieldData['name'].'[options][value][]',
			'class'             => 'option-value',
			'value'             => $value,
		);

		$output .= self::cwp_td_start().cwp_render_text_input($label_arg).self::cwp_td_end();
		$output .= self::cwp_td_start().cwp_render_text_input($value_arg).self::cwp_td_end();
		$output .= self::_extra_options($FieldData, $key);


		$output .= self::cwp_td_start();
		$output .= '<a class="remove-option" href="javascript:void(0);"><span class="dashicons dashicons-minus"></span></a>';
		$output .= '<a class="add-option" href="javascript:void(0);"><span class="dashicons dashicons-plus"></span></a>';
		$output .= self::cwp_td_end();

		$output .= self::cwp_td_start();
		$output .='<input class="default-option" name="'.$FieldData['name'].'[default_option]" type="radio" value="'. $value .'" '. $checked .'><label>'. esc_html__('Set as Default', 'cubewp-framework') .'</label>';
		$output .= self::cwp_td_end();
		$output .= '</tr>';
		return $output;
	}

	private static function _extra_options($FieldData = array(),$key = ''){
		if(isset($FieldData['options']) && !empty($FieldData['options'])){
			$options = json_decode($FieldData['options'], true);
			unset($options['label']);
			unset($options['value']);
		}
		$output = '';
		if(!empty($options)){
			foreach($options as $name=>$value){
				$type = isset($FieldData[$name.'_type']) && !empty($FieldData[$name.'_type']) ? $FieldData[$name.'_type'] : 'text';
				$value = isset($options[$name][$key]) ? $options[$name][$key] : '';
				$icon_arg = array(
					'type'              => $type,
					'name'              => $FieldData['name'].'[options]['.$name.'][]',
					'class'             => 'option-'.$name,
					'value'             => $value,
					'placeholder'       => isset($FieldData[$name.'_placeholder']) && !empty($FieldData[$name.'_placeholder']) ? $FieldData[$name.'_placeholder'] : '',
				);
				$output .= self::cwp_td_start().cwp_render_text_input($icon_arg).self::cwp_td_end();
			}
			return $output;
		}
	}
}
new CubeWp_Admin_Dropdwon_Field();