<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;

/**
 * CubeWP Search Map Widgets.
 *
 * Elementor Widget For Search Map By CubeWP.
 *
 * @since 1.0.0
 */

 class CubeWp_Elementor_Archive_Map_Widget extends Widget_Base {

    public function get_name() {
        return 'search_map_display_widget';
    }

    public function get_title() {
        return __( 'Search Map Display Widget', 'elementor' );
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
                'label' => __( 'Map Settings', 'elementor' ),
            ]
        );

        $this->add_responsive_control(
            'map_height',
            [
                'label' => __( 'Map Height', 'elementor' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-archive-content-map' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <script>
            CWP_Cluster_Map();
        </script>
        <div class="cwp-archive-content-map"></div>
        <?php
    }

    
}