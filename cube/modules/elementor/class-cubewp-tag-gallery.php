<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Gallery extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'cubewp-gallery-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (gallery)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
	}

	public function get_categories() {
		return [ 
                \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
                \Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY,
                \Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY,
               ];
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
        
		$options = get_fields_by_type(array('gallery'));

		$this->add_control(
			'user_selected_field',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Select custom field', 'cubewp-framework' ),
				'options' => $options,
			]
		);
	}
    
    public function get_value( $options = array() ){
		$field = $this->get_settings( 'user_selected_field' );
        
		if ( ! $field ) {
			return;
		}
        $values = get_field_value( $field );
        $returnArr = array();
        if(is_array($values) && count($values)>0){
            foreach($values as $key=> $value ){
				if(get_post($value)){
                	$returnArr[$key] = [
                        'id' =>$value,
                        'url' => wp_get_attachment_image_src($value, 'full')[0],
                    ];
				}
            }
        }else{
            $imageID = attachment_url_to_postid($values);
            if(get_post($imageID)){
                $returnArr = [
                    'id' =>$imageID,
                    'url' => wp_get_attachment_image_src($imageID, 'full')[0],
                ]; 
            }
        }
		return $returnArr;
	}
    

}