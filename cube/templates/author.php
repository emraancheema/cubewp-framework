<?php
/**
 * Method cubewp_author
 *
 *
 * @return string html
 * @since  1.0.6
 */
function cubewp_author(){
    get_header();
    global $cubewp_frontend,$cwpOptions;

    CubeWp_Enqueue::enqueue_style('loop-style');
    CubeWp_Enqueue::enqueue_style('single-cpt-styles');
    CubeWp_Enqueue::enqueue_style('author-style');
    CubeWp_Enqueue::enqueue_script('cwp-tabs');
    //CubeWp_Enqueue::enqueue_script('cwp-search-filters');

    $author_post_types = isset($cwpOptions['author_post_types']) ? $cwpOptions['author_post_types'] : '';


    $single = $cubewp_frontend->single();
    $author_id = get_the_author_meta('ID');

    $author_f_name = get_the_author_meta('first_name');
    $author_l_name = get_the_author_meta('last_name');
    $author_name = $author_f_name.' '.$author_l_name;
    if(empty($author_f_name)|| empty($author_l_name)){
      $author_name = get_the_author_meta('display_name');
    }
    $author_description=get_the_author_meta('user_description');
    ?>
    <div class="cwp-author-page">
    <?php
    $author_banner_image = isset($cwpOptions['author_banner_image']) ? wp_get_attachment_image_src($cwpOptions['author_banner_image']) : array();
    $author_banner_image_src = is_array($author_banner_image) && count($author_banner_image) > 0 ? 'style="background-image: url('.$author_banner_image[0].')"' : '';
    ?>
    <div class="cwp-auther-page-banner" <?php echo $author_banner_image_src; ?> >
    </div>
    <?php echo cwp_author_banner($author_id,$author_name); ?>
    <div class="cwp-auther-page-content">
        <div class="cwp-container">
            <div class="cwp-row">
                <div class="cwp-col-md-3">
                  <div class="cwp-auther-left-contetnt">
                  <?php
                    $author_contact_info = isset($cwpOptions['author_contact_info']) ? $cwpOptions['author_contact_info'] : '';
                    if($author_contact_info=='1'){ ?>
                    <div class="cwp-author-contact-detail">
                        <?php echo get_author_contact_info($author_id); ?>
                    </div>
                    <?php }
                    if(!empty($author_description)){ ?>
                      <div class="cwp-auther-about-info">
                          <div class="cwp-auther-sidebar-headings">
                              <h2><?php esc_html_e("About Me", "cubewp-framework"); ?></h2>
                          </div>
                          <div class="cwp-auther-sidebar-headings-content">
                              <p><?php echo $author_description ?></p>
                          </div>
                      </div>
                    <?php }
                    $author_custom_fields = isset($cwpOptions['author_custom_fields']) ? $cwpOptions['author_custom_fields'] : '';
                    if($author_custom_fields=='1'){
                      echo cwp_author_custom_fields($author_id);
                    }
                    ?>
                  </div>
                </div>
                <div class="cwp-col-md-9">
                  <div class="cwp-auther-posts">
                    <?php global $cubewp_frontend;
                    $post_types = cwp_post_types();
                    unset( $post_types['elementor_library'] );
                    unset( $post_types['e-landing-page'] );
                    unset( $post_types['attachment'] );
                    unset( $post_types['page'] );
                    unset( $post_types['cwp_reviews'] );
                    $args = get_author_posts_args($post_types,$author_id);
                    $page_num     =  isset($_GET['page_num']) ? $_GET['page_num'] : 1;
                    $is_archive_page =  isset($args['is_archive']) ? $args['is_archive'] : '';
                    $post_type    =  isset($args['post_type']) ? $args['post_type'] : '';
                    $query = new CubeWp_Query($args);
                    $posts = $query->cubewp_post_query();
                    $keyind = 0;
                    $active_class = $keyind == 0 ? 'cwp-active-tab' : '';
                    if($posts->have_posts()){
                    ?>
                    <div class="cwp-auther-post-tabs">
                        <ul class="cwp-tabs" role="tablist">
                          <li class="cwp-author-allposts-tab cwp-active-tab">
                            <a class="list-group-item" data-toggle="tab" href="#cwp-author-allposts"><?php esc_html_e("All My Posts", "cubewp-framework"); ?></a>
                          </li>
                          <?php if(!empty($author_post_types) && is_array($author_post_types)){
                            foreach($author_post_types as $post_type){
                              $args = array(
                                  'post_type' => $post_type,
                                  'author' => $author_id,
                                  'post_status' => 'publish',
                              );
                              $query = new CubeWp_Query($args);
                              $post = $query->cubewp_post_query();
                              if($post->have_posts()){
                            ?>
                            <li class="cwp-author-<?php esc_html_e($post_type)?>-tab <?php $active_class ?>">
                              <a class="list-group-item" data-toggle="tab" href="#cwp-author-<?php esc_html_e($post_type)?>"><?php esc_html_e($post_type)?></a>
                            </li>
                          <?php }
                            }
                          } ?>
                        </ul>
                    </div>
                    <div class="cwp-auther-post-content">
                      <?php $active_class = $keyind == 0 ? 'cwp-active-tab-content' : ''; ?>
                      <div class="cwp-tab-content cwp-active-tab-content" id="cwp-author-allposts">
                        <div class="cwp-row">
                            <?php
                              while ($posts->have_posts()) : $posts->the_post();
                              $post_id = get_the_id();
                              echo CubeWp_frontend_grid_HTML($post_id, $col_class = 'cwp-col-12 cwp-col-md-4');
                              endwhile;
                              $pagination_args = get_pagination_args($posts,$page_num,$is_archive_page);
                              echo apply_filters('cubewp_frontend_posts_pagination', '', $pagination_args);
                             ?>
                        </div>
                      </div>
                      <?php
                        if(!empty($author_post_types) && is_array($author_post_types)){
                        foreach ($author_post_types as $post_type) { ?>
                          <div class="cwp-tab-content <?php $active_class ?>" id="cwp-author-<?php esc_html_e($post_type)?>">
                            <div class="cwp-row">
                                <?php
                                  $args = get_author_posts_args($post_type,$author_id);
                                  $page_num     =  isset($_GET['page_num']) ? $_GET['page_num'] : 1;
                                  $is_archive_page =  isset($args['is_archive']) ? $args['is_archive'] : '';
                                  $post_type    =  isset($args['post_type']) ? $args['post_type'] : '';
                                  $query = new CubeWp_Query($args);
                                  $posts = $query->cubewp_post_query();
                                  while ($posts->have_posts()) : $posts->the_post();
                                  $post_id = get_the_id();
                                  echo CubeWp_frontend_grid_HTML($post_id, $col_class = 'cwp-col-12 cwp-col-md-4');
                                  endwhile;
                                  $pagination_args = get_pagination_args($posts,$page_num,$is_archive_page);
                                  echo apply_filters('cubewp_frontend_posts_pagination', '', $pagination_args);
                                 ?>
                            </div>
                          </div>
                      <?php } 
                    } ?>
                    </div>
                  <?php }else{?>
                        <div class="cwp-empty-search">
                          <img class="cwp-empty-search-img" src="<?php echo CWP_PLUGIN_URI?>cube/assets/frontend/images/no-result.png" alt="">
                          <h2><?php esc_html_e('No Posts Found','cubewp-framework')?></h2>
                          <p><?php esc_html_e('There are no posts associated with this author.','cubewp-framework') ?></p>
                        </div>
                  <?php } ?>
                  </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php
    get_footer();
}
/**
 * Method cwp_author_custom_fields
 *
 * @param int    $author_id
 * @param string $author_name
 *
 * @return string html
 * @since  1.0.6
 */
function cwp_author_banner($author_id,$author_name){
    global $cubewp_frontend,$cwpOptions;
    ?>
    <div class="cwp-auther-page-banner-info">
        <div class="cwp-container">
            <div class="cwp-row">
                <div class="cwp-col-md-3">
                    <div class="cwp-auther-frontend-image">
                        <img src="<?php echo get_avatar_url($author_id,array("size"=>360)); ?>" alt="<?php esc_html_e("Author", "cubewp-framework"); ?>" />
                    </div>
                    <?php
                    $edit_profile = isset($cwpOptions['author_edit_profile']) ? $cwpOptions['author_edit_profile'] : '';
                    $profile_page = isset($cwpOptions['profile_page']) ? $cwpOptions['profile_page'] : '';
                    $profile_page_url = get_permalink($profile_page);
                    if(!empty($profile_page) && is_user_logged_in() && $edit_profile=='1'){ ?>
                    <div class="cwp-auther-frontend-edit-option">
                        <a href="<?php echo esc_url($profile_page_url) ?>"> <span class="dashicons dashicons-edit-page"></span><?php esc_html_e("Edit Profile", "cubewp-framework"); ?></a>
                    </div>
                  <?php } ?>
                </div>
                <div class="cwp-col-md-4">
                    <div class="cwp-auther-name">
                        <h2><?php esc_html_e($author_name); ?></h2>
                    </div>
                    <div class="cwp-auther-joined-date">
                        <?php $author_registered=get_the_author_meta('user_registered');
                              $author_registered=date_create($author_registered);
                              $author_registered= date_format($author_registered,"M d, Y"); 
                        ?>
                        <p><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                          </svg>
                          <?php esc_html_e("joined", "cubewp-framework"); ?> <?php echo $author_registered ?>
                        </p>
                    </div>
                </div>
                <?php
                $author_share = isset($cwpOptions['author_share_button']) ? $cwpOptions['author_share_button'] : '';
                if($author_share=='1'){ ?>
                <div class="cwp-col-md-5">
                    <?php echo cwp_author_share($author_id,$author_name); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}
/**
 * Method cwp_author_custom_fields
 *
 * @param int    $author_id
 *
 * @return string html
 * @since  1.0.6
 */
function cwp_author_custom_fields($author_id) {
    global $cubewp_frontend,$cwpOptions;
    $author_custom_metas=CubeWp_Custom_Fields::cubewp_user_metas($author_id);
    $display_section = false;
    foreach($author_custom_metas as $author_custom_meta){
		if( isset($author_custom_meta['meta_value']) &&  !empty($author_custom_meta['meta_value']) ){
			$display_section = true;
			break;
		}
    }
    if(!empty($author_custom_metas) && is_array($author_custom_metas) && $display_section ){
		ob_start();
		?>
		<div class="cwp-author-custom-fields cwp-auther-about-info">
		<div class="cwp-auther-sidebar-headings">
				<h2><?php esc_html_e("Author Details", "cubewp-framework"); ?></h2>
		</div>
		<?php
		foreach($author_custom_metas as $author_custom_meta){
			$author_custom_meta['value']=$author_custom_meta['meta_value'];
			$author_custom_meta['field_size']='1-1';
			$author_custom_meta['container_class']='';
			$author_custom_meta['class']='';
			if (method_exists('CubeWp_Single_Page_Trait', 'field_' . $author_custom_meta['type'])) {
				echo call_user_func('CubeWp_Single_Page_Trait::field_' . $author_custom_meta['type'], $author_custom_meta);
			}
		}?>
		</div>
		<?php
    }
    return ob_get_clean();
}
/**
 * Method cwp_author_share
 *
 *@param int    $author_id
 *
 * @return string html
 * @since  1.0.6
 */
function cwp_author_share($author_id,$author_name) {
    global $cubewp_frontend,$cwpOptions;
    $site_title     = $author_name;
    $site_url       = get_author_posts_url($author_id);
    $site_title     = str_replace(' ', '%20', $site_title);
    $post_thumbnail = get_avatar_url($author_id);
    $twitterURL     = 'https://twitter.com/intent/tweet?text=' . esc_attr($site_title) . '&amp;url=' . esc_url($site_url) . '';
    $facebookURL    = 'https://www.facebook.com/sharer/sharer.php?u=' . esc_url($site_url);
    $pinterest      = 'https://pinterest.com/pin/create/button/?url=' . esc_url($site_url) . '&media=' . esc_attr($post_thumbnail) . '&description=' . esc_attr($site_title);
    $linkedin       = 'http://www.linkedin.com/shareArticle?mini=true&url=' . esc_url($site_url);
    $reddit         = 'https://www.reddit.com/login?dest=https%3A%2F%2Fwww.reddit.com%2Fsubmit%3Ftitle%3D' . esc_attr($site_title) . '%26url%3D' . esc_url($site_url);
    ob_start();
    ?>
    <div class="cwp-auther-share-links">
		<div class="cwp-auther-share">
			<span class="cwp-main">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
				<path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
				</svg>
				<span class="cwp-share-text"><?php esc_html_e("Share", "cubewp-framework"); ?></span>
			</span>
			<div class="cwp-share-modal" style="display:none;">
				<ul class="cwp-share-options">

						<li style="background-color: #4099FF;">
							<?php echo CubeWp_Single_Cpt::get_twitter_svg(esc_url($twitterURL)) ?>
						</li>

						<li style="background-color: #3b5998;">
							<?php echo CubeWp_Single_Cpt::get_facebook_svg(esc_url($facebookURL)) ?>
						</li>

						<li style="background-color: #C92228;">
							<?php echo CubeWp_Single_Cpt::get_pinterest_svg(esc_url($pinterest)) ?>
						</li>

						<li style="background-color: #0077B5;">
							<?php echo CubeWp_Single_Cpt::get_linkedIn_svg(esc_url($linkedin)) ?>
						</li>

						<li style="background-color: #fe6239;">
							<?php echo CubeWp_Single_Cpt::get_reddit_svg(esc_url($reddit)) ?>
						</li>
				</ul>
			</div>
		</div>
	</div>
	<?php
    return ob_get_clean();
}

/**
 * Method get_author_posts_args
 *
 * @param int    $author_id
 * @param string $post_type
 *
 * @return array
 * @since  1.0.6
 */
function get_author_posts_args($post_type,$author_id) {
    $args = array(
              'post_type' => $post_type,
              'author' => $author_id,
              'post_status' => 'publish',
              'is_archive' => 'false',
              'page_num' => isset($_GET['page_num']) ? $_GET['page_num'] : 1,
              'posts_per_page' => '10',
          );
    return $args;
}

/**
 * Method get_pagination_args
 *
 * @param int $page_num
 * @param string $posts
 * @param bool $is_archive_page
 *
 * @return array
 * @since  1.0.6
 */
function get_pagination_args($posts,$page_num,$is_archive_page) {
    $args=array(
              'total_posts'    => $posts->found_posts,
              'posts_per_page' => '10',
              'page_num'       => $page_num,
              'is_archive'     => $is_archive_page
          );
    return $args;
}
echo cubewp_author();