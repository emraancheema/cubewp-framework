<?php

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_Post_Share extends Tag {

	use CubeWp_Single_Page_Trait;

	public function get_name() {
		return 'cubewp-post-share-tag';
	}

	public function get_title() {
		return esc_html__( 'Post Share Button', 'cubewp-framework' );
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

		return $this->get_post_share_button( $post_id );
	}
}