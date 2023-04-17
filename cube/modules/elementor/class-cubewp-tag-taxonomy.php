<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;


class CubeWp_Tag_Taxonomy extends Data_Tag {

	use CubeWp_Single_Page_Trait;

	public function get_name() {
		return 'cubewp-term-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (taxonomy relation)', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-fields' ];
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::URL_CATEGORY,
		];
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$options = get_fields_by_type( array( 'taxonomy' ) );
		$this->add_control( 'user_selected_field', [
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select custom field', 'cubewp-framework' ),
			'options' => $options,
		]);
		$this->add_control( 'content_type', [
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Select content type', 'cubewp-framework' ),
			'options'   => [
				'full'    => esc_html__( 'Full Grid', 'cubewp-framework' ),
				'title'   => esc_html__( 'Term Name', 'cubewp-framework' ),
				'link' 	  => esc_html__( 'Term Link', 'cubewp-framework' ),
			],
			'default'   => 'title',
			'condition' => array(
				'user_selected_field!' => '',
			),
		]);
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
			$args = array('container_class' => '', 'label' => '', 'value' => $value);
			return $this->field_terms( $args );
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
		$term = get_term($value);
		if ( $content_type == 'title' ) {
			return $term->name;
		} else if ( $content_type == 'link' ) {
			return get_term_link( $term->term_id );
		}

		return '';
	}
}