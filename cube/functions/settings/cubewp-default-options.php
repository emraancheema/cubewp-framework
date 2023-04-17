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
			'desc'    => __( 'Gives you a range bar in google address field on search and filter.', 'cubewp-framework' ),
		),
		array(
			'id'       => 'google_address_min_radius',
			'type'     => 'text',
			'title'    => __( 'Minimum Radius', 'cubewp-framework' ),
			'default'  => '5',
			'desc'     => __( 'Minimum radius for google address field on search and filter.', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		),
		array(
			'id'       => 'google_address_default_radius',
			'type'     => 'text',
			'title'    => __( 'Default Radius', 'cubewp-framework' ),
			'default'  => '30',
			'desc'     => __( 'Default radius for google address field on search and filter.', 'cubewp-framework' ),
			'required' => array(
				array( 'google_address_radius', 'equals', '1' )
			)
		),
		array(
			'id'       => 'google_address_max_radius',
			'type'     => 'text',
			'title'    => __( 'Maximum Radius', 'cubewp-framework' ),
			'default'  => '500',
			'desc'     => __( 'Maximum radius for google address field on search and filter.', 'cubewp-framework' ),
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
			'desc'     => __( 'Unit of radius for google address field on search and filter.', 'cubewp-framework' ),
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
			'desc'    => __( 'Get your Google Maps API Key here. <br> https://developers.google.com/maps/documentation/javascript/get-api-key', 'cubewp-framework' ),
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
			'desc'    => __( 'Write Valid Latitude For Default Map.', 'cubewp-framework' ),
			'type'    => 'text',
			'default' => '40.68924104083928',
		),
		array(
			'id'      => 'map_longitude',
			'title'   => __( 'Set Map Default Longitude', 'cubewp-framework' ),
			'desc'    => __( 'Write Valid Longitude For Default Map.', 'cubewp-framework' ),
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
			'id'      => 'cubewp_archive',
			'title'   => __( 'CubeWP Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off CubeWP custom archive page.' ),
			'type'    => 'switch',
			'default' => '1',
		),
		array(
			'id'      => 'archive_map',
			'title'   => __( 'Map on Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off map on archive page.' ),
			'type'    => 'switch',
			'default' => '0',
			'required' => array(
				array( 'cubewp_archive', 'equals', '1' )
			)
		),
		array(
			'id'      => 'archive_filters',
			'title'   => __( 'Filters on Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off filters on archive page.' ),
			'type'    => 'switch',
			'default' => '0',
			'required' => array(
				array( 'cubewp_archive', 'equals', '1' )
			)
		),
		array(
			'id'      => 'archive_sort_filter',
			'title'   => __( 'Sorting filter on Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off sorting filter on archive page.' ),
			'type'    => 'switch',
			'default' => '1',
			'required' => array(
				array( 'cubewp_archive', 'equals', '1' )
			)
		),
		array(
			'id'      => 'archive_layout',
			'title'   => __( 'Layout switcher on Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off layout switcher on archive page.' ),
			'type'    => 'switch',
			'default' => '1',
			'required' => array(
				array( 'cubewp_archive', 'equals', '1' )
			)
		),
		array(
			'id'      => 'archive_found_text',
			'title'   => __( 'Found Text on Archive', 'cubewp-framework' ),
			'desc'    => __( 'You can easily on/off found text on archive page.' ),
			'type'    => 'switch',
			'default' => '1',
			'required' => array(
				array( 'cubewp_archive', 'equals', '1' )
			)
		),
	)
 );
 
$conditional_options = array();
if ( cubewp_check_if_elementor_active() && ! cubewp_check_if_elementor_active(true) && ! class_exists("CubeWp_Frontend_Load") ) {
   	$pages   = get_pages( array( "fields" => "ids" ) );
   	$options = array();
   	if ( ! empty( $pages ) && !is_null(Elementor\Plugin::$instance->documents) ) {
		foreach ( $pages as $page ) {
			$document = Elementor\Plugin::$instance->documents->get( $page->ID );
			if ( $document && $document->is_built_with_elementor() && $document->is_editable_by_current_user() ) {
				$options[ $page->ID ] = $page->post_title;
			}
		}
   	}
   	$conditional_options[] = array(
		'id'      => 'post_type_for_elementor_page',
		'type'    => 'select',
		'title'   => __( 'Post-Type For Elementor Single Page', 'cubewp-framework' ),
		'options' => cwp_post_types(),
		'desc'    => __( 'Please select post-type for Elementor single page template. If you want to use custom page with multiple post-types please download <a href="https://cubewp.com/cubewp-frontend-pro/" target="_blank">CubeWP Frontend Pro</a>.', 'cubewp-framework' ),
			'required' => array(
				array( 'cubewp_singular', 'equals', '1' )
			)
   	);
   	$conditional_options[] = array(
		'id'      => 'custom_elementor_page',
		'type'    => 'select',
		'title'   => __( 'Elementor Single Page', 'cubewp-framework' ),
		'options' => $options,
		'desc'    => __( 'Please select Elementor single page template.', 'cubewp-framework' ),
		'required' => array(
			array( 'post_type_for_elementor_page', '!=', '' )
		)
   	);
}
if ( cubewp_check_if_elementor_active() && ! cubewp_check_if_elementor_active(true) ) {
	$conditional_options[] = array(
	   'id'      => 'cubewp_ignore_theme_single',
	   'title'   => __( 'Overwrite Theme Single Template', 'cubewp-framework' ),
	   'desc'    => __( 'Enable if you also want to overwrite post type single page theme layout.' ),
	   'type'    => 'switch',
	   'default' => '0',
	);
}
$settings['post_settings'] = array(
	'title'  => __( 'Post Settings', 'cubewp-framework' ),
	'id'     => 'post_settings',
	'fields' => array_merge(
		array(
			array(
				'id'      => 'cubewp_singular',
				'title'   => __( 'CubeWP Single Page', 'cubewp-framework' ),
				'desc'    => __( 'You can easily on/off CubeWP custom single page.' ),
				'type'    => 'switch',
				'default' => '1',
			),
			array(
				'id'      => 'post_type_save_button',
				'type'    => 'switch',
				'title'   => __( 'Post Save Button', 'cubewp-framework' ),
				'default' => '1',
				'desc'    => __( 'Gives you a button on single page to save post type.', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' )
				)
			),
			array(
				'id'      => 'post_type_share_button',
				'type'    => 'switch',
				'title'   => __( 'Post Share Button', 'cubewp-framework' ),
				'default' => '1',
				'desc'    => __( 'Gives you a button on single page to share post type.', 'cubewp-framework' ),
				'required' => array(
					array( 'cubewp_singular', 'equals', '1' )
				)
			),
			array(
				'id'       => 'twitter_share',
				'type'     => 'switch',
				'title'    => __( 'Post Twitter Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on twitter.', 'cubewp-framework' ),
				'required' => array(
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'facebook_share',
				'type'     => 'switch',
				'title'    => __( 'Post Facebook Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on facebook.', 'cubewp-framework' ),
				'required' => array(
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'pinterest_share',
				'type'     => 'switch',
				'title'    => __( 'Post Pinterest Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on pinterest.', 'cubewp-framework' ),
				'required' => array(
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'linkedin_share',
				'type'     => 'switch',
				'title'    => __( 'Post LinkedIn Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on linkedIn.', 'cubewp-framework' ),
				'required' => array(
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
			array(
				'id'       => 'reddit_share',
				'type'     => 'switch',
				'title'    => __( 'Post Reddit Share', 'cubewp-framework' ),
				'default'  => '1',
				'desc'     => __( 'By enabling this option, you can share post on reddit.', 'cubewp-framework' ),
				'required' => array(
					array( 'post_type_share_button', 'equals', '1' )
				)
			),
		),$conditional_options
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
			'desc'    => __( 'If you have your author page template by any theme or plugin then you do not need to enable this option, Otherwise you can enable cubewp Author page template ', 'cubewp-framework' ),
		),
		array(
			'id'      => 'author_banner_image',
			'type'    => 'media',
			'title'   => __( 'CubeWP Author Banner Image', 'cubewp-framework' ),
			'default' => '',
			'desc'    => __( 'Please upload banner image for author page.', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'      => 'author_share_button',
			'type'    => 'switch',
			'title'   => __( 'Author Share Button', 'cubewp-framework' ),
			'default' => '1',
			'desc'    => __( 'Gives you a share button on author page.', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_edit_profile',
			'type'     => 'switch',
			'title'    => __( 'Author Edit Profile', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author can edit profile from author page.', 'cubewp-framework' ),
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
			'title'    => __( 'Author Contact Info', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author contact info will be visible on author page.', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_post_types',
			'type'     => 'select',
			'multi'   =>  true,
			'title'    => __( 'Select Post types', 'cubewp-reviews' ),
			'subtitle' => '',
			'desc'     => __( 'Tabs for above selected post types will be added other than all posts tab on author page.', 'cubewp-reviews' ),
			'options'  => cwp_post_types(),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
		),
		array(
			'id'       => 'author_custom_fields',
			'type'     => 'switch',
			'title'    => __( 'Author Custom FIeld', 'cubewp-framework' ),
			'default'  => '1',
			'desc'     => __( 'By enabling this option, author custom fields will be shown on author page.', 'cubewp-framework' ),
			'required' => array(
				array( 'show_author_template', 'equals', '1' )
			)
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
			'desc'    => __( 'Put CSS code above. It will be enqueued on frontend only.', 'cubewp-framework' ),
		),
		array(
			'id'      => 'cubewp-js',
			'type'    => 'ace_editor',
			'mode'    => 'javascript',
			'title'   => __( 'JS or jQ ( JavaScript Or jQuery )', 'cubewp-framework' ),
			'desc'    => __( 'Put JS code above. It will be enqueued on frontend only.', 'cubewp-framework' ),
		),
	)
);
return $settings;