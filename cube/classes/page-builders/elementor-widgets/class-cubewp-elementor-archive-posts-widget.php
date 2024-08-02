<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;

/**
 * CubeWP Search Posts Widgets.
 *
 * Elementor Widget For Search Posts By CubeWP.
 *
 * @since 1.0.0
 */

 class CubeWp_Elementor_Archive_Posts_Widget extends Widget_Base {

    public function get_name() {
        return 'search_posts_widget';
    }

    public function get_title() {
        return __( 'Search Posts Display Widget', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-map-pin';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_map',
            [
                'label' => __( 'Search Posts Settings', 'elementor' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        CubeWp_Frontend_Search_Filter::get_filters_style_scripts();
        echo CubeWp_Frontend_Search_Filter::cwp_filter_results(); 
    }

    
}