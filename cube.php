<?php
/**
* Plugin Name: CubeWP Framework
* Plugin URI: https://cubewp.com/
* Description: CubeWP is an end-to-end dynamic content framework for WordPress to help you save up to 90% of your coding time.
* Version: 1.1.1-beta
* Author: CubeWP
* Author URI: https://cubewp.com
* Text Domain: cubewp-framework
* Domain Path: /languages/
* @package Cubewp
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) )
exit;

if ( !defined( 'CUBEWP' ) ) {
    define( 'CUBEWP', 'CubeWp' );
}

/* CWP_PLUGIN_PATH Defines for load Php files */
if ( !defined( 'CWP_PLUGIN_PATH' ) ) {
    define( 'CWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define( 'CUBEWP_FILES', plugin_dir_path( __FILE__ ).'/cube/' );
    define( 'CUBEWP_CLASSES', plugin_dir_path( __FILE__ ).'/cube/classes/' );
}

/* CWP_PLUGIN_URI Defines for load JS and CSS files */
if ( !defined( 'CWP_PLUGIN_URI' ) ) {
    define( 'CWP_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}

/* CWP_PLUGIN_FILE Defines for file access */
if ( !defined( 'CWP_PLUGIN_FILE' ) ) {
    define( 'CWP_PLUGIN_FILE', __FILE__ );
}

spl_autoload_register( 'CWP_autoload_classes' );


/**
 * All CubeWP classes files to be loaded automatically.
 * @param  string $className a class name
 * @since  1.0.0
 */
function CWP_autoload_classes( $className ) {

    // If class does not start with our prefix ( CubeWp ), nothing will return.
    if ( false === strpos( $className, 'CubeWp' ) ) {
        return null;
    }
    $file_name = 'class-' .str_replace( '_', '-', strtolower( $className ) ).'.php';
    $file = CUBEWP_CLASSES.$file_name;

    // Checking if exists then include.
    if ( file_exists( $file ) ) {
        require $file;
    }
    return;
}

/**
* Class CubeWp_Load: Loads CubeWP plugin configurations.
*
* @since  1.0
*/

function CWP() {
    return CubeWp_Load::instance();
}
CWP();