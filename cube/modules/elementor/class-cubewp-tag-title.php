<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CubeWp_Tag_Title extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'cubewp-title-tag';
    }

    public function get_title() {
        return esc_html__( 'Post Title', 'cubewp-framework' );
    }

    public function get_group() {
        return [ 'cubewp-single-fields' ];
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        ];
    }

    public function is_settings_required() {
		return true;
	}

    protected function register_controls() {}

    public function render() {
        if (cubewp_is_elementor_editing()) {
            echo get_the_title(cubewp_get_elementor_preview_post_id());
        }else {
            echo get_the_title();
        }
    }
}