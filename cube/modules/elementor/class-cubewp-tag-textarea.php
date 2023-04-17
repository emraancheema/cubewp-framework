<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Textarea extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'cubewp-textarea-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (textarea)', 'cubewp-framework' );
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
        
		$options = get_fields_by_type(array('textarea'));

		$this->add_control(
			'user_selected_field',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Select custom field', 'cubewp-framework' ),
				'options' => $options,
			]
		);
	}

	public function render() {
		$field = $this->get_settings( 'user_selected_field' );
        
		if ( ! $field ) {
			return;
		}
        $value = get_field_value( $field );
		echo cubewp_core_data($value);
	}
    

}