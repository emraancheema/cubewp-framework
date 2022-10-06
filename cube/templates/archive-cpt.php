<?php
get_header();
global $cubewp_frontend;
?>
<div class="cwp-container cwp-archive-container">
    <div class="cwp-row">
        <div class="cwp-col-md-2 cwp-archive-sidebar-filters-container">
			<?php $cubewp_frontend->filters(); ?>
        </div>
        <div class="cwp-col-md-7 cwp-archive-content-container">
            <div class="cwp-archive-content-listing">
                <div class="cwp-breadcrumb-results">
                    <div class="cwp-filtered-results">
						<?php $cubewp_frontend->results_data(); ?>
						<?php $cubewp_frontend->sorting_filter(); ?>
						<?php $cubewp_frontend->list_switcher(); ?>
                    </div>
                </div>
                <div class="cwp-search-result-output"></div>
            </div>
        </div>
        <div class="cwp-col-md-3 cwp-archive-content-map"></div>
    </div>
</div>
<?php
get_footer();