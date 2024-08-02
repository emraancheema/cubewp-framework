<?php
$thumbnail_url = cubewp_get_post_thumbnail_url($post_id);
$post_content = strip_tags(get_the_content('', '', $post_id));
if (str_word_count($post_content, 0) > 10) {
    $words = str_word_count($post_content, 2);
    $pos = array_keys($words);
    $post_content = substr($post_content, 0, $pos[10]) . '...';
}
ob_start();
?>
<div <?php post_class($col_class); ?>>
    <div class="cwp-post">
        <div class="cwp-post-thumbnail">
            <a href="<?php echo get_permalink($post_id); ?>">
                <img src="<?php echo esc_url($thumbnail_url); ?>"
                     alt="<?php echo get_the_post_thumbnail_caption($post_id); ?>">
            </a>
            <?php
            if (class_exists('CubeWp_Booster_Load')) {
                if (is_boosted($post_id)) {
                    ?>
                    <div class="cwp-post-boosted">
                        <?php echo esc_html_e('Ad', 'cubewp-framework'); ?>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="cwp-archive-save">
                <?php get_post_save_button($post_id); ?>
            </div>
        </div>
        <div class="cwp-post-content-container">
            <div class="cwp-post-content">
                <h4><a href="<?php echo get_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
                </h4>
                <p><?php echo esc_html($post_content); ?></p>
            </div>
            <?php
            $post_type = get_post_type($post_id);
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            $terms_ui = '';
            if (!empty($taxonomies) && is_array($taxonomies) && count($taxonomies) > 0) {
                $counter = 1;
                foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
                    $terms = get_the_terms($post_id, $taxonomy_slug);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            $terms_ui .= sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_term_link($term->slug, $taxonomy_slug)), esc_html($term->name));
                            if ($counter > 4) {
                                $terms_ui .= sprintf('<li><a href="%1$s">%2$s</a></li>', esc_url(get_the_permalink()), esc_html("View All", "cubewp-framework"));
                                break;
                            }
                            $counter++;
                        }
                    }
                }
            }
            if (!empty($terms_ui)) {
                ?>
                <ul class="cwp-post-terms"><?php
                    echo cubewp_core_data($terms_ui);
                    ?></ul><?php
            }
            ?>
        </div>
    </div>
</div>
<?php

return ob_get_clean();
?>
