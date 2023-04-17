<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !cubewp_check_if_elementor_active() || cubewp_check_if_elementor_active(true)) {
	echo cwp_alert_ui('Sorry! "CubeWP Single Post" page template only works with elementor (Free).');
	return false;
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

get_header();
/**
 * Before Header-Footer page template content.
 *
 * Fires before the content of Elementor Header-Footer page template.
 *
 * @since 2.0.0
 */
do_action( 'elementor/page_templates/header-footer/before_content' );

\Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content();

/**
 * After Header-Footer page template content.
 *
 * Fires after the content of Elementor Header-Footer page template.
 *
 * @since 2.0.0
 */
do_action( 'elementor/page_templates/header-footer/after_content' );

get_footer();