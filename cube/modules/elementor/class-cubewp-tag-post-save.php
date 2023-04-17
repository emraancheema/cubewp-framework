<?php

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Post_Save extends Tag {

	public function get_name() {
		return 'cubewp-post-save-tag';
	}

	public function get_title() {
		return esc_html__( 'Post Save Button', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-single-fields' ];
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY
		];
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {}

	public function render() {
		if ( cubewp_is_elementor_editing() ) {
			$post_id = cubewp_get_elementor_preview_post_id();
		} else {
			$post_id = get_the_ID();
		}
		CubeWp_Single_Cpt::$post_id = $post_id;
		return CubeWp_Single_Cpt::get_post_save_button();
	}
}