<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;

/**
 * CubeWP Result Data Widgets.
 *
 *
 * @since 1.0.0
 */

 class CubeWp_Elementor_Archive_Result_Data_Widget extends Widget_Base {

    public function get_name() {
        return 'result_data_widget';
    }

    public function get_title() {
        return __( 'Result Data Display', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-number-field';
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
        $output = '<div class="cwp-filtered-result-count">';
        $data =  esc_html__( '0 Results Found', 'cubewp-framework' );;
        $output .= '<div class="cwp-total-results">'. $data .'</div>';
        $output .= '</div>';
        
        echo $output;
    }
}