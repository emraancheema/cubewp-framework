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

class CubeWp_Forms_Pro {
    /**
     * CubeWp_Load Constructor.
     */
    public function __construct() {
        add_action("cubewp_forms_unsubscribed_templates", array($this, "CubeWp_Empty_Form_Builder"));
    }

	/**
	 * Method CubeWp_Form_Builder
	 *
	 * @return string html
	 * @since  1.0.0
	 */
    public function CubeWp_Empty_Form_Builder() {
        wp_enqueue_style('cwp-form-builder');
		$page_header="CubeWP Forms";
		$background_image_src = CWP_PLUGIN_URI.'cube/assets/admin/images/forms-templates.png';
		echo'<div id="cubewp-title-bar">
			<h1>'.$page_header.'</h1>
		</div>
		<div class="cubewp-subscription-frame forms-templates" style="background:#f0f0f1 0% 0% no-repeat padding-box;">
			<img class="cubewp-subscription-frame-bg" src="'.$background_image_src.'" alt="">
			<div class="cubewp-subscription-main">
				<div class="cubewp-subscription-form">
					<div class="cube-subscription-header forms-templates">
						<img class="subscription-header-super" src="'.CWP_PLUGIN_URI.'cube/assets/admin/images/forms.svg" alt="image">
					</div>
					<div class="cubewp-subscription-contant forms-templates">
						<div class="cubewp-subscription-logo">
							 <span>Unlock 10+ form templates with a dozen different styles for free!</span>
						</div>
						<div class="cube-popup-title">
							<h2>All-In-One Form Builder</h2>
							<h3>CONTACT FORM + LEADGEN FORM + NEWSLETTER SIGNUP FORM</h3>
							<p>Create various forms with ease using a drag-and-drop builder, then manage your leads seamlessly.</p>
						</div>
						<div class="cube-subscription-active-options">
							<ul class="list-options-subscription-form">
								<li><span class="dashicons dashicons-yes"></span>Unlimited Forms</li>
								<li><span class="dashicons dashicons-yes"></span>File Uploads</li>
								<li><span class="dashicons dashicons-yes"></span>Unlimited Submissions</li>
								<li><span class="dashicons dashicons-yes"></span>Set Character Limit</li>
								<li><span class="dashicons dashicons-yes"></span>Unlimited Leads</li>
								<li><span class="dashicons dashicons-yes"></span>Instant Notifications</li>
								<li><span class="dashicons dashicons-yes"></span>Leads Management</li>
								<li><span class="dashicons dashicons-yes"></span>Smart Conditional Logic</li>
								<li><span class="dashicons dashicons-yes"></span>Field Validation</li>
								<li><span class="dashicons dashicons-yes"></span>Spam Protection</li>
								<li><span class="dashicons dashicons-yes"></span>25 Custom Field Types</li>
								<li><span class="dashicons dashicons-yes"></span>MailChimp Integration</li>
							</ul>
						</div>
						<div class="cubewp-subscription-bottom-contant">
							<div class="cubewp-subscription-download">
								<a href="https://wordpress.org/plugins/cubewp-forms/" target="_blank">DOWNLOAD FOR FREE</a>
								<a href="https://cubewp.com/extensions/cubewp-forms-templates/" target="_blank"></span>PREVIEW TEMPLATES</a>
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