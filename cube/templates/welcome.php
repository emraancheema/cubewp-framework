<?php
wp_enqueue_style('cubewp-welcome');
$current_user = wp_get_current_user();
if( $current_user->user_firstname ){
	$display_name = $current_user->user_firstname;
}else{
	$display_name = $current_user->user_login;
}
?>
<style>
.cwp-post-grid-contentarea .cwp-shordcodes .cwp-set-title-copyarea a {
    color: #000;
}
.cwp-post-grid-contentarea .cwp-shordcodes .cwp-set-title-copyarea a:hover {
    color: #6852eb;
}
</style>
<div class="cwp-welcome-title">
    <h2>Welcome <?php echo $display_name; ?>! Let’s Get Started.</h2>
</div>
<div class="cubwp-welcome">
    <div class="cwp-dashboard-content-panel" id="Dashboard">
        <div class="cwp-dashboard-content">
			<?php do_action( 'cwp-welcome-theme-header' ); ?>
            <div class="cwp-dashboard-data-structure first-data-structure">
                <div class="cwp-dashboard-data-structure-header">
                    <div class="cwp-dashboard-data-structure-svg">
                        <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/data-structure.png" alt="image" />
                    </div>
                    <div class="cwp-dashboard-data-structure-header-details">
                        <h3>Dynamic Data Structure</h3>
                        <p>Create custom post creation with diverse fields, advanced features, and interactive content for better UX.</p>
                    </div>
                </div>
                <div class="cwp-dashboard-data-structure-content">
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-post-types' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                            <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/Custom-Post-Types.png" alt="image" />
                        </div>
                        <p> Custom Post Types</p>
                        <span class="dashicons dashicons-plus"></span>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-taxonomies' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                            <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/Custom-Taxonomies.png" alt="image" />
                        </div>
                        <p>Custom Taxonomies</p>
                        <span class="dashicons dashicons-plus"></span>
                    </a>

                    <div class="cwp-dashboard-data-structure-inner-content">
                        <div class="cwp-dashboard-data-customs-links headings">
                            <div class="custom-cube-icons">
                                <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/Custom-Fields.png" alt="image" />
                            </div>
                            <p>Custom Fields<br><b>(25+ TYPES)</b></p>
                        </div>
                        <a href="<?php echo admin_url( 'admin.php?page=custom-fields' ); ?>" class="cwp-dashboard-data-customs-links">
                            <div class="custom-cube-icons inner">
                            <svg
                            viewBox="0 0 448 512"
                            version="1.1"
                            id="svg4"
                            sodipodi:docname="wpforms.svg"
                            inkscape:version="1.2.1 (9c6d41e, 2022-07-14)"
                            xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                            xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:svg="http://www.w3.org/2000/svg">
                            <defs
                                id="defs8" />
                            <sodipodi:namedview
                                id="namedview6"
                                pagecolor="#ffffff"
                                bordercolor="#000000"
                                borderopacity="0.25"
                                inkscape:showpageshadow="2"
                                inkscape:pageopacity="0.0"
                                inkscape:pagecheckerboard="0"
                                inkscape:deskcolor="#d1d1d1"
                                showgrid="false"
                                inkscape:zoom="0.37245305"
                                inkscape:cx="212.10727"
                                inkscape:cy="233.58649"
                                inkscape:window-width="1309"
                                inkscape:window-height="456"
                                inkscape:window-x="0"
                                inkscape:window-y="43"
                                inkscape:window-maximized="0"
                                inkscape:current-layer="svg4" />
                            <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="m 448,75.2 v 361.7 c 0,24.3 -19,43.2 -43.2,43.2 H 43.2 C 19.3,480 0,461.4 0,436.8 V 75.2 C 0,51.1 18.8,32 43.2,32 h 361.7 c 24,0 43.1,18.8 43.1,43.2 z M 410.7,436.8 V 75.2 c 0,-3 -3.3385,-3.755248 -5.8,-5.8 -168.20043,-0.657831 -168.25632,-0.713837 -361.7,-0.1 -3.2,0 -5.8,2.8 -5.8,5.8 v 361.7 c 0,3 2.6,5.8 5.8,5.8 h 361.7 c 3.2,0.1 5.8,-2.7 5.8,-5.8 z M 150.2,160 v 37 H 76.7 v -37 z m 0,74.4 v 37.3 H 76.7 V 234.4 Z M 96.8,69.4 c -64.533333,-46.266667 -32.266667,-23.133333 0,0 z M 371.3,160 v 37 h -196 v -37 z m 0,74.4 v 37.3 h -196 v -37.3 z m 0,74.6 v 37.3 H 271.9 V 309 Z"
                                id="path2"
                                sodipodi:nodetypes="ssscsssssssccssscsccccccccccccccccccccccccccc" />
                            </svg>
                            </div>
                            <p>For <b>Post Type</b></p>
                            <span class="dashicons dashicons-plus"></span>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=taxonomy-custom-fields' ); ?>" class="cwp-dashboard-data-customs-links">
                            <div class="custom-cube-icons inner">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M80 104a24 24 0 1 0 0-48 24 24 0 1 0 0 48zm80-24c0 32.8-19.7 61-48 73.3v87.8c18.8-10.9 40.7-17.1 64-17.1h96c35.3 0 64-28.7 64-64v-6.7C307.7 141 288 112.8 288 80c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V160c0 70.7-57.3 128-128 128H176c-35.3 0-64 28.7-64 64v6.7c28.3 12.3 48 40.5 48 73.3c0 44.2-35.8 80-80 80s-80-35.8-80-80c0-32.8 19.7-61 48-73.3V352 153.3C19.7 141 0 112.8 0 80C0 35.8 35.8 0 80 0s80 35.8 80 80zm232 0a24 24 0 1 0 -48 0 24 24 0 1 0 48 0zM80 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg>
                            </div>
                            <p>For <b>Taxonomies</b></p>
                            <span class="dashicons dashicons-plus"></span>
                        </a>
                        <a href="<?php echo admin_url( 'admin.php?page=user-custom-fields' ); ?>" class="cwp-dashboard-data-customs-links">
                            <div class="custom-cube-icons inner">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M144 160A80 80 0 1 0 144 0a80 80 0 1 0 0 160zm368 0A80 80 0 1 0 512 0a80 80 0 1 0 0 160zM0 298.7C0 310.4 9.6 320 21.3 320H234.7c.2 0 .4 0 .7 0c-26.6-23.5-43.3-57.8-43.3-96c0-7.6 .7-15 1.9-22.3c-13.6-6.3-28.7-9.7-44.6-9.7H106.7C47.8 192 0 239.8 0 298.7zM320 320c24 0 45.9-8.8 62.7-23.3c2.5-3.7 5.2-7.3 8-10.7c2.7-3.3 5.7-6.1 9-8.3C410 262.3 416 243.9 416 224c0-53-43-96-96-96s-96 43-96 96s43 96 96 96zm65.4 60.2c-10.3-5.9-18.1-16.2-20.8-28.2H261.3C187.7 352 128 411.7 128 485.3c0 14.7 11.9 26.7 26.7 26.7H455.2c-2.1-5.2-3.2-10.9-3.2-16.4v-3c-1.3-.7-2.7-1.5-4-2.3l-2.6 1.5c-16.8 9.7-40.5 8-54.7-9.7c-4.5-5.6-8.6-11.5-12.4-17.6l-.1-.2-.1-.2-2.4-4.1-.1-.2-.1-.2c-3.4-6.2-6.4-12.6-9-19.3c-8.2-21.2 2.2-42.6 19-52.3l2.7-1.5c0-.8 0-1.5 0-2.3s0-1.5 0-2.3l-2.7-1.5zM533.3 192H490.7c-15.9 0-31 3.5-44.6 9.7c1.3 7.2 1.9 14.7 1.9 22.3c0 17.4-3.5 33.9-9.7 49c2.5 .9 4.9 2 7.1 3.3l2.6 1.5c1.3-.8 2.6-1.6 4-2.3v-3c0-19.4 13.3-39.1 35.8-42.6c7.9-1.2 16-1.9 24.2-1.9s16.3 .6 24.2 1.9c22.5 3.5 35.8 23.2 35.8 42.6v3c1.3 .7 2.7 1.5 4 2.3l2.6-1.5c16.8-9.7 40.5-8 54.7 9.7c2.3 2.8 4.5 5.8 6.6 8.7c-2.1-57.1-49-102.7-106.6-102.7zm91.3 163.9c6.3-3.6 9.5-11.1 6.8-18c-2.1-5.5-4.6-10.8-7.4-15.9l-2.3-4c-3.1-5.1-6.5-9.9-10.2-14.5c-4.6-5.7-12.7-6.7-19-3L574.4 311c-8.9-7.6-19.1-13.6-30.4-17.6v-21c0-7.3-4.9-13.8-12.1-14.9c-6.5-1-13.1-1.5-19.9-1.5s-13.4 .5-19.9 1.5c-7.2 1.1-12.1 7.6-12.1 14.9v21c-11.2 4-21.5 10-30.4 17.6l-18.2-10.5c-6.3-3.6-14.4-2.6-19 3c-3.7 4.6-7.1 9.5-10.2 14.6l-2.3 3.9c-2.8 5.1-5.3 10.4-7.4 15.9c-2.6 6.8 .5 14.3 6.8 17.9l18.2 10.5c-1 5.7-1.6 11.6-1.6 17.6s.6 11.9 1.6 17.5l-18.2 10.5c-6.3 3.6-9.5 11.1-6.8 17.9c2.1 5.5 4.6 10.7 7.4 15.8l2.4 4.1c3 5.1 6.4 9.9 10.1 14.5c4.6 5.7 12.7 6.7 19 3L449.6 457c8.9 7.6 19.2 13.6 30.4 17.6v21c0 7.3 4.9 13.8 12.1 14.9c6.5 1 13.1 1.5 19.9 1.5s13.4-.5 19.9-1.5c7.2-1.1 12.1-7.6 12.1-14.9v-21c11.2-4 21.5-10 30.4-17.6l18.2 10.5c6.3 3.6 14.4 2.6 19-3c3.7-4.6 7.1-9.4 10.1-14.5l2.4-4.2c2.8-5.1 5.3-10.3 7.4-15.8c2.6-6.8-.5-14.3-6.8-17.9l-18.2-10.5c1-5.7 1.6-11.6 1.6-17.5s-.6-11.9-1.6-17.6l18.2-10.5zM472 384a40 40 0 1 1 80 0 40 40 0 1 1 -80 0z"/></svg>
                            </div>
                            <p>For <b>User Roles</b></p>
                            <span class="dashicons dashicons-plus"></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="cwp-dashboard-data-structure">
                <div class="cwp-dashboard-data-structure-header">
                    <div class="cwp-dashboard-data-structure-svg">
                        <div class="cwp-dashboard-data-structure-svg">
                            <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/form-builder.png" alt="image" />
                        </div>
                    </div>
                    <div class="cwp-dashboard-data-structure-header-details">
                        <h3>Frontend Form Builders</h3>
                        <p>Build custom user registration, profile forms, advanced search and filters, post submissions, and frontend layouts.</p>
                    </div>
                </div>
                <div class="cwp-dashboard-data-structure-content">
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-user-registration-form' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M512 80c8.8 0 16 7.2 16 16V416c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V96c0-8.8 7.2-16 16-16H512zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zM208 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128zm-32 32c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16H304c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80H176zM376 144c-13.3 0-24 10.7-24 24s10.7 24 24 24h80c13.3 0 24-10.7 24-24s-10.7-24-24-24H376zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24h80c13.3 0 24-10.7 24-24s-10.7-24-24-24H376z"/></svg>
                        </div>
                        <p>User Registration</p>
						<?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-user-profile-form' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 48V64c0 17.7-14.3 32-32 32H160c-17.7 0-32-14.3-32-32V48H64c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H320c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H256zM0 64C0 28.7 28.7 0 64 0H320c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM160 320h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/></svg>
                        </div>
                        <p>User Profile</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-admin-search-fields' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg>
                        </div>
                        <p>Search</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-admin-search-filters' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"/></svg>
                        </div>
                        <p>Filter</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-post-types-form' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                            <svg
                            viewBox="0 0 448 512"
                            version="1.1"
                            id="svg4"
                            sodipodi:docname="wpforms.svg"
                            inkscape:version="1.2.1 (9c6d41e, 2022-07-14)"
                            xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                            xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:svg="http://www.w3.org/2000/svg">
                            <defs
                                id="defs8" />
                            <sodipodi:namedview
                                id="namedview6"
                                pagecolor="#ffffff"
                                bordercolor="#000000"
                                borderopacity="0.25"
                                inkscape:showpageshadow="2"
                                inkscape:pageopacity="0.0"
                                inkscape:pagecheckerboard="0"
                                inkscape:deskcolor="#d1d1d1"
                                showgrid="false"
                                inkscape:zoom="0.37245305"
                                inkscape:cx="212.10727"
                                inkscape:cy="233.58649"
                                inkscape:window-width="1309"
                                inkscape:window-height="456"
                                inkscape:window-x="0"
                                inkscape:window-y="43"
                                inkscape:window-maximized="0"
                                inkscape:current-layer="svg4" />
                            <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="m 448,75.2 v 361.7 c 0,24.3 -19,43.2 -43.2,43.2 H 43.2 C 19.3,480 0,461.4 0,436.8 V 75.2 C 0,51.1 18.8,32 43.2,32 h 361.7 c 24,0 43.1,18.8 43.1,43.2 z M 410.7,436.8 V 75.2 c 0,-3 -3.3385,-3.755248 -5.8,-5.8 -168.20043,-0.657831 -168.25632,-0.713837 -361.7,-0.1 -3.2,0 -5.8,2.8 -5.8,5.8 v 361.7 c 0,3 2.6,5.8 5.8,5.8 h 361.7 c 3.2,0.1 5.8,-2.7 5.8,-5.8 z M 150.2,160 v 37 H 76.7 v -37 z m 0,74.4 v 37.3 H 76.7 V 234.4 Z M 96.8,69.4 c -64.533333,-46.266667 -32.266667,-23.133333 0,0 z M 371.3,160 v 37 h -196 v -37 z m 0,74.4 v 37.3 h -196 v -37.3 z m 0,74.6 v 37.3 H 271.9 V 309 Z"
                                id="path2"
                                sodipodi:nodetypes="ssscsssssssccssscsccccccccccccccccccccccccccc" />
                            </svg>

                        </div>
                        <p>Post Type Submission</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( class_exists('CubeWp_Forms_Custom') ){  echo 'href="'.admin_url( 'admin.php?page=cubewp-form-fields' ).'"'; }else { echo 'href="'.'https://wordpress.org/plugins/cubewp-forms/'.'" target="blank"'; } ?>  class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V428.7c-2.7 1.1-5.4 2-8.2 2.7l-60.1 15c-3 .7-6 1.2-9 1.4c-.9 .1-1.8 .2-2.7 .2H240c-6.1 0-11.6-3.4-14.3-8.8l-8.8-17.7c-1.7-3.4-5.1-5.5-8.8-5.5s-7.2 2.1-8.8 5.5l-8.8 17.7c-2.9 5.9-9.2 9.4-15.7 8.8s-12.1-5.1-13.9-11.3L144 381l-9.8 32.8c-6.1 20.3-24.8 34.2-46 34.2H80c-8.8 0-16-7.2-16-16s7.2-16 16-16h8.2c7.1 0 13.3-4.6 15.3-11.4l14.9-49.5c3.4-11.3 13.8-19.1 25.6-19.1s22.2 7.8 25.6 19.1l11.6 38.6c7.4-6.2 16.8-9.7 26.8-9.7c15.9 0 30.4 9 37.5 23.2l4.4 8.8h8.9c-3.1-8.8-3.7-18.4-1.4-27.8l15-60.1c2.8-11.3 8.6-21.5 16.8-29.7L384 203.6V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM549.8 139.7c-15.6-15.6-40.9-15.6-56.6 0l-29.4 29.4 71 71 29.4-29.4c15.6-15.6 15.6-40.9 0-56.6l-14.4-14.4zM311.9 321c-4.1 4.1-7 9.2-8.4 14.9l-15 60.1c-1.4 5.5 .2 11.2 4.2 15.2s9.7 5.6 15.2 4.2l60.1-15c5.6-1.4 10.8-4.3 14.9-8.4L512.1 262.7l-71-71L311.9 321z"/></svg>
                        </div>
                        <p>Contact Form</p>
                        <?php if( class_exists('CubeWp_Forms_Custom') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                </div>
            </div>
            <div class="cwp-dashboard-data-structure">
                <div class="cwp-dashboard-data-structure-header">
                    <div class="cwp-dashboard-data-structure-svg">
                        <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/layout-manager.png" alt="image" />
                    </div>
                    <div class="cwp-dashboard-data-structure-header-details">
                        <h3>Frontend Dynamic Layout Manager:</h3>
                        <p>Create custom user dashboards, page layouts, and single-post templates with a drag-and-drop interface.</p>
                    </div>
                </div>
                <div class="cwp-dashboard-data-structure-content">
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-single-layout' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                            <svg
                            viewBox="0 0 512 512"
                            version="1.1"
                            id="svg4"
                            sodipodi:docname="newspaper-regular.svg"
                            inkscape:version="1.2.1 (9c6d41e, 2022-07-14)"
                            xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                            xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:svg="http://www.w3.org/2000/svg">
                            <defs
                                id="defs8" />
                            <sodipodi:namedview
                                id="namedview6"
                                pagecolor="#ffffff"
                                bordercolor="#000000"
                                borderopacity="0.25"
                                inkscape:showpageshadow="2"
                                inkscape:pageopacity="0.0"
                                inkscape:pagecheckerboard="0"
                                inkscape:deskcolor="#d1d1d1"
                                showgrid="false"
                                inkscape:zoom="0.2576067"
                                inkscape:cx="143.6298"
                                inkscape:cy="143.6298"
                                inkscape:window-width="1309"
                                inkscape:window-height="456"
                                inkscape:window-x="0"
                                inkscape:window-y="43"
                                inkscape:window-maximized="0"
                                inkscape:current-layer="svg4" />
                            <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="m 168,80 c -13.3,0 -24,10.7 -24,24 v 304 c 0,8.4 -1.4,16.5 -4.1,24 H 440 c 13.3,0 24,-10.7 24,-24 V 104 C 464,90.7 453.3,80 440,80 Z M 96.068541,478.39543 C 95.039257,450.6024 95.534015,423.44006 96,408 V 104 c 0,-39.8 32.2,-72 72,-72 h 272 c 39.8,0 72,32.2 72,72 v 304 c 0,39.8 -32.20043,72.18568 -72,72 z M 176,136 c 0,-13.3 10.7,-24 24,-24 h 96 c 13.3,0 24,10.7 24,24 v 80 c 0,13.3 -10.7,24 -24,24 h -96 c -13.3,0 -24,-10.7 -24,-24 z m 200,-24 h 32 c 13.3,0 24,10.7 24,24 0,13.3 -10.7,24 -24,24 h -32 c -13.3,0 -24,-10.7 -24,-24 0,-13.3 10.7,-24 24,-24 z m 0,80 h 32 c 13.3,0 24,10.7 24,24 0,13.3 -10.7,24 -24,24 h -32 c -13.3,0 -24,-10.7 -24,-24 0,-13.3 10.7,-24 24,-24 z m -176,80 h 208 c 13.3,0 24,10.7 24,24 0,13.3 -10.7,24 -24,24 H 200 c -13.3,0 -24,-10.7 -24,-24 0,-13.3 10.7,-24 24,-24 z m 0,80 h 208 c 13.3,0 24,10.7 24,24 0,13.3 -10.7,24 -24,24 H 200 c -13.3,0 -24,-10.7 -24,-24 0,-13.3 10.7,-24 24,-24 z"
                                id="path2"
                                sodipodi:nodetypes="ssscsssssccsssssscsssssssssssssssssssssssssssssssssssss" />
                            </svg>
                        </div>
                        <p>Single-Post</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-user-dashboard' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm320 96c0-26.9-16.5-49.9-40-59.3V88c0-13.3-10.7-24-24-24s-24 10.7-24 24V292.7c-23.5 9.5-40 32.5-40 59.3c0 35.3 28.7 64 64 64s64-28.7 64-64zM144 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-16 80a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM400 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>
                        </div>
                        <p>User Dashboard</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=cubewp-loop-builder' ); ?>" class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 136c0-22.1-17.9-40-40-40L40 96C17.9 96 0 113.9 0 136l0 48c0 22.1 17.9 40 40 40H88c22.1 0 40-17.9 40-40l0-48zm0 192c0-22.1-17.9-40-40-40H40c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40H88c22.1 0 40-17.9 40-40V328zm32-192v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V136c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM288 328c0-22.1-17.9-40-40-40H200c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V328zm32-192v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V136c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM448 328c0-22.1-17.9-40-40-40H360c-22.1 0-40 17.9-40 40v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V328z"/></svg>
                        </div>
                        <p>Post Loop Generator</p>
                        <?php if( class_exists('CubeWp_Frontend_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                </div>
            </div>
            <div class="cwp-dashboard-data-structure">
                <div class="cwp-dashboard-data-structure-header">
                    <div class="cwp-dashboard-data-structure-svg">
                        <img src="<?php echo  CWP_PLUGIN_URI; ?>/cube/assets/admin/images/welcome-dashboard/robust-features.png" alt="image" />
                    </div>
                    <div class="cwp-dashboard-data-structure-header-details">
                        <h3>Engagement & Growth</h3>
                        <p>Platform includes P2P messaging, lead gen, reviews, WooCommerce payments, social integration, email verification, claims.</p>
                    </div>
                </div>
                <div class="cwp-dashboard-data-structure-content">
                    <a <?php if( class_exists('CubeWp_Inbox_Load') ){  echo 'href="https://support.cubewp.com/docs/cubewp-inbox/how-to-use/how-to-add-inbox-tab-in-user-dashboard/" target="blank"'; }else { echo 'href="'.'https://cubewp.com/cubewp-inbox'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M88.2 309.1c9.8-18.3 6.8-40.8-7.5-55.8C59.4 230.9 48 204 48 176c0-63.5 63.8-128 160-128s160 64.5 160 128s-63.8 128-160 128c-13.1 0-25.8-1.3-37.8-3.6c-10.4-2-21.2-.6-30.7 4.2c-4.1 2.1-8.3 4.1-12.6 6c-16 7.2-32.9 13.5-49.9 18c2.8-4.6 5.4-9.1 7.9-13.6c1.1-1.9 2.2-3.9 3.2-5.9zM0 176c0 41.8 17.2 80.1 45.9 110.3c-.9 1.7-1.9 3.5-2.8 5.1c-10.3 18.4-22.3 36.5-36.6 52.1c-6.6 7-8.3 17.2-4.6 25.9C5.8 378.3 14.4 384 24 384c43 0 86.5-13.3 122.7-29.7c4.8-2.2 9.6-4.5 14.2-6.8c15.1 3 30.9 4.5 47.1 4.5c114.9 0 208-78.8 208-176S322.9 0 208 0S0 78.8 0 176zM432 480c16.2 0 31.9-1.6 47.1-4.5c4.6 2.3 9.4 4.6 14.2 6.8C529.5 498.7 573 512 616 512c9.6 0 18.2-5.7 22-14.5c3.8-8.8 2-19-4.6-25.9c-14.2-15.6-26.2-33.7-36.6-52.1c-.9-1.7-1.9-3.4-2.8-5.1C622.8 384.1 640 345.8 640 304c0-94.4-87.9-171.5-198.2-175.8c4.1 15.2 6.2 31.2 6.2 47.8l0 .6c87.2 6.7 144 67.5 144 127.4c0 28-11.4 54.9-32.7 77.2c-14.3 15-17.3 37.6-7.5 55.8c1.1 2 2.2 4 3.2 5.9c2.5 4.5 5.2 9 7.9 13.6c-17-4.5-33.9-10.7-49.9-18c-4.3-1.9-8.5-3.9-12.6-6c-9.5-4.8-20.3-6.2-30.7-4.2c-12.1 2.4-24.7 3.6-37.8 3.6c-61.7 0-110-26.5-136.8-62.3c-16 5.4-32.8 9.4-50 11.8C279 439.8 350 480 432 480z"/></svg>
                        </div>
                        <p>Inbox</p>
                        <?php if( class_exists('CubeWp_Inbox_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( class_exists('CubeWp_Payments_Load') ){  echo 'href="'.admin_url( 'edit.php?post_type=price_plan' ).'"'; }else { echo 'href="'.'https://cubewp.com/cubewp-payments'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 0C46.3 0 32 14.3 32 32V96c0 17.7 14.3 32 32 32h80v32H87c-31.6 0-58.5 23.1-63.3 54.4L1.1 364.1C.4 368.8 0 373.6 0 378.4V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V378.4c0-4.8-.4-9.6-1.1-14.4L488.2 214.4C483.5 183.1 456.6 160 425 160H208V128h80c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H64zM96 48H256c8.8 0 16 7.2 16 16s-7.2 16-16 16H96c-8.8 0-16-7.2-16-16s7.2-16 16-16zM64 432c0-8.8 7.2-16 16-16H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm48-168a24 24 0 1 1 0-48 24 24 0 1 1 0 48zm120-24a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM160 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM328 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM256 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48zM424 240a24 24 0 1 1 -48 0 24 24 0 1 1 48 0zM352 344a24 24 0 1 1 0-48 24 24 0 1 1 0 48z"/></svg>
                        </div>
                        <p>Payments</p>
                        <?php if( class_exists('CubeWp_Payments_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( class_exists('CubeWp_Reviews_Load') ){  echo 'href="'.admin_url( 'edit.php?post_type=cwp_reviews' ).'"'; }else { echo 'href="'.'https://cubewp.com/downloads/cubewp-addon-reviews'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M320 376.4l.1-.1 26.4 14.1 85.2 45.5-16.5-97.6-4.8-28.7 20.7-20.5 70.1-69.3-96.1-14.2-29.3-4.3-12.9-26.6L320.1 86.9l-.1 .3V376.4zm175.1 98.3c2 12-3 24.2-12.9 31.3s-23 8-33.8 2.3L320.1 439.8 191.8 508.3C181 514 167.9 513.1 158 506s-14.9-19.3-12.9-31.3L169.8 329 65.6 225.9c-8.6-8.5-11.7-21.2-7.9-32.7s13.7-19.9 25.7-21.7L227 150.3 291.4 18c5.4-11 16.5-18 28.8-18s23.4 7 28.8 18l64.3 132.3 143.6 21.2c12 1.8 22 10.2 25.7 21.7s.7 24.2-7.9 32.7L470.5 329l24.6 145.7z"/></svg>
                        </div>
                        <p>Reviews & Rating</p>
                        <?php if( class_exists('CubeWp_Reviews_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
					<a <?php if( !class_exists('CubeWp_Booking_Load') ){  echo 'href="'.'https://cubewp.com/downloads/cubewp-addon-booking/'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 0c17.7 0 32 14.3 32 32V64H288V32c0-17.7 14.3-32 32-32s32 14.3 32 32V64h48c26.5 0 48 21.5 48 48v48H0V112C0 85.5 21.5 64 48 64H96V32c0-17.7 14.3-32 32-32zM0 192H448V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V192zM329 305c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-95 95-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L329 305z"/></svg>
						</div>
                        <p>Bookings</p>
                        <?php if( class_exists('CubeWp_Booking_Load') ){ ?>
							<span class="dashicons dashicons-saved"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( class_exists('CubeWp_Claim_Load') ){  echo 'href="'.admin_url( 'edit.php?post_type=cwp_claim' ).'"'; }else { echo 'href="'.'https://cubewp.com/downloads/cubewp-post-claim'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
                        </div>
                        <p>Claim Listing</p>
                        <?php if( class_exists('CubeWp_Claim_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( class_exists('CubeWp_Wallet_Load') ){  echo 'href="'.admin_url( 'edit.php?post_type=cubewp_wallet' ).'"'; }else { echo 'href="'.'https://cubewp.com/downloads/cubewp-addon-wallet'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H448c17.7 0 32-14.3 32-32s-14.3-32-32-32H64zM416 272a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg>
                        </div>
                        <p>Digital Wallet</p>
                        <?php if( class_exists('CubeWp_Wallet_Load') ){ ?>
							<span class="dashicons dashicons-plus"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
                    <a <?php if( !class_exists('CubeWp_Social_Logins_Load') ){  echo 'href="'.'https://cubewp.com/downloads/cubewp-addon-social-logins'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"/></svg>
                        </div>
                        <p>Social Login</p>
                        <?php if( class_exists('CubeWp_Social_Logins_Load') ){ ?>
							<span class="dashicons dashicons-saved"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
					
					<a <?php if( !class_exists('CubeWp_Bulk_Import_Load') ){  echo 'href="'.'https://cubewp.com/downloads/cubewp-addon-bulk-import/'.'" target="blank"'; } ?> class="cwp-dashboard-data-customs-links">
                        <div class="custom-cube-icons">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M128 64c0-35.3 28.7-64 64-64H352V128c0 17.7 14.3 32 32 32H512V448c0 35.3-28.7 64-64 64H192c-35.3 0-64-28.7-64-64V336H302.1l-39 39c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l80-80c9.4-9.4 9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l39 39H128V64zm0 224v48H24c-13.3 0-24-10.7-24-24s10.7-24 24-24H128zM512 128H384V0L512 128z"/></svg>
					    </div>
                        <p>Bulk Import</p>
                        <?php if( class_exists('CubeWp_Bulk_Import_Load') ){ ?>
							<span class="dashicons dashicons-saved"></span>
						<?php }else{ ?>
							<span class="dashicons dashicons-lock"></span>
						<?php } ?>
                    </a>
					
                </div>
            </div>
        </div>
        <div class="cwp-dashboard-content-sidebar">
            <div class="cwp-dashboard-sidebar">
                <div class="cwp-welcome-box">
                    <div class="cwp-welcome-box-content">
                        <h2>CubeWP Framework</h2>
                        <p>CubeWP is an end-to-end dynamic content framework for WordPress to help you shrink time and cut cost of development up to 90%.</p>
                        <div class="cwp-learmore-addons">
                            <a target="_blank" href="https://cubewp.com/features/">Learn More</a>
                        </div>
                    </div>
                    <div class="cwp-welcome-box-logo">
                        <img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/cube-addons.png'; ?>" alt="" />
                    </div>
                </div>
                <div class="cwp-welcome-box cwp-leads-template-addons">
                    <div class="cwp-leads-template-addons-titles">
                        <h3>Download Free Extensions</h3>
                        <a href="https://cubewp.com/extensions/" target="_blank">See All</a>
                    </div>
                    <div class="cwp-leads-template-addons-cotent">
                        <a href="https://cubewp.com/downloads/cubewp-addon-social-logins/" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Social-Login.png'; ?>" alt="image" />Social Login</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-wallet" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Digital-Wallet.png'; ?>" alt="image" />Digital Wallet</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-post-claim" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Post-Claim.png'; ?>" alt="image" />Post Claim</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-bulk-import/" class="cwp-lead-content-imges four" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Bulk-Import.png'; ?>" alt="image" />Bulk Import</a>
                    </div>
                </div>
                <div class="cwp-welcome-box cwp-leads-template-addons">
                    <div class="cwp-leads-template-addons-titles">
                        <h3>Premium Extensions <span>Included with All Premium Plans - Starting $19</span></h3>
                        <a href="https://cubewp.com/extensions/" target="_blank">See All</a>
                    </div>
                    <div class="cwp-leads-template-addons-cotent">
                        <a href="https://cubewp.com/downloads/cubewp-addon-frontend-pro/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Frontend.png'; ?>" alt="image" />Frontend Pro</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-payments/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Payments.png'; ?>" alt="image" />Payments</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-inbox/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Inbox.png'; ?>" alt="image" />Inbox</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-reviews/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Reviews.png'; ?>" alt="image" />Reviews</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-booster/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Booster.png'; ?>" alt="image" />Booster</a>
                        <a href="https://cubewp.com/downloads/cubewp-addon-booking/" class="cwp-lead-content-imges three" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/Booking.png'; ?>" alt="image" />Booking</a>
                    </div>
                </div>
                <div class="cwp-welcome-box cwp-leads-template-addons">
                    <div class="cwp-leads-template-addons-titles">
                        <h3>Premium Themes <span>Included with All Premium Plans - Starting $19</span></h3>
                        <a href="https://cubewp.com/themes/" target="_blank">See All</a>
                    </div>
                    <div class="cwp-leads-template-addons-cotent">
                        <a href="https://cubewp.com/downloads/dubified/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/dubi.png'; ?>" alt="image" />Classified Ads Theme</a>
                        <a href="https://cubewp.com/downloads/streetwise/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/street.png'; ?>" alt="image" />Real-Estate Theme</a>
                        <a href="https://cubewp.com/downloads/yellowbooks/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/yellow.png'; ?>" alt="image" />Directory Theme</a>
                        <a href="https://themeforest.net/item/classifiedpro-recommerce-classified-wordpress-theme/44528010" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/themes-extensions/classi.png'; ?>" alt="image" />Classified Ads Theme</a>
						<a href="https://cubewp.com/downloads/staybnb/" class="cwp-lead-content-imges two" target="_blank"><img src="<?php echo CWP_PLUGIN_URI . 'cube/assets/admin/images/welcome-dashboard/template-library/StayBnb_Preview.png'; ?>" alt="image" style=" background-color: #FFFAFA;border-radius: 5px;" />Staybnb Theme</a>
					</div>
                </div>
                <div class="cwp-welcome-row">
                    <div class="cwp-welcome-col-md-12 margin-bottom-10 ">
                        <div class="cwp-welcome-faqs">
                            <div class="cwp-faqs-top cwp-welcome-row">
                                <div class="cwp-welcome-header-info">
                                    <span class="dashicons dashicons-shortcode"></span>
                                    <h3 class="cwp-welcome-section-heading">All Shortcodes Cheatsheet</h3>
                                </div>
                            </div>
                            <div class="cwp-post-grid-contentarea">
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Search Form</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpSearch type="YOUR POST TYPE"]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Search Filter</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpFilters]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Saved Posts Page</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpSaved]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>User Signup Page <?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
											<a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
											<?php } ?>
										</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpRegisterForm role=YOUR USER ROLE”]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Post Type Form Page <?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
											<a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
											<?php } ?>
										</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwpForm type="YOUR POST TYPE"]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>User Dashboard Page <?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
												<a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
											<?php } ?>
										</h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>copy shortcode</p>[cwp_dashboard]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes border-bottom-welcome">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Pricing Plan Page <?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
											<a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
											<?php } ?></h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>Copy Shortcode</p>[cwpPricingPlans]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cwp-shordcodes">
                                    <div class="cwp-set-title-copyarea">
                                        <h3>Reset Password Form <?php if( !class_exists('CubeWp_Frontend_Load') ){ ?>
											<a href="https://cubewp.com/pricing/" target="_blank"><span class="dashicons dashicons-lock"></span></a>
											<?php } ?></h3>
                                        <div class="shoftcode-area">
                                            <div class="cwpform-shortcode">
                                                <div class="inner copy-to-clipboard"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"></path>
                                                    </svg>
                                                    <p>Copy Shortcode</p>[cwpResetPasswordForm]
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cwp-welcome-col-md-12 margin-bottom-10">
                        <div class="cwp-welcome-faqs">
                            <div class="cwp-faqs-top cwp-welcome-row">
                                <div class="cwp-welcome-header-info">
                                    <span class="dashicons dashicons-sos"></span>
                                    <h3 class="cwp-welcome-section-heading">Top Helpful Resources</h3>
                                    <a href="https://support.cubewp.com/" class="cwp-welcome-section-all-faqs" target="_blank">All Documentation</a>
                                </div>
                            </div>
                            <div class="cwp-post-grid-contentarea">
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-post-types/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Custom Post Types</p>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-taxonomies/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Custom Taxonomies</p>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/custom-fields/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>How to Create Custom Fields</p>
                                </a>
                                <a href="https://support.cubewp.com/docs/cubewp-framework/developer-guides/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Developer's Guide (CubeWP Filters & Actions)</p>
                                </a>
                                <a href="https://support.cubewp.com/forums/" target="_blank" class="cwp-cutom-post-info-row border-bottom-welcome">
                                    <p>Community Forum</p>
                                </a>
                                <a href="https://help.cubewp.com/" target="_blank" class="cwp-cutom-post-info-row padding-bottom-18">
                                    <p>Helpdesk</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>