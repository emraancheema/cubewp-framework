<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

class CubeWp_Tag_Post_Author extends Data_Tag {
	public function get_name() {
		return 'cubewp-post-author-tag';
	}

	public function get_title() {
		return esc_html__( 'Post Author', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-single-fields' ];
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::URL_CATEGORY,
			Module::IMAGE_CATEGORY,
		];
	}

	public function is_settings_required() {
		return true;
	}

	public function get_value( $options = array() ) {
		$field = $this->get_settings( 'post_author_field' );
		if ( cubewp_is_elementor_editing() ) {
			$post_id = cubewp_get_elementor_preview_post_id();
		} else {
			$post_id = get_the_ID();
		}
		$post_author_id   = get_post_field( 'post_author', $post_id );
		$post_author_data = get_userdata( $post_author_id );
		if ( $field == 'display_name' ) {
			return $post_author_data->display_name;
		} else if ( $field == 'profile_url' ) {
			return get_author_posts_url( $post_author_data->ID );
		} else if ( $field == 'avatar' ) {
			return [
				'id'  => $post_author_data->ID,
				'url' => get_avatar_url( $post_author_data->ID ),
			];
		} else if ( $field == 'custom_meta' ) {
			$field = $this->get_settings( 'post_author_custom_field' );
			if ( ! $field ) {
				return '';
			}

			return get_user_meta( $post_author_data->ID, $field, true );
		}

		return '';
	}

	protected function register_controls() {
		$this->add_control( 'post_author_field', [
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Select Author Field', 'cubewp-framework' ),
				'options' => array(
					"display_name" => esc_html__( "Display Name", "cubewp-framework" ),
					"avatar"       => esc_html__( "Avatar", "cubewp-framework" ),
					"profile_url"  => esc_html__( "Profile URL", "cubewp-framework" ),
					"custom_meta"  => esc_html__( "Custom Meta", "cubewp-framework" )
				),
				'default' => 'display_name'
			] );
		$this->add_control( 'post_author_custom_field', [
				'type'      => Controls_Manager::TEXT,
				'label'     => esc_html__( 'Custom Meta Field ID', 'cubewp-framework' ),
				'condition' => array(
					"post_author_field" => "custom_meta",
				),
			] );
	}

}