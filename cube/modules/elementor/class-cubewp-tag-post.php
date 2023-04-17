<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;


class CubeWp_Tag_Post extends Data_Tag {

	use CubeWp_Single_Page_Trait;

	public function get_name() {
		return 'cubewp-post-tag';
	}

	public function get_title() {
		return esc_html__( 'Fields type (post relation)', 'cubewp-framework' );
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
		$options = get_fields_by_type( array( 'post' ) );
		$this->add_control( 'user_selected_field', [
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select custom field', 'cubewp-framework' ),
			'options' => $options,
		] );
		$this->add_control( 'content_type', [
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Select content type', 'cubewp-framework' ),
			'options'   => [
				'full'    => esc_html__( 'Full Grid', 'cubewp-framework' ),
				'title'   => esc_html__( 'Post Title', 'cubewp-framework' ),
				'content' => esc_html__( 'Post Content', 'cubewp-framework' ),
				'image'   => esc_html__( 'Post Featured Image', 'cubewp-framework' ),
				'URL'     => esc_html__( 'Post URL', 'cubewp-framework' ),
				'author'  => esc_html__( 'Post Author Name', 'cubewp-framework' ),
				//'post_meta' => esc_html__( 'Post meta', 'cubewp-framework' ),
			],
			'default'   => 'title',
			'condition' => array(
				'user_selected_field!' => '',
			),
		] );
		$this->add_control( 'post_meta', [
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Meta Key', 'cubewp-framework' ),
			'description' => esc_html__( 'Put here post meta that is in relation with original post', 'cubewp-framework' ),
			'condition'   => array(
				'content_type'         => 'post_meta',
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
			$args = array('container_class' => '', 'label' => '', 'value' => $value);
			return $this->field_post( $args );
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
		if ( $content_type == 'title' ) {
			$value = get_the_title( $value );

			return cubewp_core_data( $value );
		} else if ( $content_type == 'content' ) {
			$value = get_the_content( null, false, $value );

			return cubewp_core_data( $value );
		} else if ( $content_type == 'URL' ) {
			$value = get_permalink( $value );

			return cubewp_core_data( $value );
		} else if ( $content_type == 'author' ) {
			$value = get_the_author_meta( "display_name", $value );

			return cubewp_core_data( $value );
		} else if ( $content_type == 'image' ) {
			$returnArr = array();
			$imageID   = get_post_thumbnail_id( $value );
			if ( $imageID ) {
				$returnArr = [
					'id'  => $imageID,
					'url' => wp_get_attachment_image_src( $imageID, 'full' )[0],
				];
			}

			return $returnArr;
		}

		return '';
	}
}