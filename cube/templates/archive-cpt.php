<?php
get_header();
global $cubewp_frontend, $cwpOptions;

$archive_map = isset($cwpOptions['archive_map']) ? $cwpOptions['archive_map'] : 1;
$archive_filters = isset($cwpOptions['archive_filters']) ? $cwpOptions['archive_filters'] : 1;
$archive_sort_filter = isset($cwpOptions['archive_sort_filter']) ? $cwpOptions['archive_sort_filter'] : 1;
$archive_layout = isset($cwpOptions['archive_layout']) ? $cwpOptions['archive_layout'] : 1;
$archive_found_text = isset($cwpOptions['archive_found_text']) ? $cwpOptions['archive_found_text'] : 1;

$filter_area_cols = 'cwp-col-md-2';
if ( ! $archive_filters) {
   $filter_area_cols = 'cwp-hide';
}
$content_area_cols = 'cwp-col-md-7';
if ( ! $archive_filters && $archive_map) {
   $content_area_cols = 'cwp-col-md-9';
}else if ( ! $archive_map && $archive_filters) {
   $content_area_cols = 'cwp-col-md-10';
}else if ( ! $archive_map && ! $archive_filters) {
   $content_area_cols = 'cwp-col-md-12';
}
?>
<div class="cwp-container cwp-archive-container">
    <div class="cwp-row">
        <div class="<?php esc_attr_e($filter_area_cols); ?> cwp-archive-sidebar-filters-container">
            <?php $cubewp_frontend->filters(); ?>
        </div>
        <div class="<?php esc_attr_e($content_area_cols); ?> cwp-archive-content-container">
            <div class="cwp-archive-content-listing">
                <div class="cwp-breadcrumb-results">
                    <?php if ($archive_sort_filter || $archive_layout || $archive_found_text) { ?>
                        <div class="cwp-filtered-results">
                            <?php if ($archive_found_text) {$cubewp_frontend->results_data(); } ?>
                            <?php if ($archive_sort_filter) {$cubewp_frontend->sorting_filter(); } ?>
                            <?php if ($archive_layout) {$cubewp_frontend->list_switcher(); } ?>
                        </div>
               <?php } ?>
                </div>
                <div class="cwp-search-result-output"></div>
            </div>
        </div>
        <?php if ($archive_map) { ?>
            <div class="cwp-col-md-3 cwp-archive-content-map"></div>
        <?php } ?>
    </div>
</div>
<?php
get_footer();