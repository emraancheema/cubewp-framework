<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CubeWp_Tag_Featured_Image extends \Elementor\Core\DynamicTags\Data_Tag {
    public function get_name() {
        return 'cubewp-featured-image-tag';
    }

    public function get_title() {
        return esc_html__( 'Featured Image', 'cubewp-framework' );
    }

    public function get_group() {
        return [ 'cubewp-single-fields' ];
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
        ];
    }

    public function is_settings_required() {
		return true;
	}

    protected function register_controls() {}

    public function get_value( $options = array() ){
        $returnArr = array();
        if (cubewp_is_elementor_editing()) {
            $imageID = get_post_thumbnail_id(cubewp_get_elementor_preview_post_id());
        }else {
            $imageID = get_post_thumbnail_id();
        }
        if(!empty($imageID)){
            $returnArr = [
                'id' =>$imageID,
                'url' => wp_get_attachment_image_src($imageID, 'full')[0],
            ]; 
        }
		return $returnArr;
	}
}