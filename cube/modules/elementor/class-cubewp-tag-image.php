<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Image extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'cubewp-image-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (image)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
	}

	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
		];
	}

	public function is_settings_required() {
		return true;
	}

	public function get_value( $options = array() ){
		$returnArr = array();
        $field = $this->get_settings( 'user_selected_field' );

		if ( ! $field ) {
			return;
		}
		$value = get_field_value( $field );
        if ( !$value || !is_numeric($value) ) {
			return;
		}
		$imageID = $value;
        if($imageID){
            $returnArr = [
                'id' =>$imageID,
                'url' => wp_get_attachment_image_src($imageID, 'full')[0],
            ]; 
        }
		return $returnArr;
	}

	protected function register_controls() {

		$options = get_fields_by_type( array( 'image' ) );

		$this->add_control( 'user_selected_field', [
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Select custom field', 'cubewp-framework' ),
				'options' => $options,
			] );
	}


}