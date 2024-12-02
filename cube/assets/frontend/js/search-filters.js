jQuery(document).ready(function () {
    var typingTimer;
    var doneTypingInterval = 200;

    jQuery(document).on('click', '.quick-see-more', function(){
        if(jQuery(this).hasClass('show')){
            jQuery(this).removeClass('show');
            jQuery(this).text('See More');
        }else{
            jQuery(this).addClass('show');
            jQuery(this).text('Less More');
        }
        jQuery(this).closest('.cwp-search-field-checkbox').find('.hidden-field').toggle(500);
    });
    
    jQuery(document).on("click", '.listing-switcher', function() {
        var thisObj = jQuery(this);
        if(thisObj.hasClass('list-view')){
            jQuery('.cwp-grids-container').removeClass('grid-view');
            jQuery('.cwp-grids-container').addClass('list-view');
            jQuery(this).addClass('cwp-active-style');
            jQuery('.listing-switcher.grid-view').removeClass('cwp-active-style');
        }else{
            jQuery('.cwp-grids-container').removeClass('list-view');
            jQuery('.cwp-grids-container').addClass('grid-view');
            jQuery(this).addClass('cwp-active-style');
            jQuery('.listing-switcher.list-view').removeClass('cwp-active-style');
        }
    });
    
    jQuery(document).on("change", '.cwp-search-field-checkbox input[type="checkbox"]', function() {
        
        var hidden_checkbox = jQuery(this).closest('.cwp-search-field-checkbox').find('input[type="hidden"]');
        var hidden_vals = hidden_checkbox.val();
        if(jQuery(this).is(':checked')){
            if( hidden_vals == '' ){
                hidden_vals = jQuery(this).val();
            }else{
                hidden_vals += ','+ jQuery(this).val();
            }
            jQuery(this).prop('checked', true);
        }else{
            jQuery(this).prop('checked', false);
            hidden_vals = cwp_remove_string_value(hidden_vals, jQuery(this).val() );
        }
        hidden_checkbox.val(hidden_vals);
        cwp_search_filters_ajax_content();
    });
    
    jQuery(document).on("change", '.cwp-search-filters select', function() {
        if(jQuery(this).hasClass('multi-select')){
            var value = jQuery(this).val();
            if( value != '' ){
                value.join(',');
            }
            jQuery(this).closest('.cwp-search-field-dropdown').find('input[type="hidden"]').val(value);
            cwp_search_filters_ajax_content();
        }else{
            cwp_search_filters_ajax_content();
        }
        
    });

    if(jQuery('.cubewp-date-range-picker').length > 0 ) {
        jQuery('.cubewp-date-range-picker').each(function () {
            var $this = jQuery(this),
                from = $this.find(".cubewp-date-range-picker-from")
                    .datepicker({
                        dateFormat: "mm/dd/yy",
                        defaultDate: "+1w", changeMonth: true, numberOfMonths: 1
                    })
                    .on("change", function () {
                        to.datepicker("option", "minDate", getDate(this));
                        $this.find('.cubewp-date-range-picker-input').val(getDateRange(from, to)).trigger('input');
                    }),
                to = $this.find(".cubewp-date-range-picker-to").datepicker({
                    dateFormat: "mm/dd/yy",
                    defaultDate: "+1w", changeMonth: true, numberOfMonths: 1
                })
                    .on("change", function () {
                        from.datepicker("option", "maxDate", getDate(this));
                        $this.find('.cubewp-date-range-picker-input').val(getDateRange(from, to)).trigger('input');
                    });
        });
    }

    if (jQuery(".cubewp-date-range-picker-input").length > 0) {
        jQuery(document).on("input", ".cubewp-date-range-picker-input", function (){
            cwp_search_filters_ajax_content();
        });
    }
    
    jQuery(document).on("change", '.cwp-search-filters input[type="radio"]', function() {
        jQuery(this).closest('.cwp-field-radio-container').find('input[type="radio"]').prop('checked', false);
        jQuery(this).prop('checked', true);
        var hidden_radio = jQuery(this).closest('.cwp-radio-container').find('input[type="hidden"]');
        var hidden_vals = jQuery(this).val();
        hidden_radio.val(hidden_vals);
        cwp_search_filters_ajax_content();
    });
    
    // jQuery(document).on("change input", '.cwp-search-field .cwp-date-hidden-field', function() {
    //     cwp_search_filters_ajax_content();
    // });
    
    // jQuery(document).on("change", '.cwp-search-field .cwp-date-hidden-field', function() {
    //     cwp_search_filters_ajax_content();
    // });
    
    jQuery(document).on("cwp-address-change", '.cwp-search-field-google_address .address', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
    });
    jQuery(document).on('keyup', '.cwp-search-filters input[type="text"]', function (e) {
        if(!jQuery(this).closest('.cwp-search-field').hasClass('cwp-search-field-google_address')){
            clearTimeout(typingTimer);
            typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
        }
    });
    jQuery(document).on('keyup', '.cwp-search-filters input[type="number"]', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
    });
    
    jQuery(document).on("change", '.cwp-search-filters .cwp-address-range .range', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
    });
    
    jQuery(document).on('change', '.cwp-field-switch-container input[type="checkbox"]', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
    });

    jQuery(document).on('change', '.cwp-field-range input[type="range"]', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(cwp_search_filters_ajax_content, doneTypingInterval);
    });

    jQuery(document).on('click', '.cwp-search-filters .clear-filters', function (e) {
        
        var PostType = jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[name="post_type"]').val();
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="text"]').val('');
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="number"]').val('');
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="cwp-date-range"]').val('');
        if(jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="hidden"]').hasClass('is_tax')){
            var currentVal = jQuery(this).closest('.cwp-search-filters').find('.is_tax').attr('data-current-tax');
            jQuery(this).closest('.cwp-search-filters').find('.is_tax').val(currentVal);
        }else{
            jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="hidden"]').val('');
        }
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="radio"]').removeAttr("checked");
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="checkbox"]').prop('checked', false);
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields select').val('');
        jQuery(this).closest('.cwp-search-filters').find('input[name="page_num"]').val('1');
        jQuery('select[name="cwp_orderby"]').val('');
        jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="google_address"]').val('');

        if (jQuery(this).closest('.cwp-search-filters').find('.cwp-address-range').length > 0) {
            jQuery(this).closest('.cwp-search-filters').find('.cwp-address-range').addClass("cwp-hide");
            jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[type="range"]').attr('type', 'hidden').removeAttr("value min max");
            jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields .cwp-search-field-google_address input[type="range"]').attr('type', 'hidden').removeAttr("value min max");
        }
        
        var PostType = jQuery(this).closest('.cwp-search-filters').find('.cwp-search-filters-fields input[name="post_type"]').val(PostType);
        if (jQuery(this).closest('.cwp-search-filters').find(".cwp-select2 select").length > 0) {
            jQuery(this).closest('.cwp-search-filters').find(".cwp-select2 select").val(null).trigger("change");
        }else{
            cwp_search_filters_ajax_content('');
        }
        
    });
    
    cwp_search_filters_ajax_content();
});

function cubewp_posts_pagination_ajax( page_num ){
    jQuery('#cwp-page-num').val(page_num);
    cwp_search_filters_ajax_content(page_num,'');
}

jQuery(document).on("change", '#cwp-order-filter', function() {
    cwp_search_filters_ajax_content();
});

jQuery(document).on("change", '#cwp-sorting-filter', function() {
    var val = jQuery(this).val();
    if (val == 'rand') {
        jQuery('select[name="cwp_order"]').prop('disabled', true);
    } else {
        jQuery('select[name="cwp_order"]').prop('disabled', false);
    }
    cwp_search_filters_ajax_content();
});

function cwp_search_filters_ajax_content( page_num=''){

    // Loading SKeleton 
    if (jQuery('.cwp-archive-container .cwp-grids-container').length > 0) {
        jQuery('.cwp-archive-container .cwp-grids-container div').html(
            '<div class="cwp-processing-post-grid">' +
            '<div class="cwp-processing-post-thumbnail"></div>' +
            '<div class="cwp-processing-post-content"><p></p><p></p><p></p></div>' +
            '</div>'
        );
    } else {
        let processingGrid = '';
        for (let i = 0; i < 6; i++) {
            processingGrid += 
                '<div class="cwp-col-md-4">' +
                '<div class="cwp-processing-post-grid">' +
                '<div class="cwp-processing-post-thumbnail"></div>' +
                '<div class="cwp-processing-post-content"><p></p><p></p><p></p></div>' +
                '</div></div>';
        }
        jQuery('.cwp-archive-container .cwp-search-result-output').html(
            '<div class="cwp-grids-container cwp-row">' + processingGrid + '</div>'
        );
    }

    // Getting filter form
    var FilterForm = jQuery('.cwp-search-filters'),
        state = jQuery('.cwp-search-filters-fields').find('input[name="page"]').val(),
        is_tax = FilterForm.find('.is_tax').val();

    page_num = page_num || 1;    

    var action = '&action=cwp_search_filters_ajax_content';

    FilterForm.find('input[name="page_num"]').val( page_num );

    var FilterFields = FilterForm.serialize();
    
    if(jQuery('#cwp-order-filter').length > 0){
        FilterFields += '&order='+jQuery('#cwp-order-filter').val();
    }
    if(jQuery('#cwp-sorting-filter').length > 0){
        FilterFields += '&orderby='+jQuery('#cwp-sorting-filter').val();
    }
    var data_vals = FilterFields;
    
    data_vals = urlCombine(data_vals,window.location.search);
    data_vals = stripUrlParams(data_vals);
    data_vals = data_vals.replace(/(?!s=)[^&]+=\.?(?:&|$)/g, function(match) {
        return match.endsWith('&') ? '' : '&';
    }).replace(/&$/, ''); // remove all empty variables except s= and handle trailing '&'
    
    data_vals = data_vals.replace('undefined', ''); // remove extra and empty variables
    
    if(state !== 'page' && (is_tax == '' || is_tax == undefined)){
        var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals;
        window.history.pushState(null, null, decodeURIComponent(current_url));
    }

    // Remove _ST_ from parameter names from query Strings like if there is a taxonomy property_type it will come in query string _ST_property_type
    data_vals = stripPrefixFromParams(data_vals, '_ST_');
    
    jQuery.ajax({
        url: cwp_search_filters_params.ajax_url,
        type: 'POST',
        data: data_vals+action,
        dataType: "json",
        success: function (response) {
            if( jQuery(".cwp-archive-container").length > 0 ){
                jQuery('html, body').animate({
                    scrollTop: jQuery(".cwp-archive-container").offset().top - 100
                }, 200);
            }
            jQuery('.cwp-search-result-output').html(response.grid_view_html);
            jQuery('.cwp-total-results').html(response.post_data_details);

            // Listing update on Map
            if (typeof CWP_Cluster_Map === 'function') {
                CWP_Cluster_Map(response.map_cordinates);
            }
            
            jQuery('.cwp-archive-container').removeClass('cwp-active-ajax');
            jQuery( document.body ).trigger( 'cubewp_search_results_loaded' );
        }
    });
}


function urlCombine(a,b,overwrite=false){
    a = new URLSearchParams(a);
    let one=[];
    let i=0;
    const fn = overwrite ? a.set : a.append;
    for(let [key1, value1] of a){
        one[i] = key1;
        i++;
    }
    for(let [key2, value2] of new URLSearchParams(b)){
        if(jQuery.inArray( key2, one ) == -1){
            fn.call(a, key2, value2);
        }
    }
    return a.toString();
}

function stripUrlParams(args) {
    
    "use strict";
    var parts = args.split("&");
    
    var comps = {};
    for (var i = parts.length - 1; i >= 0; i--) {
        var spl = parts[i].split("=");
        // Overwrite only if existing is empty.
        if (typeof comps[ spl[0] ] == "undefined" || (typeof comps[ spl[0] ] != "undefined" && comps[ spl[0] ] == '')) {
            comps[ spl[0] ] = spl[1];
        }
    }
    parts = [];
    for (var a in comps) {
        parts.push(a + "=" + comps[a]);
    }

    return parts.join('&');
}

function stripPrefixFromParams(params, prefix) {
    let paramMap = new Map();
    
    // Split the query string into individual parameters
    params.split('&').forEach(function(param) {
        let [key, value] = param.split('=');
        
        // If the parameter starts with the prefix, remove it and store it in the map
        if (key.startsWith(prefix)) {
            key = key.substring(prefix.length);
            paramMap.set(key, value);
        } else if (!paramMap.has(key)) {
            // Only add the parameter if it isn't already in the map (i.e., prefixed version doesn't exist)
            paramMap.set(key, value);
        }
    });

    // Reconstruct the query string from the map
    return Array.from(paramMap)
        .map(([key, value]) => `${key}=${value}`)
        .join('&');
}


function cwp_remove_string_value( list, value, separator ) {
    if (list === undefined) return false;

    separator = separator || ",";
    var values = list.split(separator);
    for(var i = 0 ; i < values.length ; i++) {
      if(values[i] == value) {
          values.splice(i, 1);
          return values.join(separator);
      }
    }
    return list;
}
function getDateRange(from, to, separator = '-') {
    var from_val = from.val(),
        to_val   = to.val();

    if (from_val === '' && to_val === '') return '';
    return from_val + separator + to_val;
}

function getDate(element) {
    var date;
    try {
        date = jQuery.datepicker.parseDate("mm/dd/yy", element.value);
    } catch (error) {
        date = null;
    }

    return date;
}

jQuery(document).ready(function(){
	if(jQuery(".listing-switcher").length > 0 ){
        jQuery(document).on("click", '.cwp-archive-toggle-Listing-style .listing-switcher', function() {
			$this = jQuery(this);
			if( $this.hasClass('list-view') ){
				cwp_setCookie( "cwp_archive_switcher", 'list-view' , 30 );
			}else if( $this.hasClass('grid-view') ) {
				cwp_setCookie("cwp_archive_switcher", 'grid-view' , 30);
			}
        });
    }

    if (jQuery('.cwp-taxonomy-field').length > 0) {
        cwp_display_groups_meta_by_terms_onLoad();
        jQuery(document).on('change', '.cwp-taxonomy-field', function () {
            cwp_display_groups_meta_by_terms_onLoad();
     });
    }

});

function cwp_display_groups_meta_by_terms_onLoad(){
    jQuery(".cwp-conditional-by-term").hide();
    jQuery(".cwp-taxonomy-field").each(function() {
        var thisObj  = jQuery(this),
            type     = thisObj.attr('type'),
            id       = thisObj.attr('id'),
            val = '';
        if (type == 'checkbox'){
            if (thisObj.is(':checked')){
                var val = jQuery(this).val();
            }
        }else {
            var val = jQuery(this).val();
        }
        if(val != ''){
            cwp_display_groups_meta_by_terms(val,id);
        }
        
    });
}

function cwp_display_groups_meta_by_terms(objectVal,objectID){
    jQuery(".cwp-conditional-by-term").each(function() {
        var thisObj     = jQuery(this);
        var group_terms = thisObj.data('terms');
        if( typeof group_terms !== 'undefined' && group_terms !== '' ){
            var group_terms_arr = group_terms.toString().split(",");
            if(Array.isArray(objectVal) && objectVal.length != 0){
                jQuery.each(objectVal, function(index, item) {
                    if( jQuery.inArray(item, group_terms_arr) != '-1' ){
                        thisObj.show();
                    }
                }.bind(this));
            }else if( jQuery.inArray(objectVal, group_terms_arr) != '-1' ){
                thisObj.show();
            }
        }
    });
}
	
function cwp_setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}