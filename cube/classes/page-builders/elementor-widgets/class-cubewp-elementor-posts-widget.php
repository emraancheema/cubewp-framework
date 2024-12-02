<?php
defined('ABSPATH') || exit;

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
class CubeWp_Elementor_Posts_Widget extends Widget_Base
{

	private static $post_types = array();
	private static $settings = array();

	public function get_name()
	{
		return 'cubewp_posts';
	}

	public function get_title()
	{
		return esc_html__('CubeWP Posts', 'cubewp-framework');
	}

	public function get_icon()
	{
		return 'eicon-post-list';
	}

	public function get_categories()
	{
		return array('cubewp');
	}

	public function get_keywords()
	{
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

	protected function register_controls()
	{
		self::get_post_types();

		$this->start_controls_section('cubewp_widgets_section', array(
			'label' => esc_html__('Query Options', 'cubewp-framework'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		));

		$this->add_post_type_controls();
		$this->add_additional_controls();


		$this->add_control('orderby', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__('Order By', 'cubewp-framework'),
			'options' => array(
				'title' => esc_html__('Title', 'cubewp-framework'),
				'date'  => esc_html__('Most Recent', 'cubewp-framework'),
				'rand'  => esc_html__('Random', 'cubewp-framework'),
			),
			'default' => 'date',
		));
		$this->add_control('order', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__('Order', 'cubewp-framework'),
			'options'   => array(
				'ASC'  => esc_html__('Ascending', 'cubewp-framework'),
				'DESC' => esc_html__('Descending', 'cubewp-framework'),
			),
			'default'   => 'DESC',
			'condition' => array(
				'orderby!' => 'rand',
			),
		));
		$this->add_control('number_of_posts', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__('Number Of Posts', 'cubewp-framework'),
			'options' => array(
				'-1' => esc_html__('All Posts', 'cubewp-framework'),
				'1'  => esc_html__('1 Post', 'cubewp-framework'),
				'2'  => esc_html__('2 Posts', 'cubewp-framework'),
				'3'  => esc_html__('3 Posts', 'cubewp-framework'),
				'4'  => esc_html__('4 Posts', 'cubewp-framework'),
				'5'  => esc_html__('5 Posts', 'cubewp-framework'),
				'6'  => esc_html__('6 Posts', 'cubewp-framework'),
				'8'  => esc_html__('8 Posts', 'cubewp-framework'),
				'9'  => esc_html__('9 Posts', 'cubewp-framework'),
				'12' => esc_html__('12 Posts', 'cubewp-framework'),
				'16' => esc_html__('16 Posts', 'cubewp-framework'),
				'15' => esc_html__('15 Posts', 'cubewp-framework'),
				'20' => esc_html__('20 Posts', 'cubewp-framework')
			),
			'default' => '3'
		));
		$this->add_control('load_more', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__('Load More Button', 'cubewp-framework'),
			'default'   => 'yes',
			'condition' => array(
				'number_of_posts' => '-1',
			)
		));
		$this->add_control('posts_per_page', array(
			'type'    => Controls_Manager::NUMBER,
			'label'   => esc_html__('Posts Per Page', 'cubewp-framework'),
			'default' => '6',
			'condition' => array(
				'number_of_posts' => '-1',
				'load_more' => 'yes',
			)
		));

		$this->add_control('layout', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__('Layout', 'cubewp-framework'),
			'options' => array(
				'grid' => esc_html__('Grid View', 'cubewp-framework'),
				'list' => esc_html__('List View', 'cubewp-framework')
			),
			'default' => 'grid'
		));

		$this->add_control('enable_scroll_on_small_devices', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__('Enable Scroll on Small Devices', 'cubewp-framework'),
			'default'   => '',
			'description' => esc_html__('Enable overflow scroll behaviour on small devices.', 'cubewp-framework'),
			'condition' => array(
				'cwp_enable_slider!' => 'yes',
			),
		));

		$this->end_controls_section();

		$this->start_controls_section('cubewp_posts_widget_additional_setting_section', array(
			'label' => esc_html__('Filter By Meta / Custom Fields', 'cubewp-classifiad'),
			'tab'   => Controls_Manager::TAB_CONTENT,
			'condition' => array(
				'posts_by'  => array('all', 'taxonomy'),
			),
		));
		$this->add_control('filter_by_meta', array(
			'type'      => Controls_Manager::SWITCHER,
			'label'     => esc_html__('Filter By Meta / Custom Field', 'cubewp-framework'),
			'default'   => 'no',
		));

		$this->add_control('meta_relation', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__('Select Relation', 'cubewp-classifiad'),
			'description'   => esc_html__("e.g. If you have multiple custom field's conditions and you set relation OR then system will get result if one of these conditions will be true.", "cubewp-framework"),
			'options'   => array(
				'OR'  => esc_html__('OR', 'cubewp-classifiad'),
				'AND'  => esc_html__("AND", 'cubewp-classifiad'),
			),
			'default'   => 'or',
			'condition' => array(
				'filter_by_meta'  => 'yes',
			),
		));

		$repeater = new Repeater();

		$repeater->add_control('meta_key', array(
			'type'      => Controls_Manager::SELECT2,
			'label'     => esc_html__('Select Custom Field', 'cubewp-framework'),
			'options'   => get_fields_by_type(array('number', 'text', 'checkbox')),
			'label_block' => true,
		));

		$repeater->add_control('meta_value', array(
			'type'      => Controls_Manager::TEXT,
			'label'     => esc_html__('Put here meta value', 'cubewp-framework'),
			'placeholder'   => esc_html__("e.g. APPLE", "cubewp-framework"),
			'description'   => esc_html__("e.g. If custom field is BRAND NAME, you can set value as APPLE to get all those posts who set this meta.", "cubewp-framework"),
			'label_block' => true,
		));

		$repeater->add_control('meta_compare', array(
			'type'      => Controls_Manager::SELECT,
			'label'     => esc_html__('Select Operator to compare ', 'cubewp-classifiad'),
			'description'   => esc_html__("e.g. If going to select BETWEEN or NOT BETWEEN then add value like this [100, 200].", "cubewp-framework"),
			'options'   => array(
				'='  => esc_html__('Equal', 'cubewp-framework'),
				'!='  => esc_html__('Not Equal', 'cubewp-framework'),
				'>'  => esc_html__('Greater Than', 'cubewp-framework'),
				'>='  => esc_html__('Greater Than or Equal', 'cubewp-framework'),
				'<'  => esc_html__('Less Than', 'cubewp-framework'),
				'<='  => esc_html__('Less Than or Equal', 'cubewp-framework'),
				'LIKE'  => esc_html__('LIKE %', 'cubewp-framework'),
				'NOT LIKE'  => esc_html__('NOT LIKE', 'cubewp-framework'),
				'IN' => esc_html__('IN', 'cubewp-framework'),
				'NOT IN' => esc_html__('NOT IN', 'cubewp-framework'),
				'BETWEEN' => esc_html__('BETWEEN', 'cubewp-framework'),
				'NOT BETWEEN' => esc_html__('NOT BETWEEN', 'cubewp-framework'),
				'EXISTS' => esc_html__('EXISTS', 'cubewp-framework'),
				'NOT EXISTS' => esc_html__('NOT EXISTS', 'cubewp-framework'),
			),
			'default'   => 'LIKE',
			'condition' => array(
				'meta_key!'  => '',
				'meta_value!' => '',
			),
		));

		$this->add_control('filter_by_custom_fields', array(
			'label'       => esc_html__('Add Conditions', 'cubewp-classifiad'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ meta_key }}}',
			'condition' => array(
				'filter_by_meta' => "yes",
				'posttype!' => '',
			),

		));
		$this->end_controls_section();
		$this->add_slider_controls();
	}

	private static function get_post_types()
	{
		$post_types = get_post_types(['public' => true], 'objects');
		$options = [];
		foreach ($post_types as $post_type) {
			$options[$post_type->name] = $post_type->label;
		}
		unset($options['elementor_library']);
		unset($options['e-landing-page']);
		unset($options['attachment']);
		unset($options['page']);

		self::$post_types = $options;
	}

	private function add_post_type_controls()
	{
		$post_types = self::$post_types;
		if (is_array($post_types) && ! empty($post_types)) {
			$this->add_control('posttype', array(
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label'       => esc_html__('Select Post Types', 'cubewp-classifiad'),
				'description' => esc_html__('You can select one or multiple post types to show post cards.', 'cubewp-framework'),
				'options'     => $post_types,
				'default'     => array('post'),
				'label_block' => true,
			));
			foreach ($post_types as $slug => $post_type) {
				$this->add_card_style_controls($slug);
			}
		}
	}

	private function add_card_style_controls($post_type)
	{
		if (!empty(cubewp_post_card_styles($post_type))) {
			$this->add_control($post_type . '_card_style', array(
				'type'        => Controls_Manager::SELECT,
				'label'       => esc_html__('Card Style for ' . self::get_post_type_name_by_slug($post_type), 'cubewp-framework'),
				'options'     => cubewp_post_card_styles($post_type),
				'default'     => 'default_style',
				'condition'   => array(
					'posttype' => $post_type
				)
			));
		}
	}

	private function add_additional_controls()
	{

		$post_types = self::$post_types;
		if (is_array($post_types) && ! empty($post_types)) {
			$options = array(
				"all" => esc_html__("All"),
				"taxonomy" => esc_html__("By Taxonomy"),
				//"post_ids" => esc_html__( "By IDs" ),
			);
			if (class_exists('CubeWp_Booster_Load')) {
				$options['boosted'] = esc_html__("Boosted Only");
			}
			$this->add_control('posts_by', array(
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__('Show Posts', 'cubewp-framework'),
				'options' => $options,
				'condition' => array(
					'posttype!' => "",
				),
				'default' => 'all'
			));
			foreach ($post_types as $slug => $post_type) {
				$this->add_taxonomy_controls($slug);
				$this->add_posttype_controls($slug);
			}
		}
	}

	private function add_taxonomy_controls($post_type)
	{
		$taxonomies = get_object_taxonomies($post_type);
		$taxonomies = array_combine($taxonomies, $taxonomies);
		if (is_array($taxonomies) && ! empty($taxonomies)) {
			$this->add_control('taxonomy-' . $post_type, array(
				'type'      => Controls_Manager::SELECT2,
				'label'     => esc_html__('Select Terms for ' . self::get_post_type_name_by_slug($post_type), 'cubewp-framework'),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'   => self::get_terms_by_post_type($post_type),
				'multiple'  => true,
				'condition' => array(
					'posts_by' => "taxonomy",
					'posttype' => $post_type,
				),
				'label_block' => true,
			));
		}
	}

	private function add_posttype_controls($post_type)
	{
		//$posts = self::get_post_type_posts( $post_type );

		if (! empty($posts)) {
			$this->add_control($post_type . '_post__in', array(
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__('Please Select Posts for ' . self::get_post_type_name_by_slug($post_type), 'cubewp-framework'),
				'description' => esc_html__('Leave empty if you want to display all posts.', 'cubewp-framework'),
				'options'     => $posts,
				'multiple'    => true,
				'placeholder' => esc_html__('Please Select Posts', 'cubewp-framework'),
				'condition'   => array(
					'posts_by' => "post_ids",
					'posttype' => $post_type
				),
				'label_block' => true,
			));
		}
	}

	private static function get_post_type_posts($post_types)
	{
		$query  = new CubeWp_Query(array(
			'post_type'      => $post_types,
			'posts_per_page' => -1
		));
		$posts  = $query->cubewp_post_query();
		$return = array();
		if ($posts->have_posts()) :
			while ($posts->have_posts()) : $posts->the_post();
				$return[get_the_ID()] = get_the_title() . ' [' . get_the_ID() . ']';
			endwhile;
		endif;

		return $return;
	}

	private static function get_terms_by_post_type($post_type)
	{
		$object  = cubewp_terms_by_post_types($post_type);
		$termArray = [];
		if (!empty($object)) {
			foreach ($object as $key => $terms) {
				$termArray['[' . $terms['taxonomy'] . ']' . $key] = $terms['name'];
			}
		}

		return $termArray;
	}

	private static function get_post_type_name_by_slug($post_type_slug)
	{
		$post_type_object = get_post_type_object($post_type_slug);
		// Check if the post type object exists and return its label (name)
		if ($post_type_object) {
			return $post_type_object->label;
		}
		return null;
	}

	protected static function split_taxonomy_and_term($input)
	{
		if (preg_match('/\[(.*?)\](.*)/', $input, $matches)) {
			return [
				'taxonomy' => $matches[1],
				'term_slug' => $matches[2]
			];
		}

		// Return null if the format is not matched
		return null;
	}

	private static function _meta_query($args)
	{
		if (is_array($args) && isset($args['query']) && !empty($args['query'])) {
			$meta_query = array();
			$meta_query['relation'] = $args['relation'];
			$numeric_comparisons = ['=', '!=', '>', '>=', '<', '<=', 'BETWEEN', 'NOT BETWEEN'];
			foreach ($args['query'] as $index => $query) {
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

	protected function render()
	{
		$settings   = $this->get_settings_for_display();
		$meta_query = array();
		$posts_by = isset($settings['posts_by']) ? $settings['posts_by'] : '';
		$filter_by_meta = isset($settings['filter_by_meta']) ? $settings['filter_by_meta'] : array();

		$widget_id = $this->get_id();
		if ($settings['enable_scroll_on_small_devices'] === 'yes') {
			echo '<style>
                @media (max-width: 767px) {
                    .elementor-element-' . $widget_id . ' .cwp-row {
                        overflow: scroll;
                        flex-wrap: nowrap;
                    }
                }
            </style>';
		}

		$prev_icon = '';
		$prev_icon_type = false;
		if (!empty($settings['prev_icon']['value'])) {
			if ('svg' === $settings['prev_icon']['library']) {
				$prev_icon_url = esc_url($settings['prev_icon']['value']['url']);
				$prev_icon_content = file_get_contents($prev_icon_url);
				$prev_icon = $prev_icon_content;
			} else {
				$prev_icon = esc_attr($settings['prev_icon']['value']);
				$prev_icon_type = true;
			}
		}
		$next_icon = '';
		$next_icon_type = false;
		if (!empty($settings['next_icon']['value'])) {
			if ('svg' === $settings['next_icon']['library']) {
				$next_icon_url = esc_url($settings['next_icon']['value']['url']);
				$next_icon_content = file_get_contents($next_icon_url);
				$next_icon = $next_icon_content;
			} else {
				$next_icon =  esc_attr($settings['next_icon']['value']);
				$next_icon_type = true;
			}
		}
		$slides_to_show = $settings['slides_to_show'];
		$slides_to_scroll = $settings['slides_to_scroll'];
		$slides_to_show_tablet = $settings['slides_to_show_tablet'];
		$slides_to_show_tablet_portrait = $settings['slides_to_show_tablet_portrait'];
		$slides_to_show_mobile = $settings['slides_to_show_mobile'];
		$slides_to_scroll_tablet = $settings['slides_to_scroll_tablet'];
		$slides_to_scroll_tablet_portrait = $settings['slides_to_scroll_tablet_portrait'];
		$slides_to_scroll_mobile = $settings['slides_to_scroll_mobile'];
		$autoplay = $settings['autoplay'] === 'yes' ? true : false;
		$autoplay_speed = $settings['autoplay_speed'];
		$speed = $settings['speed'];
		$infinite = $settings['infinite'] === 'yes' ? true : false;
		$variable_width = $settings['variable_width'] === 'yes' ? true : false;
		$custom_arrows = $settings['custom_arrows'] === 'yes' ? true : false;
		$custom_dots = $settings['custom_dots'] === 'yes' ? true : false;
		$enable_progress_bar = $settings['enable_progress_bar'] === 'yes' ? true : false;

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
			'cwp_enable_slider' => $settings['cwp_enable_slider'] === 'yes' ? 'cubewp-post-slider' : '',
			'prev_icon' => $prev_icon,
			'next_icon' => $next_icon,
			'next_icon_type' => $next_icon_type,
			'prev_icon_type' => $prev_icon_type,
			'slides_to_show' => $slides_to_show,
			'slides_to_scroll' => $slides_to_scroll,
			'slides_to_show_tablet' => $slides_to_show_tablet,
			'slides_to_show_tablet_portrait' => $slides_to_show_tablet_portrait,
			'slides_to_show_mobile' => $slides_to_show_mobile,
			'slides_to_scroll_tablet' => $slides_to_scroll_tablet,
			'slides_to_scroll_tablet_portrait' => $slides_to_scroll_tablet_portrait,
			'slides_to_scroll_mobile' => $slides_to_scroll_mobile,
			'autoplay' => $autoplay,
			'autoplay_speed' => $autoplay_speed,
			'speed' => $speed,
			'infinite' => $infinite,
			'variable_width' => $variable_width,
			'custom_arrows' => $custom_arrows,
			'custom_dots' => $custom_dots,
			'enable_progress_bar' => $enable_progress_bar,
		);

		if (is_array($settings['posttype']) && ($posts_by !== 'boosted' || $posts_by !== 'all')) {
			foreach ($settings['posttype'] as $post_type) {

				if ($posts_by == 'post_ids') {

					$post_in = isset($settings[$post_type . '_post__in']) ? $settings[$post_type . '_post__in'] : array();
					if (!empty($post_in)) {
						$args['post__in'] = array_merge($args['post__in'], $post_in);
					}
				} elseif ($posts_by == 'taxonomy') {

					$terms = isset($settings['taxonomy-' . $post_type]) ? $settings['taxonomy-' . $post_type] : array();
					if (!empty($terms)) {
						foreach ($terms as $term) {
							$result = self::split_taxonomy_and_term($term);
							if ($result) {
								$args['taxonomy'] = array_unique(array_merge($args['taxonomy'], array($result['taxonomy'])));
								$args[$result['taxonomy'] . '-terms'][] = $result['term_slug'];
							}
						}
					}
				}
				$card_style = isset($settings[$post_type . '_card_style']) ? $settings[$post_type . '_card_style'] : '';
				if (!empty($card_style)) {
					$args['card_style'][$post_type] = $card_style;
				}
			}
		}

		if (class_exists('CubeWp_Booster_Load')) {
			if ($posts_by == 'boosted') {
				$args['boosted_only'] = 'yes';
			}
		}

		if ($filter_by_meta == 'yes') {
			$meta_query['query'] = isset($settings['filter_by_custom_fields']) ? $settings['filter_by_custom_fields'] : array();
			$meta_query['relation'] = isset($settings['meta_relation']) ? $settings['meta_relation'] : 'OR';
			$args['meta_query'] = self::_meta_query($meta_query);
		}


		echo apply_filters('cubewp_shortcode_posts_output', '', $args);
	}

	private function add_slider_controls()
	{

		$this->start_controls_section(
			'slider_style_section',
			[
				'label' => esc_html__('Posts Slider', 'cubewp-framework'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'cwp_enable_slider',
			[
				'label'        => esc_html__('Enable Slider', 'cubewp-framework'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'cubewp-framework'),
				'label_off'    => esc_html__('No', 'cubewp-framework'),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'layout' => 'grid',
				],
			]
		);


		$this->add_responsive_control(
			'slider_post_spacing',
			[
				'label'        => esc_html__('Post Spacing', 'cubewp-framework'),
				'type'         => Controls_Manager::DIMENSIONS,
				'size_units'   => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .cubewp-post-slider .slick-slide>div ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .cwp-row>div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control('slides_to_show', array(
			'type'    => Controls_Manager::NUMBER,
			'label'   => esc_html__('Slides To Show', 'cubewp-framework'),
			'default' => 3,
			'min'     => 1,
			'max'     => 10,
			'step'    => 1,
			'description' => esc_html__('Number of slides to show at once in the slider.', 'cubewp-framework'),
			'condition'   => [
				'cwp_enable_slider' => 'yes',
			],
		));

		$this->add_control(
			'slides_to_scroll',
			[
				'type'    => Controls_Manager::NUMBER,
				'label'   => esc_html__('Slides To Scroll', 'cubewp-framework'),
				'default' => 1,
				'min'     => 1,
				'max'     => 10,
				'step'    => 1,
				'description' => esc_html__('Number of slides to scroll at once in the slider.', 'cubewp-framework'),
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'type'    => Controls_Manager::SWITCHER,
				'label'   => esc_html__('Autoplay', 'cubewp-framework'),
				'default' => 'yes',
				'description' => esc_html__('Enable or disable autoplay for the slider.', 'cubewp-framework'),
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'type'    => Controls_Manager::NUMBER,
				'label'   => esc_html__('Autoplay Speed (ms)', 'cubewp-framework'),
				'default' => 2000,
				'min'     => 500,
				'max'     => 10000,
				'step'    => 500,
				'description' => esc_html__('Set the speed for autoplay in milliseconds.', 'cubewp-framework'),
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'autoplay'      => 'yes',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'type'    => Controls_Manager::NUMBER,
				'label'   => esc_html__('Speed (ms)', 'cubewp-framework'),
				'default' => 500,
				'min'     => 100,
				'max'     => 5000,
				'step'    => 100,
				'description' => esc_html__('Set the speed for the slider transition in milliseconds.', 'cubewp-framework'),
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'autoplay!' => 'yes',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'type'    => Controls_Manager::SWITCHER,
				'label'   => esc_html__('Infinite Loop', 'cubewp-framework'),
				'default' => 'yes',
				'description' => esc_html__('Enable or disable infinite loop for the slider.', 'cubewp-framework'),
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'variable_width',
			[
				'label' => __('Variable Width', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'cubewp-framework'),
				'label_off' => __('No', 'cubewp-framework'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'overflow_setting',
			[
				'label' => esc_html__('Overflow Setting', 'cubewp-framework'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-list.draggable' => 'overflow: inherit;',
				],
			]
		);

		$this->add_control(
			'enable_progress_bar',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__('Enable Progress Bar', 'cubewp-framework'),
				'default' => '',
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'progress_bar_height',
			[
				'label' => esc_html__('Progress Bar Height', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .slick-progress, {{WRAPPER}} .slick-progress .slick-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'enable_progress_bar' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'progress_bar_back_color',
			[
				'label' => esc_html__('Progress Bar Background Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .slick-progress' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'enable_progress_bar' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'progress_bar_color',
			[
				'label' => esc_html__('Progress Bar Fill Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'selectors' => [
					'{{WRAPPER}} .slick-progress .slick-progress-bar' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'enable_progress_bar' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_bar_margin_top',
			[
				'label' => esc_html__('Progress Bar Margin Top', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-progress' => 'margin-top: {{SIZE}}px;',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'enable_progress_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_dots_arrow_settings_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_arrows',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__('Enable Arrows', 'cubewp-framework'),
				'default' => 'yes',
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label' => __('Previous Icon', 'cubewp-framework'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
				'label_block' => true,
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_icon',
			[
				'label' => __('Next Icon', 'cubewp-framework'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
				'label_block' => true,
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Icon Size (px)', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev i, {{WRAPPER}} .cubewp-post-slider .slick-next i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_color',
			[
				'label' => esc_html__('Icon Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev i, {{WRAPPER}} .cubewp-post-slider .slick-next i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_hover_color',
			[
				'label' => esc_html__('Icon Hover Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev:hover i, {{WRAPPER}} .cubewp-post-slider .slick-next:hover i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_background_color',
			[
				'label' => esc_html__('Icon & Svg Background Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_hover_background_color',
			[
				'label' => esc_html__('Icon & Svg Hover Background Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev:hover, {{WRAPPER}} .cubewp-post-slider .slick-next:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'svg_color',
			[
				'label' => esc_html__('SVG Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev svg path, {{WRAPPER}} .cubewp-post-slider .slick-next svg path' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'svg_hover_color',
			[
				'label' => esc_html__('SVG Hover Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#FF0000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev:hover svg path, {{WRAPPER}} .cubewp-post-slider .slick-next:hover svg path' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'svg_width',
			[
				'label' => esc_html__('SVG Width', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev svg, {{WRAPPER}} .cubewp-post-slider .slick-next svg' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'svg_height',
			[
				'label' => esc_html__('SVG Height', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev svg, {{WRAPPER}} .cubewp-post-slider .slick-next svg' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border',
			[
				'label' => esc_html__('Border', 'cubewp-framework'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => esc_html__('Border Radius', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_color',
			[
				'label' => esc_html__('Border Color', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_color_hover',
			[
				'label' => esc_html__('Border Color on Hover', 'cubewp-framework'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev:hover, {{WRAPPER}} .cubewp-post-slider .slick-next:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_border_transition',
			[
				'label' => esc_html__('Transition Duration', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'transition: background-color {{SIZE}}s, color {{SIZE}}s, border-color {{SIZE}}s;',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => esc_html__('Icon & Svg Padding', 'cubewp-framework'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_position_divider_heading',
			[
				'label' => esc_html__('Set the Icons Positions', 'cubewp-framework'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_top_position',
			[
				'label' => esc_html__('Top Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'    => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_bottom_position',
			[
				'label' => esc_html__('Bottom Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev, {{WRAPPER}} .cubewp-post-slider .slick-next' => 'bottom: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'    => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_prev_left_position',
			[
				'label' => esc_html__('Left Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-prev' => 'left: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'    => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_next_right_position',
			[
				'label' => esc_html__('Right Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-next' => 'right: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'    => [
					'cwp_enable_slider' => 'yes',
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_dots',
			[
				'label' => esc_html__('Enable Dots', 'cubewp-framework'),
				'type' => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'cubewp-framework'),
				'label_off'    => esc_html__('No', 'cubewp-framework'),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_display_flex',
			[
				'label' => esc_html__('Dots Display', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'block' => esc_html__('Block', 'cubewp-framework'),
					'flex' => esc_html__('Flex', 'cubewp-framework'),
				],
				'default' => 'flex',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'display: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_dots' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_flex_direction',
			[
				'label' => esc_html__('Dots Flex Direction', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'row' => esc_html__('Row', 'cubewp-framework'),
					'row-reverse' => esc_html__('Row Reverse', 'cubewp-framework'),
					'column' => esc_html__('Column', 'cubewp-framework'),
					'column-reverse' => esc_html__('Column Reverse', 'cubewp-framework'),
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_display_flex' => 'flex',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_gap',
			[
				'label' => __('Dots Gap', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'gap:{{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'cwp_enable_slider' => 'yes',
					'custom_dots' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_position_select',
			[
				'label' => esc_html__('Dots Position', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'static' => esc_html__('Static', 'cubewp-framework'),
					'absolute' => esc_html__('Absolute', 'cubewp-framework'),
					'relative' => esc_html__('Relative', 'cubewp-framework'),
					'fixed' => esc_html__('Fixed', 'cubewp-framework'),
				],
				'default' => 'static',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'position: {{VALUE}};',
				],
				'condition' => [
					'cwp_enable_slider' => 'yes',
					'custom_dots' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_top_position',
			[
				'label' => esc_html__('Dots Top Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_position_select' => 'absolute',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_bottom_position',
			[
				'label' => esc_html__('Dots Bottom Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'bottom: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_position_select' => 'absolute',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_left_position',
			[
				'label' => esc_html__('Dots Left Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'left: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_position_select' => 'absolute',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_right_position',
			[
				'label' => esc_html__('Dots Right Position', 'cubewp-framework'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'right: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_position_select' => 'absolute',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'dots_position_z_index',
			[
				'label' => esc_html__('Dots Z-Index', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -9999,
				'max' => 9999,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots' => 'z-index: {{VALUE}} !important;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'dots_position_select' => 'absolute',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_padding',
			[
				'label' => esc_html__('Dots Padding', 'cubewp-framework'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_border_style',
			[
				'label' => __('Dots Border Style', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'None',
				'options' => [
					'none' => __('None', 'cubewp-framework'),
					'solid' => __('Solid', 'cubewp-framework'),
					'dotted' => __('Dotted', 'cubewp-framework'),
					'dashed' => __('Dashed', 'cubewp-framework'),
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_border_width',
			[
				'label' => __('Dots Border Width', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
					'dots_border_style!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'dots_outside_padding',
			[
				'label' => esc_html__('Dots Outside Padding', 'cubewp-framework'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_outside_color',
			[
				'label' => esc_html__('Dots Outside Backgroud Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'active_dot_outside_color',
			[
				'label' => esc_html__('Active Dot Outside Backgroud Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots .slick-active' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_border_color',
			[
				'label' => __('Dots Border Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_active_border_color',
			[
				'label' => __('Active Dot Border Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots .slick-active' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_border_radius',
			[
				'label' => __('Dots Border Radius', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'max' => 500,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li,{{WRAPPER}} .cubewp-post-slider .slick-dots li button' => 'border-radius: {{VALUE}}px;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_background_color',
			[
				'label' => __('Dots Background Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_active_background_color',
			[
				'label' => __('Active Dot Background Color', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots .slick-active button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_width',
			[
				'label' => __('Dots Width', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 10,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li button' => 'width: {{VALUE}}px;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_height',
			[
				'label' => __('Dots Height', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 10,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots li button' => 'height: {{VALUE}}px;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'active_dot_width',
			[
				'label' => __('Active Dot Width', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots .slick-active button' => 'width: {{VALUE}}px;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'active_dot_height',
			[
				'label' => __('Active Dot Height', 'cubewp-framework'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .cubewp-post-slider .slick-dots .slick-active button' => 'height: {{VALUE}}px;',
				],
				'condition' => [
					'custom_dots' => 'yes',
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_responsive_settings_heading',
			[
				'label' => esc_html__('Responsive Settings For Slides To Show And Scroll', 'cubewp-framework'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_show_tablet',
			[
				'label' => esc_html__('Slides To Show On (Tablet)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_show_tablet_portrait',
			[
				'label' => esc_html__('Slides To Show On (Tablet Portrait)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 2,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_show_mobile',
			[
				'label' => esc_html__('Slides To Show On (Mobile)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_responsive_settings_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition'   => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_scroll_tablet',
			[
				'label' => esc_html__('Slides To Scroll On (Tablet)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_scroll_tablet_portrait',
			[
				'label' => esc_html__('Slides To Scroll On (Tablet Portrait)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'slides_to_scroll_mobile',
			[
				'label' => esc_html__('Slides To Scroll On (Mobile)', 'cubewp-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'cwp_enable_slider' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}
}
