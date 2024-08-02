<?php
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * CubeWP Posts Widgets.
 *
 * Elementor Widget For Posts By CubeWP.
 *
 * @since 1.0.0
 */

class CubeWp_Elementor_CubeWP_Form_Widget extends Widget_Base {

    public function get_name() {
        
        return 'custom_form_widget';
    }

    public function get_title() {
        return __( 'Custom Form Widget', 'elementor' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_form',
            [
                'label' => __( 'Form Settings', 'elementor' ),
            ]
        );
    
        $this->add_control(
            'form_type',
            [
                'label' => __( 'Select Form Type', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'search_fields' => __( 'Search 1', 'elementor' ),
                    'search_filters' => __( 'Filters 2', 'elementor' ),
                ],
            ]
        );
    
        $this->add_control(
            'post_type',
            [
                'label' => __( 'Select Post Type', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_post_types(),
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'section_style_form',
            [
                'label' => __( 'Form Style', 'elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        $this->add_control(
            'form_background_color',
            [
                'label' => __( 'Background Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-search-filters-fields' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .cwp-search-filters-fields',
            ]
        );
    
        $this->add_responsive_control(
            'form_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-search-filters-fields' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_responsive_control(
            'form_margin',
            [
                'label' => __( 'Margin', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-search-filters-fields' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'section_style_fields',
            [
                'label' => __( 'Form Fields Style', 'elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
    
        $this->add_control(
            'field_placeholder_text_color',
            [
                'label' => __( 'Placeholder Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_checkbox_text_color',
            [
                'label' => __( 'Checkbox Label Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container ul li label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_switch_text_color',
            [
                'label' => __( 'Switch Button Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-switch-container span' => 'color: {{VALUE}} !important;',
                ],
            ]
        );
    
        $this->add_control(
            'field_background_color',
            [
                'label' => __( 'Background Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea' => 'background-color: {{VALUE}};',
                ],
            ]
        );
    
        $this->add_control(
            'field_width',
            [
                'label' => __( 'Field Width', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'label' => __( 'Field Border', 'elementor' ),
                'name' => 'field_border',
                'selector' => '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'label' => __( 'Field Conatiner Border', 'elementor' ),
                'name' => 'field_container_border',
                'selector' => '{{WRAPPER}} .cwp-field-container',
            ]
        );
    
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'field_box_shadow',
                'selector' => '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea',
            ]
        );
    
        $this->add_responsive_control(
            'field_padding',
            [
                'label' => __( 'Field Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_container_padding',
            [
                'label' => __( 'Field Container Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_responsive_control(
            'field_margin',
            [
                'label' => __( 'Field Margin', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_container_margin',
            [
                'label' => __( 'Field Container Margin', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .cwp-field-container ul li label, {{WRAPPER}} .cwp-field-switch-container span, {{WRAPPER}} .cwp-field-container input, {{WRAPPER}} .cwp-field-container select, {{WRAPPER}} .cwp-field-container textarea',
            ]
        );
    
        $this->add_control(
            'field_display',
            [
                'label' => __( 'Display', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'block' => __( 'Block', 'elementor' ),
                    'inline-block' => __( 'Inline Block', 'elementor' ),
                    'inline' => __( 'Inline', 'elementor' ),
                    'flex' => __( 'Flex', 'elementor' ),
                    'grid' => __( 'Grid', 'elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container' => 'display: {{VALUE}};',
                ],
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'section_style_labels',
            [
                'label' => __( 'Labels Style', 'elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_label',
            [
                'type'      => Controls_Manager::SELECT,
                'label'     => esc_html__( 'Show Label', 'cubewp-framework' ),
                'options' => [
                    'block' => __( 'Yes', 'elementor' ),
                    'None' => __( 'No', 'elementor' ),
                ],
                'default'   => 'block',
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container > label, {{WRAPPER}} .cwp-checkbox-container > label, {{WRAPPER}} .cwp-switch-container > label' => 'display: {{VALUE}};',
                ],
            ]
        );   
    
        $this->add_control(
            'label_text_color',
            [
                'label' => __( 'Text Color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container > label, {{WRAPPER}} .cwp-checkbox-container > label, {{WRAPPER}} .cwp-switch-container > label' => 'color: {{VALUE}};',
                ],
            ]
        );             
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .cwp-field-container label',
            ]
        );
    
        $this->add_responsive_control(
            'label_margin',
            [
                'label' => __( 'Margin', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-field-container label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        $this->end_controls_section();
    }

    private function get_post_types() {
        $post_types = get_post_types( ['public' => true], 'objects' );
        $options = [];
        foreach ( $post_types as $post_type ) {
            $options[ $post_type->name ] = $post_type->label;
        }
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        CubeWp_Frontend_Search_Filter::get_filters_style_scripts();
        if ( isset( $settings['post_type'] ) ) {
            $post_type = $settings['post_type'];
            ?>
            <div class="cwp-search-filters-wrap">
            <form name="cwp-search-filters" class="cwp-search-filters method="post">   
            <div class="cwp-search-filters-fields">
            <?php

            echo CubeWp_Frontend_Search_Filter::filter_hidden_fields($post_type);
            $cwp_search_filters = CWP()->get_form('search_filters');
            CubeWp_Frontend_Search_Filter::$conditional_filters = isset($cwp_search_filters[$post_type]['form']['conditional_filters']) ? $cwp_search_filters[$post_type]['form']['conditional_filters'] : '0';
            if(!empty($cwp_search_filters[$post_type]['fields']) && count($cwp_search_filters[$post_type]['fields'])>0 ){
                if(isset($cwp_search_filters[$post_type]['fields']) && !empty($cwp_search_filters[$post_type]['fields'])){
                    foreach ($cwp_search_filters[$post_type]['fields'] as $field_name => $search_filter) {
                        if(($search_filter['type'] == 'number' || $search_filter['type'] == 'date_picker') && isset($search_filter['sorting']) && $search_filter['sorting'] == 1){
                            CubeWp_Frontend_Search_Filter::$sorting[$search_filter['label']] = $search_filter['name'];
                        }
                        echo CubeWp_Frontend_Search_Filter::get_filters_content($search_filter,$field_name);
                    }
                }
            }
        ?>
            </div>
            </form>
            </div>
        <?php

        } else {
            echo __( 'Post type not set gfrf', 'elementor' );
        }
    }
    
}
