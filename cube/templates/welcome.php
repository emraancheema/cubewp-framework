<div class="cwp-welcome-tabber">
	<div>
		<nav class="wp-clearfix cwp-welcome-tabber-nav">
			<a class="<?php if( !isset($_GET['add-on']) ){ ?>nav-tab-active<?php } ?>" href="?page=cube_wp_dashboard"><?php echo esc_html__("Dashboard", "cubewp-framework"); ?></a>
			<?php /* <a class="<?php if( isset($_GET['add-on']) ){ ?>nav-tab-active<?php } ?>" href="?page=cube_wp_dashboard&add-on=true"><?php echo esc_html__("Add-on", "cubewp-framework"); ?></a>
			  ?><a class="" href="?page=user-custom-fields">Licenses</a> <?php */ ?>
		</nav>
	</div>
	<div class="cwp-important-links">
		<ul>
			<li><span class="dashicons dashicons-book"></span>
				<?php echo esc_html__("Docs", "cubewp-framework"); ?>
				<a href="https://support.cubewp.com/" target="_blank"></a>
			</li>
			<li><span class="dashicons dashicons-groups"></span>
				<?php echo esc_html__("Community", "cubewp-framework"); ?>
				<a href="https://support.cubewp.com/forums/forum/community/" target="_blank"></a>
			</li>
			<li><span class="dashicons dashicons-smiley"></span>
				<?php echo esc_html__("Feedback", "cubewp-framework"); ?>
				<a href="https://support.cubewp.com/forums/forum/feedback/" target="_blank"></a>
			</li>
			<li><span class="dashicons dashicons-sos"></span>
				<?php echo esc_html__("Helpdesk", "cubewp-framework"); ?>
				<a href="https://help.cubewp.com/help/2649704793" target="_blank"></a>
			</li>
		</ul>
	</div>
</div>
<div class="cwp-welcome-main-outer">
<?php if( !isset($_GET['add-on']) ){  ?>
	<div class="cwp-welcome-user-infor-outer">
		<div class="cwp-welcome-user-infor">
			<div class="cwp-welcome-user-infor-logo">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/welcomelogo.png'; ?>" alt="" />
			</div>
			<div>
				<h1><?php echo esc_html__("Hi!", "cubewp-framework"); ?> <?php echo wp_get_current_user()->display_name; ?></h1>
			</div>
		</div>
		<p><?php echo esc_html__("Welcome to the CubeWP admin dashboard. Here you can get a complete overview of all the core features and their status from this dashboard to help complete your website. Also learn regarding all the premium features to unlock.", "cubewp-framework"); ?></p>
	</div>
	<h1><?php echo esc_html__("Post Types Overview", "cubewp-framework"); ?></h1>
	<div class="cwp-post-type-overview">
		
		<div class="cwp-post-type-overview-left-side" style="width:100%">
<div class="cubewp-add-new-posttype"><a href="?page=cubewp-post-types&action=new" class="cwp-prev-ad-btn">+ Add New Post Type</a></div>
			<div class="cwp-post-type-overview-left-container">

					<div class="cwp-post-type-overview-left-side-inner cwp-post-type-overview-left-side-inner-header">
						<div>&nbsp;</div>
						<?php 
						$cwp_posttypes = CWP_types();
						foreach(  $cwp_posttypes as $slug => $cwp_post_args){
						?>
							<div><?php echo wp_kses_post($cwp_post_args['label']); ?></div>
						<?php 
						 }
						?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Taxonomies", "cubewp-framework"); ?></h6></div>
						<?php 
						foreach(  $cwp_posttypes as $slug => $cwp_post_args){
							$taxonomies = get_object_taxonomies( $slug , 'objects' );
							if( isset( $taxonomies ) && !empty($taxonomies)  ){
								?>
								<div  class="cwp-icon-helpTip">
									<span class="cwp-priew-count cwp-preview-toltip-outer"><?php echo count($taxonomies); ?></span>
									<span class="cwp-ctp-toolTips">
										<span class="cwp-ctp-toolTip">
										<p class="cwp-ctp-tipContent">
											<?php
											$count = 1;
											foreach(  $taxonomies as $taxslug => $tax_obj){
												echo wp_kses_post($tax_obj->label);
												if( $count < count($taxonomies) ){
													echo ', ';
												}
												$count++;
											}
											?>
										</p>
									</span>
									</span>
								</div>
								<?php
							}else{
								?>	
								<div>
									<a href="<?php echo CubeWp_Submenu::_page_action('taxonomies','new'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Custom Fields", "cubewp-framework"); ?></h6></div>
						<?php 
						foreach(  $cwp_posttypes as $slug => $cwp_post_args){
							$groups = cwp_get_groups_by_post_type(  $slug );
							if( isset( $groups ) && !empty($groups)  ){
								?>
								<div  class="cwp-icon-helpTip">
									<span class="cwp-priew-count cwp-preview-toltip-outer"><?php echo count($groups); ?></span>
									<span class="cwp-ctp-toolTips">
										<span class="cwp-ctp-toolTip">
										<p class="cwp-ctp-tipContent">
											<?php
											$count = 1;
											foreach(  $groups as $group ){
												echo get_the_title($group);
												if( $count < count($groups) ){
													echo ', ';
												}
												$count++;
											}
											?>
										</p>
									</span>
									</span>
								</div>
								<?php
							}else{
								?>	
								<div>
									<a href="<?php echo CubeWp_Submenu::_page_action('custom-fields','new'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Search Fields", "cubewp-framework"); ?></h6></div>
						<?php 
						
						$cwp_search_fields = CWP()->get_form('search_fields');
						foreach(  $cwp_posttypes as $slug => $cwp_post_args){
							if( isset( $cwp_search_fields[$slug] ) && !empty( $cwp_search_fields[$slug] ) ){
							?>
								<div  class="cwp-prev-tick-icon">
								   <span class="dashicons dashicons-saved"></span>
								</div>
						<?php 	
							}else{
							?>
								<div>
									<a href="<?php echo CubeWp_Submenu::_page_action('admin-search-fields'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
								</div>
						<?php 	
							}
						} ?>
					</div>
                <?php /*
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Single Post Type", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
								$cwp_search_fields = CWP()->get_form('single_layout');
								foreach(  $cwp_posttypes as $slug => $cwp_post_args){
									if( isset( $cwp_search_fields[$slug] ) && !empty( $cwp_search_fields[$slug] ) ){
									?>
										<div  class="cwp-prev-tick-icon">
										   <span class="dashicons dashicons-saved"></span>
										</div>
								<?php 	
									}else{
									?>
										<div>
											<a href="?page=cubewp-single-layout" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
										</div>
								<?php 	
									}
								}
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__(" See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Frontend Post Submission", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
							$cwp_post_types = CWP()->get_form('post_type');
							foreach(  $cwp_posttypes as $slug => $cwp_post_args){
								if( isset( $cwp_post_types[$slug]['groups'] ) && !empty( $cwp_post_types[$slug]['groups'] ) ){
								?>
									<div  class="cwp-prev-tick-icon">
									   <span class="dashicons dashicons-saved"></span>
									</div>
							<?php 	
								}else{
								?>
									<div>
										<a href="?page=cubewp-post-types-form" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
									</div>
							<?php 	
								}
							}
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Pricing Plans", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load')  && class_exists('woocommerce')  && is_plugin_active( 'cubewp-payments/cubewp-payments.php' )  ) {
								
								foreach(  $cwp_posttypes as $slug => $cwp_post_args){
									$plan_status = cwp_plan_exist_status_by_posttype($slug);
									if( isset( $plan_status ) && $plan_status ){
										
									?>
										<div  class="cwp-prev-tick-icon">
										   <span class="dashicons dashicons-saved"></span>
										</div>
								<?php 	
									}else{
									?>
										<div>
											<a href="<?php echo admin_url('post-new.php?post_type=price_plan'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
										</div>
								<?php 	
									}
								}
						}else{ ?>
							<div  class="cwp-unlock-feature">
								<?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
								<?php }else if( !is_plugin_active( 'cubewp-payments/cubewp-payments.php' ) ){ ?>
								<a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Cubewp Payments Required", "cubewp-framework"); ?></a>
								<?php ?>
								<?php }else if( !class_exists('woocommerce') ){ ?>
								<a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Woo Commerce Required", "cubewp-framework"); ?></a>
								<?php } ?>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__(" See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<?php /* ?><div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Reviews & Ratings", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
							
							if ( class_exists('CubeWp_Booster_Load') ) {
						?>
							
							<?php }else{ ?>
						
							 <div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("CubeWp Reviews Required", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
							
							<?php }
							
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__(" See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Ad Campaign", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
							
							if ( class_exists('CubeWp_Booster_Load') ) {
						?>
						
							<?php }else{ ?>
						
							 <div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("CubeWp Booster Required", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
							
							<?php }
							
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__(" See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Inbox", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
							
							if ( class_exists('CubeWp_Booster_Load') ) {
						?>
						
							<?php }else{ ?>
						
							 <div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("CubeWp Inbox Required", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
							
							<?php }
							
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span> <?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<div class="cwp-post-type-overview-left-side-inner <?php if ( !class_exists('CubeWp_Frontend_Load') ) { ?>cwp-post-type-overview-left-side-inner-pro<?php }else{ ?>cwp-post-type-overview-left-side-inner-pro-active<?php } ?>">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Calendar Booking", "cubewp-framework"); ?></h6></div>
						<?php 
						if ( class_exists('CubeWp_Frontend_Load') ) {
							
							if ( class_exists('CubeWp_Booster_Load') ) {
						?>
						
							<?php }else{ ?>
						
							 <div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("CubeWP Booking Required", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
							
							<?php }
							
						}else{ ?>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-lock"></span><?php echo esc_html__("Unlock this advance feature", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-unlock-feature">
							   <a href=""><span class="dashicons dashicons-controls-play"></span><?php echo esc_html__("See in action", "cubewp-framework"); ?></a>
							</div>
							<div  class="cwp-prev-tick-icon">
							</div>
							<div  class="cwp-icon-helpTip">
							</div>
						<?php } ?>
					</div>
					<?php */ ?>
					
					
				</div>
				<?php /* <h1><?php echo esc_html__("Pages Setup Overview", "cubewp-framework"); ?></h1>
				<div class="cwp-post-type-overview-left-container">
					<div class="cwp-post-type-overview-left-side-inner cwp-post-type-overview-left-side-inner-header">
						<div>&nbsp;</div>
						<div><?php echo esc_html__("Signup", "cubewp-framework"); ?></div>
						<div><?php echo esc_html__("Signin", "cubewp-framework"); ?></div>
						<div><?php echo esc_html__("User Profile", "cubewp-framework"); ?></div>
						<div><?php echo esc_html__("User Dashboard", "cubewp-framework"); ?></div>
						<div><?php echo esc_html__("Pricing Plans", "cubewp-framework"); ?></div>
						<div><?php echo esc_html__("Frontend Post Submission", "cubewp-framework"); ?></div>
					</div>
					<div class="cwp-post-type-overview-left-side-inner">
						<div><h6 class="cwp-review-option-name"><?php echo esc_html__("Status", "cubewp-framework"); ?></h6></div>
						<?php $Registerstatus = cwp_has_shortcode_pages_array('cwpRegisterForm'); 
						if( $Registerstatus ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
						<?php $loginstatus = cwp_has_shortcode_pages_array('cwpLoginForm'); 
						if( $loginstatus ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
						<?php $cwpProfileForm = cwp_has_shortcode_pages_array('cwpProfileForm'); 
						if( $cwpProfileForm ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
						<?php $cwp_dashboard = cwp_has_shortcode_pages_array('cwp_dashboard'); 
						if( $cwp_dashboard ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
						<?php $cwp_pricing_plans = cwp_has_shortcode_pages_array('cwp_pricing_plans'); 
						if( $cwp_pricing_plans ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
						<?php $cwpForm  = cwp_has_shortcode_pages_array('cwpForm'); 
						if( $cwpForm  ){
						?>
							<div  class="cwp-prev-tick-icon">
							   <span class="dashicons dashicons-saved"></span>
							</div>
						<?php }else{ ?>
							<div>
								<a href="?page=cubewp-post-types-form" class="cwp-prev-ad-btn"><span class="dashicons dashicons-plus"></span></a>
							</div>
						<?php } ?>
					</div>
				</div>
		</div>
		<div class="cwp-post-type-overview-right-side">
			<h1><?php echo esc_html__("Want to unlock advance features?", "cubewp-framework"); ?></h1>
			<a href="" class="cwp-view-extentions"><?php echo esc_html__("View all extensions", "cubewp-framework"); ?></a>
		</div>*/ ?>
	</div>
<?php }else{ ?>
	<div class="cwp-all-plugis-outer">
		<h1><?php echo esc_html__("CubeWP Add-ons", "cubewp-framework"); ?></h1>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
		<div class="cwp-plugins-grid-outer">
			<div class="cwp-plugins-thumbnail">
				<img src="<?php echo CWP_PLUGIN_URI.'cube/assets/admin/images/pl.png'; ?>"  alt="" />
			</div>
			<div class="cwp-plugin-name-box">
				<div><h4><?php echo esc_html__("Frontend Pro ", "cubewp-framework"); ?><span><?php echo esc_html__("1.0", "cubewp-framework"); ?></span></h4></div>
				<div><h5><?php echo esc_html__("Premium", "cubewp-framework"); ?></h5></div>
			</div>
			<p><?php echo esc_html__("Manage your contacts more effectively with HubSpot an all-in-one sales and marketing platform. With the HubSpot Add-On, you can automatically send form entries to your.", "cubewp-framework"); ?></p>
			<a href="" class="cwp-plugin-pur-btn"><?php echo esc_html__("Purchase this Extension", "cubewp-framework"); ?></a>
		</div>
	</div>
<?php } ?>
</div>