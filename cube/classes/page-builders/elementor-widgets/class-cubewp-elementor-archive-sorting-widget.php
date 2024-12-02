<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;

/**
 * CubeWP Sorting Widgets.
 *
 * Elementor Widget For Sorting By CubeWP.
 *
 * @since 1.0.0
 */

 class CubeWp_Elementor_Archive_Sorting_Widget extends Widget_Base {

    public function get_name() {
        return 'sorting_widget';
    }

    public function get_title() {
        return __( 'Sorting Fields Display', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-sort-amount-desc';
    }

    public function get_categories() {
        return [ 'cubewp' ];
    }

    protected function _register_controls() {
        // $this->start_controls_section(
        //     'section_map',
        //     [
        //         'label' => __( 'Map Settings', 'elementor' ),
        //     ]
        // );

        // $this->end_controls_section();
    }

    protected function render() {
        echo CubeWp_Frontend_Search_Filter::cwp_filter_sorting();
    }
}