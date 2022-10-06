<?php
/**
 * Creates the cubewp settings fields in wordpress admin.
 *
 * @version 1.0
 * @package cubewp/cube/functions/settings
 */

if ( ! defined('ABSPATH')) {
	exit;
}

$settings['general'] = array(
	'title'  => __('General', 'cubewp-framework'),
	'id'     => 'general',
	'icon'   => 'dashicons-admin-settings',
	'fields' => array(
	   array(
		  'id'       => 'post_admin_approved',
		  'type'     => 'select',
		  'title'    => __( 'Submitted Listing Require Approval', 'cubewp-framework'),
		  'subtitle' => '',
		  'desc'     => __( 'Enable to require listings to be approved by an admin before published. If disabled listing will automatically publish after submission and payment made if applicable. Note: If paying via Bank Transfer listings will still require manual approval.', 'cubewp-framework' ),
		  'options'  =>  array(
			 'pending' => 'Yes',
			 'publish' => 'No',
		  ),
		  'default' => 'pending',
	   )
	)
 );

$settings['search_filters'] = array(
	'title'  => __('Search & Filters', 'cubewp-framework'),
	'id'     => 'search_filters',
	'icon'   => 'dashicons-filter',
	'fields' => array(
	   array(
		  'id'      => 'google_address_radius',
		  'type'    => 'switch',
		  'title'   => __('Google Address Search Radius', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('Gives you a range bar in google address field on search and filter.', 'cubewp-framework'),
	   ),
	   array(
		  'id'      => 'google_address_min_radius',
		  'type'    => 'text',
		  'title'   => __('Minimum Radius', 'cubewp-framework'),
		  'default' => '5',
		  'desc'    => __('Minimum radius for google address field on search and filter.', 'cubewp-framework'),
		  'required' => array(
			 array('google_address_radius', 'equals', '1')
		  )
	   ),
	   array(
		  'id'      => 'google_address_default_radius',
		  'type'    => 'text',
		  'title'   => __('Default Radius', 'cubewp-framework'),
		  'default' => '30',
		  'desc'    => __('Default radius for google address field on search and filter.', 'cubewp-framework'),
		  'required' => array(
			 array('google_address_radius', 'equals', '1')
		  )
	   ),
	   array(
		  'id'      => 'google_address_max_radius',
		  'type'    => 'text',
		  'title'   => __('Maximum Radius', 'cubewp-framework'),
		  'default' => '500',
		  'desc'    => __('Maximum radius for google address field on search and filter.', 'cubewp-framework'),
		  'required' => array(
			 array('google_address_radius', 'equals', '1')
		  )
	   ),
	   array(
		  'id'      => 'google_address_radius_unit',
		  'type'    => 'select',
		  'title'   => __('Radius Unit', 'cubewp-framework'),
		  'options' => array(
			 'mi' => __('Mile', 'cubewp-framework'),
			 'km' => __('Kilometre', 'cubewp-framework')
		  ),
		  'default' => 'mi',
		  'desc'    => __('Unit of radius for google address field on search and filter.', 'cubewp-framework'),
		  'required' => array(
			 array('google_address_radius', 'equals', '1')
		  )
	   )
	)
 );

$settings['map'] = array(
	'title'  => __('Map', 'cubewp-framework'),
	'id'     => 'map',
	'icon'   => 'dashicons-location-alt',
	'fields' => array(
	   array(
		  'id'      => 'google_map_api',
		  'title'   => __('Google Map & Places API Key', 'cubewp-framework'),
		  'desc'    => __('Get your Google Maps API Key here. <br> https://developers.google.com/maps/documentation/javascript/get-api-key', 'cubewp-framework'),
		  'type'    => 'text',
		  'default' => '',
	   ),
	   array(
 
		  'id'      => 'map_option',
		  'type'    => 'select',
		  'title'   => __('Map Type', 'cubewp-framework'),
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
		  'title'    => __('Mapbox Token', 'cubewp-framework'),
		  'subtitle' => __('Put here MapBox token, If you leave it empty then Google map will work', 'cubewp-framework'),
		  'desc'     => __('Get your Mapbox Key here.<br>https://account.mapbox.com/access-tokens/create', 'cubewp-framework'),
		  'default'  => '',
		  'required' => array(
			 array('map_option', 'equals', 'mapbox')
		  )
	   ),
	   array(
		  'id'       => 'map_style',
		  'type'     => 'text',
		  'title'    => esc_html__('Mapbox Map Style Id', 'cubewp-framework'),
		  'subtitle' => esc_html__('Type Your Custom Style ID', 'cubewp-framework'),
		  'desc'     => esc_html__('Type how you want the Mapbox map to show. Only use YOUR_USERNAME/YOUR_STYLE_ID No slashes before and after.', 'cubewp-framework'),
		  'default'  => 'mapbox/streets-v11',
		  'required' => array(
			 array('map_option', 'equals', 'mapbox')
		  )
	   ),
	   array(
		  'id'      => 'map_zoom',
		  'title'   => __('Set Map Default Zoom Level', 'cubewp-framework'),
		  'desc'    => __('Write Value Between 1 - 18 For Default Zoom Level', 'cubewp-framework'),
		  'type'    => 'text',
		  'default' => '15',
	   ),
	   array(
		  'id'      => 'map_latitude',
		  'title'   => __('Set Map Default Latitude', 'cubewp-framework'),
		  'desc'    => __('Write Valid Latitude For Default Map.', 'cubewp-framework'),
		  'type'    => 'text',
		  'default' => '40.68924104083928',
	   ),
	   array(
		  'id'      => 'map_longitude',
		  'title'   => __('Set Map Default Longitude', 'cubewp-framework'),
		  'desc'    => __('Write Valid Longitude For Default Map.', 'cubewp-framework'),
		  'type'    => 'text',
		  'default' => '-74.04450284527532',
	   ),
	)
);
$settings['single'] = array(
	'title'  => __('Single Post', 'cubewp-framework'),
	'id'     => 'single',
	'fields' => array(
        array(
		  'id'      => 'post_type_save_button',
		  'type'    => 'switch',
		  'title'   => __('Post Type Save Button', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('Gives you a button on single page to save post type.', 'cubewp-framework'),
	   ),
        array(
		  'id'      => 'post_type_share_button',
		  'type'    => 'switch',
		  'title'   => __('Post Type Share Button', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('Gives you a button on single page to share post type.', 'cubewp-framework'),
	   ),
        array(
		  'id'      => 'twitter_share',
		  'type'    => 'switch',
		  'title'   => __('Post Type Twitter Share', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('By enabling this option, you can share post type on twitter.', 'cubewp-framework'),
          'required' => array(
              array('post_type_share_button', 'equals', '1')
              )
	   ),
        array(
		  'id'      => 'facebook_share',
		  'type'    => 'switch',
		  'title'   => __('Post Type Facebook Share', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('By enabling this option, you can share post type on facebook.', 'cubewp-framework'),
          'required' => array(
              array('post_type_share_button', 'equals', '1')
              )
	   ),
        array(
		  'id'      => 'pinterest_share',
		  'type'    => 'switch',
		  'title'   => __('Post Type Pinterest Share', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('By enabling this option, you can share post type on pinterest.', 'cubewp-framework'),
          'required' => array(
              array('post_type_share_button', 'equals', '1')
              )
	   ),
        array(
		  'id'      => 'linkedin_share',
		  'type'    => 'switch',
		  'title'   => __('Post Type LinkedIn Share', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('By enabling this option, you can share post type on linkedIn.', 'cubewp-framework'),
          'required' => array(
              array('post_type_share_button', 'equals', '1')
              )
	   ),
        array(
		  'id'      => 'reddit_share',
		  'type'    => 'switch',
		  'title'   => __('Post Type Reddit Share', 'cubewp-framework'),
		  'default' => '1',
		  'desc'    => __('By enabling this option, you can share post type on reddit.', 'cubewp-framework'),
          'required' => array(
              array('post_type_share_button', 'equals', '1')
              )
	   ),
	)
 );

return $settings;