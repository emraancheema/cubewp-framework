<?php

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CubeWp_Tag_User extends Data_Tag {

	public function get_name() {
		return 'cubewp-user-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (user relation)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
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

	protected function register_controls() {
		$options = get_fields_by_type( array( 'user' ) );
		$this->add_control( 'user_selected_field', [
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select custom field', 'cubewp-framework' ),
			'options' => $options,
		] );
		$this->add_control( 'content_type', [
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Select content type', 'cubewp-framework' ),
			'options'   => [
				'full'   => esc_html__( 'User Box', 'cubewp-framework' ),
				'name' => esc_html__( 'Display Name', 'cubewp-framework' ),
				'link' => esc_html__( 'Profile Link', 'cubewp-framework' ),
				'avatar' => esc_html__( 'Avatar', 'cubewp-framework' ),
			],
			'default'   => 'name',
			'condition' => array(
				'user_selected_field!' => '',
			),
		] );
	}

	public function get_value( $options = array() ) {
		$field = $this->get_settings( 'user_selected_field' );

		if ( ! $field ) {
			return '';
		}
		$content_type = $this->get_settings( 'content_type' );

		if ( ! $content_type ) {
			return '';
		}

		$value = get_field_value( $field );
		if ( ! $value ) {
			return '';
		}

		if ( $content_type == 'full' ) {
			return get_user_details( $value );
		}else {
			if ( is_array( $value ) ) {
				foreach ( $value as $val ) {
					$return = self::get_output( $content_type, $val );
					if ( ! empty( $return ) ) {
						return $return;
					}
				}

				return '';
			} else {
				return self::get_output( $content_type, $value );
			}
		}
	}

	private static function get_output( $content_type, $value ) {
		$user_data = get_userdata($value);
		if ( $content_type == 'name' ) {
			return $user_data->display_name;
		} else if ( $content_type == 'link' ) {
			return get_author_posts_url($user_data->ID);
		} else if ( $content_type == 'avatar' ) {
			return [
				'id' => $user_data->ID,
				'url' => get_avatar_url( $user_data->ID ),
			];
		}

		return '';
	}
}