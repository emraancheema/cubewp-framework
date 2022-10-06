<?php
get_header();
global $cubewp_frontend,$cwpOptions;
$single = $cubewp_frontend->single();
wp_enqueue_style('single-cpt-styles');
do_action('cubewp_single_page_notification', get_the_ID());
?>
    <div class="cwp-cpt-single-container-outer">
        <div class="cwp-container">
             <?php echo wp_kses_post($single->get_post_featured_image()) ?>
            <div class="cwp-row cwp-cpt-single-content">
                <div class="cwp-col-12 cwp-col-lg-8">
                    <div class="cwp-single-title-container">
                        <h1 class="cwp-single-title"><?php echo get_the_title(get_the_ID()); ?></h1>
                        <div class="cwp-single-quick-actions">
                            <?php
                            if($cwpOptions['post_type_share_button']=='1'){
                                $single->get_post_share_button(get_the_ID());
                            }?>
                            <?php
                            if($cwpOptions['post_type_save_button']=='1'){
                                $single->get_post_save_button();
                            }?>
                        </div>
                    </div>
					<?php
                    $post_dsc = get_the_content(get_the_ID());
                    if(!empty($post_dsc)){
                    ?>
                    <div class="cwp-single-des"><?php echo wp_kses_post($post_dsc) ?></div>
                    <?php } ?>
                    <div class="cwp-single-groups">
						<?php $single->get_single_content_area(); ?>
                    </div>
                </div>
                <div class="cwp-col-12 cwp-col-lg-4">
					<?php $single->get_single_sidebar_area(); ?>
                </div>
            </div>
        </div>
    </div>
<?php
do_action('cubewp_post_confirmation', get_the_ID());
get_footer();