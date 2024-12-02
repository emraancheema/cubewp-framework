<?php
/**
 * Creates the cubewp settings fields in wordpress admin.
 *
 * @version 1.0
 * @package cubewp/cube/functions/settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$all_post_types = get_post_types( array( 'public' => true ) );
$exclude_post_types = CWP_types();
$exclude_post_types['cubewp-report'] = 'cubewp-report';
$exclude_post_types['cwp_reviews'] = 'cwp_reviews';
$exclude_post_types['attachment'] = 'attachment';
$exclude_post_types['post'] = 'post';
$exclude_post_types = apply_filters( 'cubewp/settings/excluded/external/post_types', $exclude_post_types );
$external_post_types = array_diff_key( $all_post_types, $exclude_post_types );
$settings['general-settings'] = array(
	'title'  => __( 'General', 'cubewp-framework' ),
	'id'     => 'general-settings',
	'fields' => array(
		array(
			'id'      => 'external_cpt_into_cubewp',
			'type'    => 'switch',
			'title'   => __( 'External Custom Post Types Into CubeWP Builders', 'cubewp-framework' ),
			'desc'    => __( 'Enable if you want to add custom post types created by code or 3rd party plugins into CubeWP builders', 'cubewp-framework' ),
			'default' => '0',
		),
		array(
			'id'       => 'external_cpt_for_cubewp_builders',
			'type'     => 'select',
			'title'    => __( 'Select Post Types', 'cubewp-framework' ),
			'options'  => $external_post_types,
			'multi'   =>  true,
			'desc'     => __( 'Select custom post types created by code or 3rd party plugins', 'cubewp-framework' ),
			'required' => array(
				array( 'external_cpt_into_cubewp', 'equals', '1' )
			)
		),
		array(
			'id'      => 'delete_custom_posts_attachments',
			'type'    => 'switch',
			'title'   => __( 'Delete Attachments Upon Post Deletion', 'cubewp-framework' ),
			'desc'    => __( 'Enable this option if you wish to delete post attachments along with the post when you delete it from the trash.', 'cubewp-framework' ),
			'default' => '0',
		),
	)
);
$settings['search_filters'] = array(
	'title'  => __( 'Search & Filters', 'cubewp-framework' ),
	'id'     => 'search_filters',
	'icon'   => 'dashicons-filter',
	'fields' => array(
		array(
			'id'      => 'google_address_radius',
			'type'    => 'switch',
			'title'   => __( 'Google Address Search Radius', 'cubewp-framework' ),
			'default' => '1',
			'desc'    => __( 'Gives you a range bar in google address field on search and filter', 'cubewp-framework' ),
		),
		array(
			'id'       => 'google_address_min_radius',
			'type'     => 'text',
			'title'    => __( 'Minimum Radius', 'cubewp-framework' ),
			'default'  => '5',
			'desc'     => __( 'Minimum radius for google address field on search and filter', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		),
		array(
			'id'       => 'google_address_default_radius',
			'type'     => 'text',
			'title'    => __( 'Default Radius', 'cubewp-framework' ),
			'default'  => '30',
			'desc'     => __( 'Default radius for google address field on search and filter', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		),
		array(
			'id'       => 'google_address_max_radius',
			'type'     => 'text',
			'title'    => __( 'Maximum Radius', 'cubewp-framework' ),
			'default'  => '500',
			'desc'     => __( 'Maximum radius for google address field on search and filter', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		),
		array(
			'id'       => 'google_address_radius_unit',
			'type'     => 'select',
			'title'    => __( 'Radius Unit', 'cubewp-framework' ),
			'options'  => array(
				'mi' => __( 'Mile', 'cubewp-framework' ),
				'km' => __( 'Kilometre', 'cubewp-framework' )
			),
			'default'  => 'mi',
			'desc'     => __( 'Unit of radius for google address field on search and filter', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		)
	)
);

$settings['map']    = array(
	'title'  => __( 'Map', 'cubewp-framework' ),
	'id'     => 'map',
	'icon'   => 'dashicons-location-alt',
	'fields' => array(
		array(
			'id'      => 'google_map_api',
			'title'   => __( 'Google Map & Places API Key', 'cubewp-framework' ),
			'desc'    => __( 'Get your Google Maps API Key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>.', 'cubewp-framework' ),
			'type'    => 'text',
			'default' => '',
		),
		array(
			'id'      => 'map_option',
			'type'    => 'select',
			'title'   => __( 'Map Type', 'cubewp-framework' ),
			'options' => array(
				'openstreet' => 'OpenStreet Map',
				'google'     => 'Google Map',
				'mapbox'     => 'MapBox API',
			),
			'default' => 'openstreet',
		),
		array(
			'id'       => 'mapbox_token',
			'type'     => 'text',
			'title'    => __( 'Mapbox Token', 'cubewp-framework' ),
			'subtitle' => __( 'Put here MapBox token, If you leave it empty then Google map will work', 'cubewp-framework' ),
			'desc'     => __( 'Get your Mapbox Key here.<br>https://account.mapbox.com/access-tokens/create', 'cubewp-framework' ),
			'default'  => '',
			'required' => array(
				array( 'map_option', 'equals', 'mapbox' )
			)
		),
		array(
			'id'       => 'map_style',
			'type'     => 'text',
			'title'    => esc_html__( 'Mapbox Map Style Id', 'cubewp-framework' ),
			'subtitle' => esc_html__( 'Type Your Custom Style ID', 'cubewp-framework' ),
			'desc'     => esc_html__( 'Type how you want the Mapbox map to show. Only use YOUR_USERNAME/YOUR_STYLE_ID No slashes before and after.', 'cubewp-framework' ),
			'default'  => 'mapbox/streets-v11',
			'required' => array(
				array( 'map_option', 'equals', 'mapbox' )
			)
		),
		array(
			'id'      => 'map_zoom',
			'title'   => __( 'Set Map Default Zoom Level', 'cubewp-framework' ),
			'desc'    => __( 'Write Value Between 1 - 18 For Default Zoom Level', 'cubewp-framework' ),
			'type'    => 'text',
			'default' => '15',
		),
		array(
			'id'      => 'map_latitude',
			'title'   => __( 'Set Map Default Latitude', 'cubewp-framework' ),
			'desc'    => __( 'Write Valid Latitude For Default Map', 'cubewp-framework' ),
			'type'    => 'text',
			'default' => '40.68924104083928',
		),
		array(
			'id'      => 'map_longitude',
			'title'   => __( 'Set Map Default Longitude', 'cubewp-framework' ),
			'desc'    => __( 'Write Valid Longitude For Default Map', 'cubewp-framework' ),
			'type'    => 'text',
			'default' => '-74.04450284527532',
		),
	)
);

$settings['archive_settings']    = array(
	'title'  => __( 'Archive Settings', 'cubewp-framework' ),
	'id'     => 'archive_settings',
	'icon'   => 'dashicons-archive',
	'fields' => array(
		array(
			'id'      => 'archive_map',
			'title'   => __( 'Map', 'cubewp-framework' ),
			'desc'    => __( 'You can easily On/Off map on CubeWP default archive page' ),
			'type'    => 'switch',
			'default' => '0',
		),
		array(
			'id'      => 'archive_filters',
			'title'   => __( 'Filters', 'cubewp-framework' ),
			'desc'    => __( 'You can easily On/Off filters on CubeWP default archive page' ),
			'type'    => 'switch',
			'default' => '0',
		),
		array(
			'id'      => 'archive_sort_filter',
			'title'   => __( 'Sorting Filter', 'cubewp-framework' ),
			'desc'    => __( 'You can easily On/Off sorting filter on CubeWP default archive page' ),
			'type'    => 'switch',
			'default' => '1',
		),
		array(
			'id'      => 'archive_layout',
			'title'   => __( 'Layout Switcher', 'cubewp-framework' ),
			'desc'    => __( 'You can easily On/Off layout switcher on CubeWP default archive page' ),
			'type'    => 'switch',
			'default' => '1',
		),
		array(
			'id'      => 'archive_found_text',
			'title'   => __( 'Found Text', 'cubewp-framework' ),
			'desc'    => __( 'You can easily On/Off found text on CubeWP default archive page' ),
			'type'    => 'switch',
			'default' => '1',
		),
		array(
			'id'      => 'archive_posts_per_page',
			'title'   => __( 'Posts Per Page', 'cubewp-framework' ),
			'desc'    => __( 'Set number of posts per page in search for CubeWP default archive page' ),
			'type'    => 'text',
			'default' => 10,
		),
	)
 );
 
$settings['post_settings'] = array(
	'title'  => __( 'Post Settings', 'cubewp-framework' ),
	'id'     => 'post_settings',
	'fields' => array_merge(
		array(
			array(
				'id'       => 'cwp_loop_style',
				'type'     => 'post_type_assignment',
				'title'    => __( 'Create Post Card Styles', 'cubewp-framework' ),
				'parent_options'  => CWP_all_post_types('settings'),
				'child_type'  => 'text',
				'multi'  => true,
				'desc'     => __( 'Select post type and assign style names to post type and then you can create different styles output using CubeWP Post Card Builder', 'cubewp-framework' ),
			),
			array(
				'id'      => 'cubewp_singular',
				'title'   => __( 'CubeWP Single Post Layout Builder', 'cubewp-framework' ),
				'desc'    => __( 'Enable/Disable CubeWP Single Post Layout Builder for managing default single post layout if you are not using CubeWP Theme Builder.' ),
				'type'    => 'switch',
				'default' => '1',
			),
			array(
				'id'      => 'post_type_save_button',
				'type'    => 'switch',
				'title'   => __( 'Save Button', 'cubewp-framework' ),
				'default' => '1',
				'desc'    => __( 'Gives you a button on single page to save post type', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' )
				)
			),
			array(
				'id'      => 'post_type_share_button',
				'type'    => 'switch',
				'title'   => __( 'Share Button', 'cubewp-framework' ),
				'default' => '1',
				'desc'    => __( 'Gives you a button on single page to share post type', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' )
				)
			),
			array(
				'id'       => 'twitter_share',
				'type'     => 'switch',
				'title'    => __( 'X (twitter) Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on X (twitter)', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' ),
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'facebook_share',
				'type'     => 'switch',
				'title'    => __( 'Facebook Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on facebook', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' ),
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'pinterest_share',
				'type'     => 'switch',
				'title'    => __( 'Pinterest Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on pinterest', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' ),
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'linkedin_share',
				'type'     => 'switch',
				'title'    => __( 'LinkedIn Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on linkedIn', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' ),
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'reddit_share',
				'type'     => 'switch',
				'title'    => __( 'Reddit Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on reddit', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' ),
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
		)
	)
);
$settings['author_settings'] = array(
	'title'  => __( 'Author Settings', 'cubewp-framework' ),
	'id'     => 'author_settings',
	'fields' => array(
		array(
			'id'      => 'show_author_template',
			'type'    => 'switch',
			'title'   => __( 'Author Page Template', 'cubewp-framework' ),
			'default' => '0',
			'desc'    => __( 'If you have your author page template by any theme or plugin then you do not need to enable this option, Otherwise you can enable CubeWP Author page template ', 'cubewp-framework' ),
		),
		array(
			'id'      => 'author_banner_image',
			'type'    => 'media',
			'title'   => __( 'Banner Image', 'cubewp-framework' ),
			'default' => '',
			'desc'    => __( 'Please upload banner image for author page', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'      => 'author_share_button',
			'type'    => 'switch',
			'title'   => __( 'Share Button', 'cubewp-framework' ),
			'default' => '1',
			'desc'    => __( 'Gives you a share button on author page', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_edit_profile',
			'type'     => 'switch',
			'title'    => __( 'Edit Profile', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author can edit profile from author page', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'profile_page',
			'type'     => 'pages',
			'title'    => __('User Profile Form Page', 'cubewp'),
			'subtitle' => __('This must be an URL.', 'cubewp'),
			'validate' => 'url',
			'desc'     => __('Select the page used for the User Profile Form (Page must include the Profile Form Shortcode)', 'cubewp'),
			'default'  => '',
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_contact_info',
			'type'     => 'switch',
			'title'    => __( 'Contact Info', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author contact info will be visible on author page', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_post_types',
			'type'     => 'select',
			'multi'   =>  true,
			'title'    => __( 'Select Post Types', 'cubewp-reviews' ),
			'subtitle' => '',
			'desc'     => __( 'Tabs for above selected post types will be added other than all posts tab on author page', 'cubewp-reviews' ),
			'options'  => cwp_post_types(),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_custom_fields',
			'type'     => 'switch',
			'title'    => __( 'User Custom Field', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author custom fields will be shown on author page', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
	)
);

$settings['cubewp-theme-builder'] = array(
	'title'  => __( 'Theme Builder', 'cubewp-framework' ),
	'id'     => 'cubewp-theme-builder',
	'icon'   => 'dashicons-editor-code',
	'fields' => array(
		array(
			'id'       => 'cwp_tb_hooks',
			'type'     => 'repeating_field',
			'title'    => __( 'Add WordPress Hooks For Theme builder', 'cubewp-framework' ),
			'child_type'  => 'text',
			'desc'     => __( 'Add WordPress Hooks here and then you will be able to select these hooks in theme builder and you can create template to show on these hooks wherever you want.', 'cubewp-framework' ),
		),
	)
);

$settings['cubewp-css-js'] = array(
	'title'  => __( 'CSS & JS', 'cubewp-framework' ),
	'id'     => 'cubewp-css-js',
	'icon'   => 'dashicons-editor-code',
	'fields' => array(
		array(
			'id'      => 'cubewp-css',
			'type'    => 'ace_editor',
			'mode'    => 'css',
			'title'   => __( 'CSS ( Cascading Style Sheets )', 'cubewp-framework' ),
			'desc'    => __( 'Put CSS code above. It will be enqueued on frontend only', 'cubewp-framework' ),
		),
		array(
			'id'      => 'cubewp-js',
			'type'    => 'ace_editor',
			'mode'    => 'javascript',
			'title'   => __( 'JS or jQ ( JavaScript Or jQuery )', 'cubewp-framework' ),
			'desc'    => __( 'Put JS code above. It will be enqueued on frontend only', 'cubewp-framework' ),
		),
	)
);
return $settings;