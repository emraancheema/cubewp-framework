<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * CubeWP Taxonomies Widgets.
 *
 * Elementor Widget For Taxonomies By CubeWP.
 *
 * @since 1.0.0
 */
class CubeWp_Elementor_Taxonomy_Widget extends Widget_Base {

	public function get_name() {
		return 'cubewp_taxonomy';
	}

	public function get_title() {
		return esc_html__( 'CubeWP Taxonomy', 'cubewp-framework' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
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
		$args       = array(
			'public'   => true,
			'_builtin' => false
		);
		$taxonomies = get_taxonomies( $args );
		$taxonomies = self::cwp_get_taxonomies_label($taxonomies);
		$this->start_controls_section( 'cubewp_widgets_section', array(
			'label' => esc_html__( 'Widget Options', 'cubewp-framework' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'taxonomy', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Taxonomy', 'cubewp-framework' ),
			'options' => $taxonomies,
		) );
		$this->add_control( 'terms_per_page', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'No Of Terms To Show', 'cubewp-framework' ),
			'options' => array(
				'0'  => esc_html__( 'Show All Terms', 'cubewp-framework' ),
				'3'  => esc_html__( 'Show 3 Terms', 'cubewp-framework' ),
				'4'  => esc_html__( 'Show 4 Terms', 'cubewp-framework' ),
				'5'  => esc_html__( 'Show 5 Terms', 'cubewp-framework' ),
				'6'  => esc_html__( 'Show 6 Terms', 'cubewp-framework' ),
				'8'  => esc_html__( 'Show 8 Terms', 'cubewp-framework' ),
				'9'  => esc_html__( 'Show 9 Terms', 'cubewp-framework' ),
				'12' => esc_html__( 'Show 12 Terms', 'cubewp-framework' ),
				'16' => esc_html__( 'Show 16 Terms', 'cubewp-framework' ),
				'15' => esc_html__( 'Show 15 Terms', 'cubewp-framework' ),
				'20' => esc_html__( 'Show 20 Terms', 'cubewp-framework' )
			),
			'default' => '0'
		) );
		$this->add_control( 'output_style', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'Select Output Style', 'cubewp-framework' ),
			'options' => array(
				'boxed_view' => esc_html__( 'Boxed View', 'cubewp-framework' ),
				'list_view'  => esc_html__( 'List View', 'cubewp-framework' ),
			),
			'default' => 'boxed_view'
		) );
		$this->add_control( 'icon_media_name', array(
			'type'        => Controls_Manager::TEXT,
			'label'       => esc_html__( 'Icon Or Image', 'cubewp-framework' ),
			'description' => esc_html__( 'Enter taxonomy custom field slug for term icon or image.', 'cubewp-framework' ),
			'condition'   => array(
				'output_style' => 'boxed_view',
			),
		) );
		$this->add_control( 'column_per_row', array(
			'type'    => Controls_Manager::SELECT,
			'label'   => esc_html__( 'No Of Columns Per Row', 'cubewp-framework' ),
			'options' => array(
				'1' => esc_html__( '1 Column Per Row', 'cubewp-framework' ),
				'2' => esc_html__( '2 Columns Per Row', 'cubewp-framework' ),
				'3' => esc_html__( '3 Columns Per Row', 'cubewp-framework' ),
				'4' => esc_html__( '4 Columns Per Row', 'cubewp-framework' ),
				'6' => esc_html__( '6 Columns Per Row', 'cubewp-framework' ),
				'0' => esc_html__( 'Auto Adjust Columns Per Row', 'cubewp-framework' )
			),
			'default' => '4'
		) );
		$this->add_control( 'child_terms', array(
			'type'    => Controls_Manager::SWITCHER,
			'label'   => esc_html__( 'Show Child Terms', 'cubewp-framework' ),
			'default' => 'no'
		) );
		$this->add_control( 'hide_empty', array(
			'type'    => Controls_Manager::SWITCHER,
			'label'   => esc_html__( 'Hide Empty Terms', 'cubewp-framework' ),
			'default' => 'no'
		) );
		$repeater = new Repeater();
		$repeater->add_control( 'term_box_color', array(
			'label'       => esc_html__( 'Color', 'cubewp-framework' ),
			'type'        => Controls_Manager::COLOR,
			'selectors'   => array(
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}'
			),
			'label_block' => true,
		) );
		$this->add_control( 'terms_box_color', array(
			'label'       => esc_html__( 'Terms Box Color', 'cubewp-framework' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => array(
				array(
					'term_box_color' => '#faf7d9',
				),
				array(
					'term_box_color' => '#e1f0ee',
				),
				array(
					'term_box_color' => '#fcece3',
				),
				array(
					'term_box_color' => '#e3effb',
				),
				array(
					'term_box_color' => '#ffeff7',
				),
			),
			'title_field' => '{{{ term_box_color }}}',
			'condition'   => array(
				'output_style' => 'boxed_view',
			),
		) );
		$this->end_controls_section();
	}
	
	public function cwp_get_taxonomies_label($taxonomies)
	{
		$taxonomy_labels = array();
		if (!empty($taxonomies) && is_array($taxonomies))
			foreach ($taxonomies as $slug => $taxonomy) {
				if (taxonomy_exists($taxonomy)) {
					$label = get_taxonomy($taxonomy)->labels->name;
					$taxonomy_labels[$slug] = $label;
				}
			}
		return $taxonomy_labels;
	}
	
	protected function render() {
		$settings = $this->get_settings_for_display();
		$args     = array(
			'taxonomy'        => $settings['taxonomy'],
			'terms_per_page'  => $settings['terms_per_page'],
			'output_style'    => $settings['output_style'],
			'child_terms'     => $settings['child_terms'],
			'hide_empty'      => $settings['hide_empty'],
			'icon_media_name' => $settings['icon_media_name'],
			'column_per_row'  => $settings['column_per_row'],
			'terms_box_color' => $settings['terms_box_color']
		);

		echo apply_filters( 'cubewp_shortcode_taxonomy_output', '', $args );
	}
}