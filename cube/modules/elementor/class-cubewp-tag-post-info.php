<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

class CubeWp_Tag_Post_Info extends Tag {
	public function get_name() {
		return 'cubewp-post-info-tag';
	}

	public function get_title() {
		return esc_html__( 'Post Info', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-single-fields' ];
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	public function is_settings_required() {
		return true;
	}

	public function render() {
		$field = $this->get_settings( 'post_info_field' );
		if ( cubewp_is_elementor_editing() ) {
			$post_id = cubewp_get_elementor_preview_post_id();
		} else {
			$post_id = get_the_ID();
		}
		if ( $field == 'publish_date' ) {
			echo get_the_date( '', $post_id );
		} else if ( $field == 'publish_time' ) {
			echo get_the_time( '', $post_id );
		} else if ( $field == 'views' ) {
			echo get_post_meta( $post_id, 'cubewp_post_views', true );
		}
	}

	protected function register_controls() {
		$this->add_control( 'post_info_field', [
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Select Info Field', 'cubewp-framework' ),
				'options' => array(
					"publish_date" => esc_html__( "Publish Date", "cubewp-framework" ),
					"publish_time" => esc_html__( "Publish Time", "cubewp-framework" ),
					"views"        => esc_html__( "Post Views", "cubewp-framework" ),
				),
				'default' => 'publish_date'
			] );
	}
}