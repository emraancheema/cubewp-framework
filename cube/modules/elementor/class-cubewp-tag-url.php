<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Url extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'cubewp-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (url)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
	}

	public function get_categories() {
		return [ 
                \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
               ];
	}

	protected function register_controls() {
        
		$options = get_fields_by_type(array('url'));

		$this->add_control(
			'user_selected_field',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Select custom field', 'cubewp-framework' ),
				'options' => $options,
			]
		);
	}

	public function is_settings_required() {
		return true;
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