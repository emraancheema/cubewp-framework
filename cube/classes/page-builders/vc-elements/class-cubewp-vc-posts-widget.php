<?php
defined( 'ABSPATH' ) || exit;

/**
 * CubeWP Posts Element.
 *
 * Visual Composer Element For Posts By CubeWP.
 *
 * @since 1.0.0
 */
class CubeWp_VC_Posts_Widget {

	private static $base_name = 'cubewp_post_element';
	private static $callback = 'cubewp_post_element_render';
	private static $category = 'CubeWP';

	public function __construct() {
		vc_map( self::element_option() );
		add_shortcode( self::$base_name, array($this, self::$callback) );
	}

	private static function element_option() {
		$params     = array();
		$post_types = self::get_post_types();
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "posttype",
			"heading"     => esc_html__( "Post Types", "cubewp-framework" ),
			'save_always' => true,
			'value'       => $post_types
		);

		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$additional_params = array();
			foreach ( $post_types as $post_type ) {
				$additional_params[] = self::add_taxonomy_controls( $post_type );
				$additional_params[] = self::add_posttype_controls( $post_type );
			}
			$additional_params = array_merge( ...$additional_params );
			$params            = array_merge( $params, $additional_params );
		}


		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "orderby",
			"heading"     => esc_html__( "Order By", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Most Recent', 'cubewp-framework' ) => 'date',
				esc_html__( 'Title', 'cubewp-framework' ) => 'title',
				esc_html__( 'Random', 'cubewp-framework' ) => 'rand' ,
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "order",
			"heading"     => esc_html__( "Order", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Descending', 'cubewp-framework' ) => 'DESC',
				esc_html__( 'Ascending', 'cubewp-framework' ) => 'ASC' ,
			),
			'dependency'  => array(
				'element' => 'orderby',
				'value'   => array( 'date', 'title' ),
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "posts_per_page",
			"heading"     => esc_html__( "Posts Per Page", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Show 3 Posts', 'cubewp-framework' ) => '3',
				esc_html__( 'Show 4 Posts', 'cubewp-framework' ) => '4',
				esc_html__( 'Show 5 Posts', 'cubewp-framework' ) => '5',
				esc_html__( 'Show 6 Posts', 'cubewp-framework' ) => '6',
				esc_html__( 'Show 8 Posts', 'cubewp-framework' ) => '8',
				esc_html__( 'Show 9 Posts', 'cubewp-framework' ) => '9',
				esc_html__( 'Show 12 Posts', 'cubewp-framework' ) => '12',
				esc_html__( 'Show 16 Posts', 'cubewp-framework' ) => '16',
				esc_html__( 'Show 15 Posts', 'cubewp-framework' ) => '15',
				esc_html__( 'Show 20 Posts', 'cubewp-framework' ) => '20',
				esc_html__( 'Show All Posts', 'cubewp-framework' ) => '-1'
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "layout",
			"heading"     => esc_html__( "Layout", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( 'Grid View', 'cubewp-framework' ) => 'grid',
				esc_html__( 'List View', 'cubewp-framework' ) => 'list'
			)
		);
		$params[]   = array(
			"type"        => "dropdown",
			"param_name"  => "column_per_row",
			"heading"     => esc_html__( "No Of Columns Per Row", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( '3 Columns Per Row', 'cubewp-framework' ) => '3',
				esc_html__( '1 Column Per Row', 'cubewp-framework' ) => '1',
				esc_html__( '2 Columns Per Row', 'cubewp-framework' ) => '2',
				esc_html__( '4 Columns Per Row', 'cubewp-framework' ) => '4',
				esc_html__( 'Auto Adjust Columns Per Row', 'cubewp-framework' ) => '0'
			),
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array( 'grid' ),
			)
		);

		return array(
			"name"     => esc_html__( "CubeWP Post's", "cubewp-framework" ),
			"base"     => self::$base_name,
			"category" => self::$category,
			"icon"     => null,
			"params"   => $params
		);
	}

	private static function get_post_types() {
		$post_types = cwp_post_types();
		unset( $post_types['elementor_library'] );
		unset( $post_types['e-landing-page'] );
		unset( $post_types['post'] );
		unset( $post_types['attachment'] );
		unset( $post_types['page'] );

		return $post_types;
	}

	private static function add_taxonomy_controls( $post_type ) {
		$return = array();

		$return[] = array(
			"type"        => "dropdown",
			"param_name"  => "posts_by-$post_type",
			"heading"     => esc_html__( "Show Posts", "cubewp-framework" ),
			'save_always' => true,
			'value'       => array(
				esc_html__( "By Taxonomy" ) => "taxonomy",
				esc_html__( "By IDs" )      => "post_ids"
			),
			'dependency'  => array(
				'element' => 'posttype',
				'value'   => array( $post_type ),
			)
		);

		$taxonomies = get_object_taxonomies( $post_type );
		$taxonomies = array_combine( $taxonomies, $taxonomies );
		if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
			$return[] = array(
				"type"        => "dropdown",
				"param_name"  => "taxonomy-$post_type",
				"heading"     => esc_html__( "Select Taxonomy", "cubewp-framework" ),
				'save_always' => true,
				'value'       => $taxonomies,
				'dependency'  => array(
					'element' => "posts_by-$post_type",
					'value'   => array( 'taxonomy' ),
				)
			);
			foreach ( $taxonomies as $taxonomy ) {
				$terms     = get_terms( array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				) );
				$terms_arr = array();
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$terms_arr[ $term->name ] = $term->slug;
					}
				}
				if ( ! empty( $terms_arr ) ) {
					$return[] = array(
						"type"        => "checkbox",
						"param_name"  => "terms-$taxonomy",
						"heading"     => esc_html__( "Select Terms", "cubewp-framework" ),
						'save_always' => true,
						'value'       => $terms_arr,
						'description' => esc_html__( 'Leave empty if you want to display all posts.', 'cubewp-framework' ),
						'dependency'  => array(
							'element' => "taxonomy-$post_type",
							'value'   => array( $taxonomy ),
						)
					);
				}
			}
		}

		return $return;
	}

	private static function add_posttype_controls( $post_type ) {
		$return = array();
		$posts  = self::get_post_type_posts( $post_type );
		if ( ! empty( $posts ) ) {
			$return[] = array(
				"type"        => "checkbox",
				"param_name"  => $post_type . "_post__in",
				"heading"     => esc_html__( "Please Select Posts", "cubewp-framework" ),
				'save_always' => true,
				'value'       => $posts,
				'description' => esc_html__( 'Leave empty if you want to display all posts.', 'cubewp-framework' ),
				'dependency'  => array(
					'element' => "posts_by-$post_type",
					'value'   => array( 'post_ids' ),
				)
			);
		}

		return $return;
	}

	private static function get_post_type_posts( $post_types ) {
		$query  = new CubeWp_Query( array(
		   'post_type'      => $post_types,
		   'posts_per_page' => - 1
		) );
		$posts  = $query->cubewp_post_query();
		$return = array();
		if ( $posts->have_posts() ) :
				while ( $posts->have_posts() ) : $posts->the_post();
					$return[ get_the_ID() ] = get_the_title() . ' [' . get_the_ID() . ']';
				endwhile;
			endif;
	 
		return $return;
	}

	public function cubewp_post_element_render( $settings ) {
		$posttype = $settings['posttype'];
		$taxonomies = isset($settings["taxonomy-$posttype"]) ? $settings["taxonomy-$posttype"] : array();
		$post__in = isset($settings[$posttype . "_post__in"]) ? $settings[$posttype . "_post__in"] : array();
		$args       = array(
			'posttype'       => $settings['posttype'],
			'taxonomy'       => $taxonomies,
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'posts_per_page' => $settings['posts_per_page'],
			'layout'         => $settings['layout'],
			'column_per_row' => isset($settings['column_per_row']) ? $settings['column_per_row'] : '',
			'post__in'       => $post__in
		);
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms                        = $settings[ 'terms-' . $taxonomy ];
				$args[ $taxonomy . '-terms' ] = $terms;
			}
		}

		echo apply_filters( 'cubewp_shortcode_posts_output','', $args );
	}
}