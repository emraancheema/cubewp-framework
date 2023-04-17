<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Repeating_Field extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'cubewp-repeating_field-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (repeating_field)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
	}

	public function get_categories() {
		return [ 
                \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
               ];
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
        
		$options = get_fields_by_type(array('repeating_field'));

		$this->add_control(
			'user_selected_field',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Select custom field', 'cubewp-framework'),
				'options' => $options,
			]
		);
	}

	public function render() {
		$field = $this->get_settings( 'user_selected_field' );
        
		if ( ! $field ) {
			return;
		}
		$options = get_field_options($field);
		$label = isset( $options["label"] ) ? $options["label"] : "";
		$field_type = isset($options['type']) ? $options['type'] : '';
        $value = get_field_value( $field );
		$args = array(
			'type'                  =>    $field_type,
			'container_class'       =>    "",
			'value'                 =>    $value,
			'label'                 =>    $label,
		);
		echo CubeWp_Single_Page_Trait::field_repeating_field($args);
	}
    

}