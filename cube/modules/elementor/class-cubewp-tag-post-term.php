<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

class CubeWp_Tag_Post_Term extends Data_Tag {
	public function get_name() {
		return 'cubewp-post-term-tag';
	}

	public function get_title() {
		return esc_html__( 'Post Term', 'cubewp-framework' );
	}

	public function get_group() {
		return [ 'cubewp-single-fields' ];
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::URL_CATEGORY
		];
	}

	public function is_settings_required() {
		return true;
	}

	public function get_value( $options = array() ) {
		$taxonomy = $this->get_settings( 'post_taxonomy' );
		$field    = $this->get_settings( 'post_term_field' );
		$post_id  = self::get_post_id();
		$terms    = wp_get_post_terms( $post_id, $taxonomy );
		if ( $terms && ! is_wp_error( $terms ) ) {
			if ( $field == 'term_name' ) {
				return $terms[0]->name;
			} else if ( $field == 'term_url' ) {
				return get_term_link( $terms[0]->term_id );
			} else if ( $field == 'custom_meta' ) {
				$field = $this->get_settings( 'post_term_custom_field' );
				if ( ! $field ) {
					return '';
				}

				return get_term_meta( $terms[0]->term_id, $field, true );
			} else if ( $field == 'all_terms' ) {
				$output = '';
				foreach ( $terms as $key => $term ) {
					if ( $key != 0 ) {
						$output .= ', ';
					}
					$output .= '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '">' . esc_html( $term->name ) . '</a>';
				}

				return $output;
			}
		}

		return '';
	}

	protected function register_controls() {
		$this->add_control( 'post_taxonomy', [
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Select Term Field', 'cubewp-framework' ),
				'options' => self::get_post_type_tax()
			] );
		$this->add_control( 'post_term_field', [
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__( 'Select Term Field', 'cubewp-framework' ),
				'options'   => array(
					"term_name"   => esc_html__( "Term Name", "cubewp-framework" ),
					"term_url"    => esc_html__( "Term URL", "cubewp-framework" ),
					"custom_meta" => esc_html__( "Custom Term Meta", "cubewp-framework" ),
					"all_terms"   => esc_html__( "All Selected Terms", "cubewp-framework" )
				),
				'default'   => 'term_name',
				'condition' => array(
					'post_taxonomy!' => ''
				)
			] );
		$this->add_control( 'post_term_custom_field', [
				'type'      => Controls_Manager::TEXT,
				'label'     => esc_html__( 'Custom Meta Field ID', 'cubewp-framework' ),
				'condition' => array(
					'post_taxonomy!'  => '',
					"post_term_field" => "custom_meta",
				),
			] );
	}

	private static function get_post_type_tax() {
		$post_id         = self::get_post_id();
		$post_type       = get_post_type( $post_id );
		$post_taxonomies = get_object_taxonomies( $post_type );
		$return          = array();
		foreach ( $post_taxonomies as $taxonomy ) {
			$return[ $taxonomy ] = $taxonomy;
		}

		return $return;
	}

	private static function get_post_id() {
		if ( cubewp_is_elementor_editing() ) {
			return cubewp_get_elementor_preview_post_id();
		} else {
			return get_the_ID();
		}
	}

}