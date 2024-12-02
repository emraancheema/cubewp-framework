<?php
/**
 * Builder Pro contains Subscription PopUp for cubeWP builder.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 * @since  1.0.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Builder_Pro {
    /**
     * CubeWp_Load Constructor.
     */
    public function __construct() {
        add_action("cubewp_post_types_form", array($this, "CubeWp_Empty_Form_Builder"));
        add_action("cubewp_user_registration_form", array($this, "CubeWp_Empty_Form_Builder"));
        add_action("cubewp_user_profile_form", array($this, "CubeWp_Empty_Form_Builder"));
        add_action("cubewp_single_layout", array($this, "CubeWp_Empty_Form_Builder"));
        add_action("cubewp_user_dashboard", array($this, "CubeWp_Empty_Form_Builder"));
        //add_action("cubewp_loop_builder", array($this, "CubeWp_Empty_Form_Builder"));
    }

	/**
	 * Method CubeWp_Form_Builder
	 *
	 * @return string html
	 * @since  1.0.0
	 */
    public function CubeWp_Empty_Form_Builder() {
        wp_enqueue_style('cwp-form-builder');
        if( current_cubewp_page() == 'cubewp_user_registration_form'){
            $page_header="User Signup Form Builder";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/form-builder-screen.png';
        }
        elseif( current_cubewp_page() == 'cubewp_user_profile_form'){
            $page_header="User Profile Form Builder";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/form-builder-screen.png';
        }
        elseif( current_cubewp_page() == 'cubewp_post_types_form'){
            $page_header="Post Types Form Builder";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/form-builder-screen.png';
        }
        elseif( current_cubewp_page() == 'cubewp_single_layout'){
            $page_header="Post Types Single Layout Builder";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/single-layout-screen.png';
        }
        elseif( current_cubewp_page() == 'cubewp_user_dashboard'){
            $page_header="User Dashboard Builder";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/user-dashboard-screen.png';
        }elseif( current_cubewp_page() == 'cubewp_loop_builder'){
            $page_header="Post Loop Generator";
            $background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/user-dashboard-screen.png';
        }
		echo'<div id="cubewp-title-bar">
		<h1>'.$page_header.'</h1>
		</div>
		<div class="cubewp-subscription-frame">
			<img class="cubewp-subscription-frame-bg" src="'.$background_image_src.'" alt="">
			<div class="cubewp-subscription-main">
				<div class="cubewp-subscription-form">
					<div class="cube-subscription-header" style="background-image: url('.CWP_PLUGIN_URI.'cube/assets/admin/images/addon-pop-header@2x.png)">
						<img class="subscription-header-super" src="'.CWP_PLUGIN_URI.'cube/assets/admin/images/wp-super.png" alt="image">
					</div>
					<div class="cubewp-subscription-contant">
						<div class="cubewp-subscription-logo">
							 <img src="'.CWP_PLUGIN_URI.'cube/assets/admin/images/cube-logo.svg" alt="">
						</div>
						<div class="cube-popup-title">
							<h2>Unlock (8) Super Powerful Frontend Builders</h2>
							<h3>Get All-in-One Forms & Layouts Builder Add-on</h3>
							<p>This single add-on gives you access to all the advanced builders you will ever need to create dynamic content.</p>
						</div>
						<div class="cube-subscription-active-options">
							<ul class="list-options-subscription-form">
								<li><span class="dashicons dashicons-yes"></span>Single Layout Builder</li>
								<li><span class="dashicons dashicons-yes"></span>Archive Layout Builder</li>
								<li><span class="dashicons dashicons-yes"></span>User Signup Form Builder</li>
								<li><span class="dashicons dashicons-yes"></span>User Profile Form Builder</li>
								<li><span class="dashicons dashicons-yes"></span>Advanced Search Fields Builder</li>
								<li><span class="dashicons dashicons-yes"></span>Post Types Form Builder</li>
								<li><span class="dashicons dashicons-yes"></span>Advanced Search Filter Builder</li>
								<li><span class="dashicons dashicons-yes"></span>User Dashboard Builder</li>
							</ul>
						</div>
						<div class="cubewp-subscription-bottom-contant">
							<div class="cubewp-subscription-download">
								<a href="https://demowp.io/get-demo.php?demo=CubeWP" target="_blank"><span class="dashicons dashicons-star-filled"></span>Try Frontend Pro Now</a>
								<span class="cube-award-option">Create Your Own Test Sandbox Powered by</span>
							</div>
							<div class="cubewp-logo">
								<img src="'.CWP_PLUGIN_URI.'cube/assets/admin/images/demowp.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
	
    }


    /**
     * Method init
     *
     * @return void
     */
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}