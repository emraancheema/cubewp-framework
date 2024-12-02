<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
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
	private static $settings = array();

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

		$this->start_controls_section( 'cubewp_widgets_section', array(
			'label' => esc_html__( 'Query Options', 'cubewp-framework' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		
		$this->add_post_type_controls();
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
		$this->add_control( 'number_of_posts', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Number Of Posts', 'cubewp-framework' ),
			'options' => array(
				'-1' => esc_html__( 'All Posts', 'cubewp-framework' ),
				'3'  => esc_html__( '3 Posts', 'cubewp-framework' ),
				'4'  => esc_html__( '4 Posts', 'cubewp-framework' ),
				'5'  => esc_html__( '5 Posts', 'cubewp-framework' ),
				'6'  => esc_html__( '6 Posts', 'cubewp-framework' ),
				'8'  => esc_html__( '8 Posts', 'cubewp-framework' ),
				'9'  => esc_html__( '9 Posts', 'cubewp-framework' ),
				'12' => esc_html__( '12 Posts', 'cubewp-framework' ),
				'16' => esc_html__( '16 Posts', 'cubewp-framework' ),
				'15' => esc_html__( '15 Posts', 'cubewp-framework' ),
				'20' => esc_html__( '20 Posts', 'cubewp-framework' )
			),
			'default' => '3'
		) );
		$this->add_control( 'load_more', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Load More Button', 'cubewp-framework' ),
			'default'   => 'yes',
			'condition' => array(
				'number_of_posts' => '-1',
			)
		) );
		$this->add_control( 'posts_per_page', array(
			'type'    => Controls_Manager::NUMBER,
			'label'   => esc_html__( 'Posts Per Page', 'cubewp-framework' ),
			'default' => '6',
			'condition' => array(
				'number_of_posts' => '-1',
				'load_more' => 'yes',
			)
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

		$this->end_controls_section();

		$this->start_controls_section( 'cubewp_posts_widget_additional_setting_section', array(
			'label' => esc_html__( 'Filter By Meta / Custom Fields', 'cubewp-classifiad' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
			'condition' => array(
				'posts_by'  => array('all','taxonomy'),
			),
		) );
		$this->add_control( 'filter_by_meta', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__( 'Filter By Meta / Custom Field', 'cubewp-framework' ),
			'default'   => 'no',
		) );
		
		$this->add_control( 'meta_relation', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Select Relation', 'cubewp-classifiad' ),
			'description'   => esc_html__( "e.g. If you have multiple custom field's conditions and you set relation OR then system will get result if one of these conditions will be true.", "cubewp-framework" ),
			'options'   => array(
				'OR'  => esc_html__( 'OR', 'cubewp-classifiad' ),
				'AND'  => esc_html__( "AND", 'cubewp-classifiad' ),
			),
			'default'   => 'or',
			'condition' => array(
				'filter_by_meta'  => 'yes',
			),
		) );

		$repeater = new Repeater();

		$repeater->add_control( 'meta_key', array(
			'type'      => Controls_Manager::SELECT2,
			'label'     => esc_html__( 'Select Custom Field', 'cubewp-framework' ),
			'options'   => get_fields_by_type(array('number','text','checkbox')),
			'label_block' => true,
		) );

		$repeater->add_control( 'meta_value', array(
			'type'      => Controls_Manager::TEXT,
			'label'     => esc_html__( 'Put here meta value', 'cubewp-framework' ),
			'placeholder'   => esc_html__( "e.g. APPLE", "cubewp-framework" ),
			'description'   => esc_html__( "e.g. If custom field is BRAND NAME, you can set value as APPLE to get all those posts who set this meta.", "cubewp-framework" ),
			'label_block' => true,
		) );

		$repeater->add_control( 'meta_compare', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__( 'Select Operator to compare ', 'cubewp-classifiad' ),
			'description'   => esc_html__( "e.g. If going to select BETWEEN or NOT BETWEEN then add value like this [100, 200].", "cubewp-framework" ),
			'options'   => array(
				'='  => esc_html__( 'Equal', 'cubewp-framework' ),
				'!='  => esc_html__( 'Not Equal', 'cubewp-framework' ),
				'>'  => esc_html__( 'Greater Than', 'cubewp-framework' ),
				'>='  => esc_html__( 'Greater Than or Equal', 'cubewp-framework' ),
				'<'  => esc_html__( 'Less Than', 'cubewp-framework' ),
				'<='  => esc_html__( 'Less Than or Equal', 'cubewp-framework' ),
				'LIKE'  => esc_html__( 'LIKE %', 'cubewp-framework' ),
				'NOT LIKE'  => esc_html__( 'NOT LIKE', 'cubewp-framework' ),
				'IN' => esc_html__( 'IN', 'cubewp-framework' ),
				'NOT IN' => esc_html__( 'NOT IN', 'cubewp-framework' ),
				'BETWEEN' => esc_html__( 'BETWEEN', 'cubewp-framework' ),
				'NOT BETWEEN' => esc_html__( 'NOT BETWEEN', 'cubewp-framework' ),
				'EXISTS' => esc_html__( 'EXISTS', 'cubewp-framework' ),
				'NOT EXISTS' => esc_html__( 'NOT EXISTS', 'cubewp-framework' ),
			),
			'default'   => 'LIKE',
			'condition' => array(
				'meta_key!'  => '',
				'meta_value!' => '',
			),
		) );

		$this->add_control( 'filter_by_custom_fields', array(
			'label'       => esc_html__( 'Add Conditions', 'cubewp-classifiad' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ meta_key }}}',
			'condition' => array(
				'filter_by_meta' => "yes",
				'posttype!' => '',
			),
			
		) );
		$this->end_controls_section();
	}

	private static function get_post_types() {
		$post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        foreach ($post_types as $post_type) {
            $options[$post_type->name] = $post_type->label;
        }
		unset( $options['elementor_library'] );
		unset( $options['e-landing-page'] );
		unset( $options['attachment'] );
		unset( $options['page'] );

		self::$post_types = $options;
	}
	
	private function add_post_type_controls() {
		$post_types = self::$post_types;
		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$this->add_control( 'posttype', array(
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label'       => esc_html__( 'Select Post Types', 'cubewp-classifiad' ),
				'description' => esc_html__( 'You can select one or multiple post types to show post cards.', 'cubewp-framework' ),
				'options'     => $post_types,
				'default'     => array( 'post' ),
				'label_block' => true,
			) );
			foreach ( $post_types as $slug => $post_type ) {
				$this->add_card_style_controls( $slug );
			}
		}
	}

	private function add_card_style_controls($post_type) {
		if(class_exists('CubeWp_Frontend_Load')){
			if(!empty(cubewp_post_card_styles($post_type))){
				$this->add_control( $post_type.'_card_style', array(
					'type'        => Controls_Manager::SELECT,
					'label'       => esc_html__( 'Card Style for '.self::get_post_type_name_by_slug($post_type), 'cubewp-framework' ),
					'options'     => cubewp_post_card_styles($post_type),
					'default'     => 'default_style',
					'condition'   => array(
						'posttype' => $post_type
					)
				) );
			}
		}
	}

	private function add_additional_controls() {
		
		$post_types = self::$post_types;
		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$options = array(
				"all" => esc_html__( "All" ),
				"taxonomy" => esc_html__( "By Taxonomy" ),
				//"post_ids" => esc_html__( "By IDs" ),
			);
			if(class_exists('CubeWp_Booster_Load')){
				$options['boosted'] = esc_html__( "Boosted Only" );
			}
			$this->add_control( 'posts_by', array(
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Show Posts', 'cubewp-framework' ),
				'options' => $options,
				'condition' => array(
					'posttype!' => "",
				),
				'default' => 'all'
			) );
			foreach ( $post_types as $slug => $post_type ) {
				$this->add_taxonomy_controls( $slug );
				$this->add_posttype_controls( $slug );
			}
		}
	}

	private function add_taxonomy_controls( $post_type ) {
		$taxonomies = get_object_taxonomies( $post_type );
		$taxonomies = array_combine( $taxonomies, $taxonomies );
		if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
			$this->add_control( 'taxonomy-' . $post_type, array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => esc_html__( 'Select Terms for '.self::get_post_type_name_by_slug($post_type), 'cubewp-framework' ),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'   => self::get_terms_by_post_type($post_type),
				'multiple'  => true,
				'condition' => array(
					'posts_by' => "taxonomy",
					'posttype' => $post_type,
				),
				'label_block' => true,
			) );
		}
	}

	private function add_posttype_controls( $post_type ) {
		//$posts = self::get_post_type_posts( $post_type );
		
		if ( ! empty( $posts ) ) {
			$this->add_control( $post_type . '_post__in', array(
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Please Select Posts for '.self::get_post_type_name_by_slug($post_type), 'cubewp-framework' ),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'     => $posts,
				'multiple'    => true,
				'placeholder' => esc_html__( 'Please Select Posts', 'cubewp-framework' ),
				'condition'   => array(
					'posts_by' => "post_ids",
					'posttype' => $post_type
				),
				'label_block' => true,
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

	private static function get_terms_by_post_type( $post_type ) {
		$object  = cubewp_terms_by_post_types($post_type);
		$termArray = [];
		if(!empty($object)){
			foreach($object as $key => $terms){
				$termArray['['.$terms['taxonomy'].']'.$key] = $terms['name'];
			}
		}
	 
		return $termArray;
	}

	private static function get_post_type_name_by_slug($post_type_slug) {
		$post_type_object = get_post_type_object($post_type_slug);
		// Check if the post type object exists and return its label (name)
		if ($post_type_object) {
			return $post_type_object->label;
		}
		return null;
	}

	protected static function split_taxonomy_and_term($input) {
		if (preg_match('/\[(.*?)\](.*)/', $input, $matches)) {
			return [
				'taxonomy' => $matches[1],
				'term_slug' => $matches[2]
			];
		}
	
		// Return null if the format is not matched
		return null;
	}

	private static function _meta_query($args){
		if(is_array($args) && isset($args['query']) && !empty($args['query'])){
			$meta_query = array();
			$meta_query['relation'] = $args['relation'];
			$numeric_comparisons = ['=', '!=', '>', '>=', '<', '<=', 'BETWEEN', 'NOT BETWEEN'];
			foreach($args['query'] as $index => $query){
				$meta_query[$index] = array(
					'key'  => $query['meta_key'],
					'value'	    => $query['meta_value'],
					'compare'   => $query['meta_compare'],
				);
				if (isset($query['meta_compare']) && in_array($query['meta_compare'], $numeric_comparisons)) {
					$meta_query[$index]['type'] = 'NUMERIC';
				}
			}
			return $meta_query;
		}
    }

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$meta_query = array();
		$posts_by = isset( $settings[ 'posts_by' ] ) ? $settings[ 'posts_by' ] : '';
		$filter_by_meta = isset( $settings[ 'filter_by_meta' ] ) ? $settings[ 'filter_by_meta' ] : array();
		
		$args = array(
			'post_type'       => $settings['posttype'],
			'taxonomy'       => array(),
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'number_of_posts' => $settings['number_of_posts'],
			'load_more' 	  => $settings['load_more'],
			'posts_per_page' => $settings['posts_per_page'],
			'layout'         => $settings['layout'],
			'post__in'       => array(),
			'boosted_only'   => 'no',
			'paged'   => '1',
		);

		if(is_array($settings['posttype']) && ($posts_by !== 'boosted' || $posts_by !== 'all')){
			foreach($settings['posttype'] as $post_type){

				if($posts_by == 'post_ids'){

					$post_in = isset( $settings[ $post_type . '_post__in' ] ) ? $settings[ $post_type . '_post__in' ] : array();
					if(!empty($post_in)){
						$args['post__in'] = array_merge($args['post__in'],$post_in);
					}

				}elseif($posts_by == 'taxonomy'){

					$terms = isset($settings[ 'taxonomy-' . $post_type ]) ? $settings[ 'taxonomy-' . $post_type ]: array();
					if(!empty($terms)){
						foreach ( $terms as $term ) {
							$result = self::split_taxonomy_and_term($term);
							if ($result) {
								$args['taxonomy'] = array_unique(array_merge($args['taxonomy'],array($result['taxonomy'])));
								$args[ $result['taxonomy'] . '-terms' ][] = $result['term_slug'];
							}
						}
					}

				}
				$card_style = isset( $settings[ $post_type . '_card_style' ] ) ? $settings[ $post_type . '_card_style' ] : '';
				if(!empty($card_style)){
					$args['card_style'][$post_type] = $card_style;
				}
			}
		}
		
		if(class_exists('CubeWp_Booster_Load')){
			if($posts_by == 'boosted'){
            	$args['boosted_only'] = 'yes';
			}
        }

		if($filter_by_meta == 'yes'){
			$meta_query['query'] = isset( $settings[ 'filter_by_custom_fields' ] ) ? $settings[ 'filter_by_custom_fields' ] : array();
			$meta_query['relation'] = isset( $settings[ 'meta_relation' ] ) ? $settings[ 'meta_relation' ] : 'OR';
			$args['meta_query'] = self::_meta_query($meta_query);
		}
		

		echo apply_filters( 'cubewp_shortcode_posts_output','', $args );
	}

}