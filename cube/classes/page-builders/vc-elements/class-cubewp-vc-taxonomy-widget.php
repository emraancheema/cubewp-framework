<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWP Taxonomy Element.
 *
 * Visual Composer Element For Taxonomy By CubeWP.
 *
 * @since 1.0.0
 */
class CubeWp_VC_Taxonomy_Widget {
	private static $base_name = 'cubewp_taxonomy_element';
	private static $callback = 'cubewp_taxonomy_element_render';
	private static $category = 'CubeWP';

	public function __construct() {
		vc_map( self::element_option() );
		add_shortcode( self::$base_name, array( $this, self::$callback ) );
	}

	private static function element_option() {
		$params     = array();
		$args       = array(
			'public'   => true,
			'_builtin' => false
		);
		$taxonomies = get_taxonomies( $args );
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "taxonomy",
			"heading"     => esc_html__( "Select Taxonomy", "cubewp-framework" ),
			'save_always' => true,
			'value'       => $taxonomies
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "terms_per_page",
			"heading"     => esc_html__( "No Of Terms To Show", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Show All Terms', 'cubewp-framework' ) => '0',
				esc_html__( 'Show 3 Terms', 'cubewp-framework' ) => '3',
				esc_html__( 'Show 4 Terms', 'cubewp-framework' ) => '4',
				esc_html__( 'Show 5 Terms', 'cubewp-framework' ) => '5',
				esc_html__( 'Show 6 Terms', 'cubewp-framework' ) => '6',
				esc_html__( 'Show 8 Terms', 'cubewp-framework' ) => '8',
				esc_html__( 'Show 9 Terms', 'cubewp-framework' ) => '9',
				esc_html__( 'Show 12 Terms', 'cubewp-framework' ) => '12',
				esc_html__( 'Show 16 Terms', 'cubewp-framework' ) => '16',
				esc_html__( 'Show 15 Terms', 'cubewp-framework' ) => '15',
				esc_html__( 'Show 20 Terms', 'cubewp-framework' ) => '20',
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "output_style",
			"heading"     => esc_html__( "Select Output Style", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Boxed View', 'cubewp-framework' ) => 'boxed_view',
				esc_html__( 'List View', 'cubewp-framework' ) => 'list_view',
			)
		);
		$params[]   = array(
			"type"        => "textfield",
			"param_name"  => "icon_media_name",
			"heading"     => esc_html__( "Icon Or Image", "cubewp-framework" ),
			'description' => esc_html__( 'Enter taxonomy custom field slug for term icon or image.', 'cubewp-framework' ),
			'save_always' => true,
			'dependency'  => array(
				'element' => 'output_style',
				'value'   => array( "boxed_view" ),
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "column_per_row",
			"heading"     => esc_html__( "No Of Columns Per Row", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( '4 Columns Per Row', 'cubewp-framework' ) => '4',
				esc_html__( '1 Column Per Row', 'cubewp-framework' ) => '1',
				esc_html__( '2 Columns Per Row', 'cubewp-framework' ) => '2',
				esc_html__( '3 Columns Per Row', 'cubewp-framework' ) => '3',
				esc_html__( '6 Columns Per Row', 'cubewp-framework' ) => '6',
				esc_html__( 'Auto Adjust Columns Per Row', 'cubewp-framework' ) => '0',
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "child_terms",
			"heading"     => esc_html__( "Show Child Terms", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'No', 'cubewp-framework' ) => 'no',
				esc_html__( 'Yes', 'cubewp-framework' ) => 'yes',
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "hide_empty",
			"heading"     => esc_html__( "Hide Empty Terms", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'No', 'cubewp-framework' ) => 'no',
				esc_html__( 'Yes', 'cubewp-framework' ) => 'yes',
			)
		);
		$params[]   = array(
			"type"        => "textfield",
			"param_name"  => "terms_box_color",
			"heading"     => esc_html__( "Terms Box Colors", "cubewp-framework" ),
			'description' => esc_html__( 'Enter HEX color code for term boxes separated by ","', 'cubewp-framework' ),
			'save_always' => true,
			'value'       => "#faf7d9,#e1f0ee,#fcece3,#e3effb,#ffeff7",
			'dependency'  => array(
				'element' => 'output_style',
				'value'   => array( "boxed_view" ),
			)
		);

		return array(
			"name"     => esc_html__( "CubeWP Taxonomy", "cubewp-framework" ),
			"base"     => self::$base_name,
			"category" => self::$category,
			"icon"     => null,
			"params"   => $params
		);
	}

	public function cubewp_taxonomy_element_render( $settings ) {
		$terms_box_colors = explode(",", $settings['terms_box_color']);
		$terms_box_color = array();
		if (is_array($terms_box_colors) && !empty($terms_box_colors)) {
			foreach ($terms_box_colors as $color) {
				$terms_box_color[]["term_box_color"] = $color;
			}
		}
		$args     = array(
			'taxonomy'        => $settings['taxonomy'],
			'terms_per_page'  => $settings['terms_per_page'],
			'output_style'    => $settings['output_style'],
			'child_terms'     => $settings['child_terms'],
			'hide_empty'      => $settings['hide_empty'],
			'icon_media_name' => $settings['icon_media_name'],
			'column_per_row'  => $settings['column_per_row'],
			'terms_box_color' => $terms_box_color
		);

		echo apply_filters( 'cubewp_shortcode_taxonomy_output', '', $args );
	}
}