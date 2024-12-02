<?php
/**
 * CubeWP Theme Updater
 *
 * @package Theme Update
 */
if ( !class_exists( 'EDD_Theme_Updater' ) ) {
	// Load our custom theme updater
	include( dirname( __FILE__ ) . '/theme-updater-class.php' );
}
function get_installed_themes_info() {
    $themes = wp_get_themes();
    $themes_info = array();

    foreach ( $themes as $theme_slug => $theme_data ) {
        $themes_info[] = array(
            'name'    => $theme_data->get( 'Name' ),
            'version' => $theme_data->get( 'Version' ),
            'slug' => $theme_slug
        );
    }

    return $themes_info;
}

$installed_themes = get_installed_themes_info();
$lic = get_option( 'theme_user_licenses');

if(!empty($installed_themes) && !empty($lic)){
	foreach ( $installed_themes as $theme ) {
		if(isset($lic[$theme['slug']])){
			$license_keys = array_column($lic[$theme['slug']], 'license_key');
			if(!empty($license_keys[0])){
				new EDD_Theme_Updater(
					array(
						'remote_api_url' 	=> 'https://cubewp.com',
						'version' 			=> $theme['version'],
						'license' 			=> trim( $license_keys[0] ),
						'item_name' 		=> $theme['name'],
						'theme_slug' 		=> $theme['slug'],
						'author'			=> 'Emraan Cheema'
					)
				);
			}
		}
	}
}

