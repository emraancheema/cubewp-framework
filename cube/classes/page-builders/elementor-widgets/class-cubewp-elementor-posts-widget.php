<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * CubeWP Posts Widgets.
 *
 * Elementor Widget For Posts By CubeWP.
 *
 * @since 1.0.0
 */
class CubeWp_Elementor_Posts_Widget extends Widget_Base {

	private static $post_types = array();

	public function get_name() {
		return 'cubewp_posts';
	}

	public function get_title() {
		return esc_html__( 'CubeWP Posts', 'cubewp-framework' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'cubewp' );
	}

	public function get_keywords() {
		return array(
			'cubewp',
			'featured',
			'elements',
			'widgets',
			'terms',
			'taxonomy',
			'category',
			'categories',
			'term',
			'taxonomies',
			'posts',
			'post',
			'archive',
			'locations'
		);
	}

	protected function register_controls() {
		self::get_post_types();
		$post_types = self::$post_types;
		$this->start_controls_section( 'cubewp_widgets_section', array(
			'label' => esc_html__( 'Widget Options', 'cubewp-framework' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'posttype', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Post Type', 'cubewp-framework' ),
			'options' => $post_types,
		) );
		if(class_exists('CubeWp_Booster_Load')){
            $this->add_control( 'show_boosted_posts', array(
                'type'      => Controls_Manager::SWITCHER,
                'label'     => esc_html__( 'Show Boosted Posts Only', 'cubewp-framework' ),
                'default'   => 'no',
            ) );
        }
        
		$this->add_additional_controls();

		$this->add_control( 'orderby', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Order By', 'cubewp-framework' ),
			'options' => array(
				'title' => esc_html__( 'Title', 'cubewp-framework' ),
				'date'  => esc_html__( 'Most Recent', 'cubewp-framework' ),
				'rand'  => esc_html__( 'Random', 'cubewp-framework' ),
			),
			'default' => 'date',
		) );
		$this->add_control( 'order', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Order', 'cubewp-framework' ),
			'options'   => array(
				'ASC'  => esc_html__( 'Ascending', 'cubewp-framework' ),
				'DESC' => esc_html__( 'Descending', 'cubewp-framework' ),
			),
			'default'   => 'DESC',
			'condition' => array(
				'orderby!' => 'rand',
			),
		) );
		$this->add_control( 'posts_per_page', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Posts Per Page', 'cubewp-framework' ),
			'options' => array(
				'-1' => esc_html__( 'Show All Posts', 'cubewp-framework' ),
				'3'  => esc_html__( 'Show 3 Posts', 'cubewp-framework' ),
				'4'  => esc_html__( 'Show 4 Posts', 'cubewp-framework' ),
				'5'  => esc_html__( 'Show 5 Posts', 'cubewp-framework' ),
				'6'  => esc_html__( 'Show 6 Posts', 'cubewp-framework' ),
				'8'  => esc_html__( 'Show 8 Posts', 'cubewp-framework' ),
				'9'  => esc_html__( 'Show 9 Posts', 'cubewp-framework' ),
				'12' => esc_html__( 'Show 12 Posts', 'cubewp-framework' ),
				'16' => esc_html__( 'Show 16 Posts', 'cubewp-framework' ),
				'15' => esc_html__( 'Show 15 Posts', 'cubewp-framework' ),
				'20' => esc_html__( 'Show 20 Posts', 'cubewp-framework' )
			),
			'default' => '3'
		) );
		$this->add_control( 'layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Layout', 'cubewp-framework' ),
			'options' => array(
				'grid' => esc_html__( 'Grid View', 'cubewp-framework' ),
				'list' => esc_html__( 'List View', 'cubewp-framework' )
			),
			'default' => 'grid'
		) );
		$this->add_control( 'column_per_row', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'No Of Columns Per Row', 'cubewp-framework' ),
			'options' => array(
				'1' => esc_html__( '1 Column Per Row', 'cubewp-framework' ),
				'2' => esc_html__( '2 Columns Per Row', 'cubewp-framework' ),
				'3' => esc_html__( '3 Columns Per Row', 'cubewp-framework' ),
				'4' => esc_html__( '4 Columns Per Row', 'cubewp-framework' ),
				'0' => esc_html__( 'Auto Adjust Columns Per Row', 'cubewp-framework' )
			),
			'condition' => array(
				'layout' => 'grid',
			),
			'default' => '3'
		) );
		$this->end_controls_section();
	}

	private static function get_post_types() {
		$post_types = cwp_post_types();
		unset( $post_types['elementor_library'] );
		unset( $post_types['e-landing-page'] );
		unset( $post_types['post'] );
		unset( $post_types['attachment'] );
		unset( $post_types['page'] );

		self::$post_types = $post_types;
	}

	private function add_additional_controls() {
		$post_types = self::$post_types;
		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$this->add_control( 'posts_by', array(
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Show Posts', 'cubewp-framework' ),
				'options' => array(
					"taxonomy" => esc_html__( "By Taxonomy" ),
					"post_ids" => esc_html__( "By IDs" )
				),
				'condition' => array(
					'posttype!' => "",
				),
				'default' => 'taxonomy'
			) );
			foreach ( $post_types as $post_type ) {
				$this->add_taxonomy_controls( $post_type );
				$this->add_posttype_controls( $post_type );
			}
		}
	}

	private function add_taxonomy_controls( $post_type ) {
		$taxonomies = get_object_taxonomies( $post_type );
		$taxonomies = array_combine( $taxonomies, $taxonomies );
		if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
			$this->add_control( 'taxonomy-' . $post_type, array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => esc_html__( 'Select Taxonomy', 'cubewp-framework' ),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'   => $taxonomies,
				'multiple'  => true,
				'condition' => array(
					'posts_by' => "taxonomy",
					'posttype' => $post_type,
				),
			) );
			foreach ( $taxonomies as $taxonomy ) {
				$terms     = get_terms( array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				) );
				$terms_arr = array();
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$terms_arr[ $term->slug ] = $term->name;
					}
				}
				if ( ! empty( $terms_arr ) ) {
					$this->add_control( 'terms-' . $post_type . '-' . $taxonomy, array(
						'type'      => Controls_Manager::SELECT2,
						'label'     => esc_html__( 'Select Term', 'cubewp-framework' ),
						'options'   => $terms_arr,
						'multiple'  => true,
						'condition' => array(
							'posts_by'               => "taxonomy",
							'taxonomy-' . $post_type => $taxonomy,
						),
					) );
				}
			}
		}
	}

	private function add_posttype_controls( $post_type ) {
		$posts = self::get_post_type_posts( $post_type );
		if ( ! empty( $posts ) ) {
			$this->add_control( $post_type . '_post__in', array(
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Please Select Posts', 'cubewp-framework' ),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'     => $posts,
				'multiple'    => true,
				'placeholder' => esc_html__( 'Please Select Posts', 'cubewp-framework' ),
				'condition'   => array(
					'posts_by' => "post_ids",
					'posttype' => $post_type
				)
			) );
		}
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

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$taxonomies = isset($settings[ 'taxonomy-' . $settings['posttype'] ]) ? $settings[ 'taxonomy-' . $settings['posttype'] ]: array();
		$args = array(
			'posttype'       => $settings['posttype'],
			'taxonomy'       => $taxonomies,
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'posts_per_page' => $settings['posts_per_page'],
			'layout'         => $settings['layout'],
			'column_per_row' => $settings['column_per_row'],
			'post__in'       => $settings[ $settings['posttype'] . '_post__in' ]
		);
		if(class_exists('CubeWp_Booster_Load')){
            $args['show_boosted_posts'] = $settings['show_boosted_posts'];
        }
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms                        = $settings[ 'terms-' . $settings['posttype'] . '-' . $taxonomy ];
				$args[ $taxonomy . '-terms' ] = $terms;
			}
		}

		echo apply_filters( 'cubewp_shortcode_posts_output','', $args );
	}

}