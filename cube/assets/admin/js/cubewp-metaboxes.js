jQuery(document).ready(function ($){
    
    cwp_conditional_fields();
    cwp_hide_group_conditional_with_terms();
    if(jQuery('.categorychecklist input[type="checkbox"]').length > 0){
        cwp_display_groups_meta_by_terms();
    }
    var date_picker_div_interval = setInterval(function () {
            if (jQuery('.editor-post-taxonomies__hierarchical-terms-list input').length > 0) {
                cwp_display_groups_meta_by_terms_for_gutten();
                clearInterval(date_picker_div_interval);
            }
    }, 500);
    
    jQuery(document).on('change', '.categorychecklist input[type="checkbox"]', function () {
        cwp_display_groups_meta_by_terms();
    });
    
    jQuery(document).on('click', '.editor-post-taxonomies__hierarchical-terms-list input', function(){
        cwp_display_groups_meta_by_terms_for_gutten();
    });

    jQuery(document).on('click', '.cwp-file-upload-button', function (e) {
        e.preventDefault();
        var thisObj = jQuery(this),
        custom_uploader = wp.media({
            multiple: false,
            library : {type : 'application/pdf,application/zip,text/plain,text/calendar,application/gzip,application/x-7z-compressed,application/x-zip-compressed,multipart/x-zip,application/x-compressed'},
        }).on('select', function(){
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowed_mime = Array('application/gzip','text/calendar','application/pdf','text/plain','application/zip','application/x-7z-compressed', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
            if(jQuery.inArray( attachment.mime, allowed_mime) !== -1 ){
                thisObj.closest('.cwp-upload-field').find('input[type="text"]').val(attachment.url).trigger("input");
                thisObj.closest('.cwp-upload-field').find('input[type="hidden"]').val(attachment.id);
                thisObj.closest('.cwp-upload-field').find('.cwp-remove-upload-button').show();
            }else{
                alert(attachment.mime+' Not allowed')
            }
        }).open();
    });
    
    jQuery(document).on('click', '.cwp-image-upload-button', function (e) {
        e.preventDefault();
        var thisObj = jQuery(this), custom_uploader = wp.media({
            multiple: false,
            library : {type : 'image'},
        }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowed_mime = Array('image/png','image/jpg','image/gif','image/jpeg');
            if(jQuery.inArray( attachment.mime, allowed_mime) !== -1 ){
                thisObj.closest('.cwp-upload-field').find('input[type="text"]').val(attachment.url).trigger("input");
                thisObj.closest('.cwp-upload-field').find('input[type="hidden"]').val(attachment.id);
                thisObj.closest('.cwp-upload-field').find('.cwp-remove-upload-button').show();
            }else{
                alert(attachment.mime+' Not allowed')
            }
            
        }).open();
    });
    
    jQuery(document).on('click', '.cwp-remove-upload-button', function (e) {
        e.preventDefault();
        var thisObj = jQuery(this);
        thisObj.closest('.cwp-upload-field').find('input[type="text"]').val('');
        thisObj.closest('.cwp-upload-field').find('input[type="hidden"]').val('');
        thisObj.hide();
    });
    
    jQuery(document).on('change', '.switch input[class="switch-field"]', function () {
        if(jQuery(this).is(":checked")){
            jQuery(this).closest('label').find('input[type="hidden"]').val('yes').trigger("input");
        }else{
            jQuery(this).closest('label').find('input[type="hidden"]').val('no').trigger("input");
        }
    });
    
    jQuery( function( $ ) {
        jQuery('ul.cwp-gallery-list').sortable({
            items: 'li',
            cursor: '-webkit-grabbing',
            scrollSensitivity: 40,
        });
    });

    jQuery(document).on('click', '.cwp-gallery-btn', function (e) {
        e.preventDefault();

        var thisObj    = jQuery(this),
        gallery_id = thisObj.closest('.cwp-gallery-field').data('id'),
        custom_uploader = wp.media({
            title: 'Add Images to Gallery',
            library : {type : 'image'},
            multiple: true
        }).on('select', function() {
            var attachments = custom_uploader.state().get('selection').map(function( attachment_data ) {
                attachment_data.toJSON();
                return attachment_data;
            });

            var attachments_list = '';
            jQuery.each( attachments, function( key, attachment_data ) {
                var attachment = '<li class="cwp-gallery-item" data-id="'+ attachment_data.id +'">\
                    <input type="hidden" name="cwp_meta['+ gallery_id +'][]" value="'+ attachment_data.id +'">\
                    <div class="thumbnail">\
                        <img src="'+ attachment_data.attributes.url +'" alt="'+ attachment_data.attributes.title +'">\
                    </div>\
                    <div class="cwp-gallery-actions">\
                        <a class="remove-gallery-item" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>\
                    </div>\
                </li>';
                attachments_list += attachment;
            });
            jQuery('#cwp-gallery-'+ gallery_id +' .cwp-gallery-list').append(attachments_list);
        }).open();

    });

    jQuery(document).on('click', '.remove-gallery-item', function (e) {
        jQuery(this).closest('li.cwp-gallery-item').remove();
    });
    
    jQuery(document).on('click', '.cwp-repeating-field .cwp-add-row-btn', function (e) {
        var thisObj = jQuery(this);
        jQuery.ajax({
            type: 'POST',
            url: cwp_vars_params.ajax_url,
            data: 'action=cwp_add_repeating_field&id='+ thisObj.data('id'),
            dataType : 'json',
            success: function (resp) {
                thisObj.closest('.cwp-repeating-field').find('.cwp-repeating-table').append(resp.sub_field_html);
                cwp_repreating_fields_county(thisObj);
            }
        });
        
    });
    
    jQuery('.cwp-repeating-table tbody').sortable({
        cursor: 'move',
        handle: '.cwp-repeating-count'
    });
    
    jQuery(document).on('click', '.cwp-remove-repeating-field', function (e) {
        jQuery(this).closest('.cwp-repeating-field').remove();
        cwp_repreating_fields_county(jQuery(this));
    });
    
    if( jQuery('select#role').length > 0 ){
        cwp_display_groups_meta_by_user_role();
        jQuery(document).on('change', 'select#role', function () {
            cwp_display_groups_meta_by_user_role();
        });
    }
    
    if(jQuery('.form-table #plan_type').length > 0){
        cwp_hide_show_plan_fields();
    }
    jQuery(document).on('change', '.form-table #plan_type', function (e) {
        cwp_hide_show_plan_fields();
    });

    jQuery(document).on('click', '.cubewp-address-manually', function () {
        var $this = jQuery(this),
            parent = $this.closest(".cwp-google-address"),
            lat = parent.find(".latitude"),
            long = parent.find(".longitude"),
            address = parent.find(".address");
        if (address.hasClass('gm-err-autocomplete')) {
            address.removeClass("gm-err-autocomplete").removeAttr("style disabled").prop("placeholder", address.attr("data-placeholder"));
            parent.find(".cwp-get-current-location").remove();
        }
        if ($this.hasClass('button-primary')) {
            $this.removeClass('button-primary');
            lat.attr("type", "hidden");
            long.attr("type", "hidden");
        }else {
            $this.addClass('button-primary');
            lat.attr("type", "text");
            long.attr("type", "text");
        }
    });
    
    function cwp_hide_show_plan_fields(){
        var plan_type = jQuery('.form-table #plan_type').val();
        if(plan_type == 'package'){
            jQuery('.form-table #no_of_posts').closest('tr').show();
        }else{
            jQuery('.form-table #no_of_posts').closest('tr').hide();
        }
    }

});

function cwp_display_groups_meta_by_terms(){
    jQuery(".postbox .group-terms").each(function() {
        var thisObj = jQuery(this);
        var group_terms = thisObj.val();
        if( group_terms != '' ){
            var group_terms_arr = group_terms.split(",");
            
            var selected_terms = new Array();
            if(jQuery('.categorychecklist').length > 0){
                jQuery(".categorychecklist").each(function() {
                    jQuery(this).find('input[type="checkbox"]:checked').each(function() {
                        if( jQuery(this).val() !== '' ){
                            selected_terms.push(jQuery(this).val());

                        }
                    });
                });
            }
            var terms_diff = cwp_array_diff(group_terms_arr,selected_terms);
            if( terms_diff == '' ){
                jQuery(thisObj).closest('.postbox ').addClass('hidden');
            }else{
                jQuery(thisObj).closest('.postbox ').removeClass('hidden');
            }
        }
    });
}

function cwp_display_groups_meta_by_terms_for_gutten(){
    
    jQuery(".group-terms").each(function() {
        var thisObj = jQuery(this);
        var group_terms = thisObj.val(),
            group_terms_name = thisObj.attr('data-term-name');
        if( group_terms != '' ){
            var group_terms_arr = group_terms_name.split(",");
            var selected_terms = new Array();
            if(jQuery('.editor-post-taxonomies__hierarchical-terms-list input').length > 0){
                jQuery('.editor-post-taxonomies__hierarchical-terms-list input:checked').each(function() {
                    termIDs = jQuery(this).parent().next('label').text();
                    if( termIDs !== '' ){
                        selected_terms.push(termIDs);
                    }
                });
                var terms_diff = cwp_array_diff(group_terms_arr,selected_terms);
                var filteredArray = terms_diff.filter(arrayFilter);
                if(filteredArray == ''){
                    jQuery(thisObj).closest('.postbox ').removeClass('active-group');
                    jQuery(thisObj).closest('.postbox ').addClass('hidden');
                }else{
                    jQuery(thisObj).closest('.postbox ').addClass('active-group');
                    jQuery(thisObj).closest('.postbox ').removeClass('hidden');
                }
            }
        }
    });
}
function arrayFilter(array){
    return (array != null && array !== false && array !== "");
}
function cwp_array_diff( array_1, array_2 ) {
    var diffItems = [];
    jQuery.grep(array_1, function(i) {
        if (jQuery.inArray(i, array_2) !== -1){
            diffItems.push(i);
        }
    });

    return diffItems;
}

function cwp_hide_group_conditional_with_terms(){

    jQuery(".postbox .group-terms").each(function() {
        var thisObj = jQuery(this);
        var group_terms = thisObj.val();
        if( group_terms != '' ){
            jQuery(thisObj).closest('.postbox ').addClass('hidden');
        }
    }
)};

function cwp_display_groups_meta_by_user_role(){
    var selected_role = jQuery('select#role').val();
    jQuery(".cwp-user-meta-fields").each(function() {
        var thisObj = jQuery(this);
        var group_role = thisObj.data('role');
        if( group_role != '' ){
            var group_role_arr = group_role.split(",");
            if(jQuery.inArray(selected_role, group_role_arr) !== -1){
                jQuery(thisObj).find("tr").removeClass("hidden");
                jQuery(thisObj).prev('h2').show(500);
            }else{
                jQuery(thisObj).find("tr").addClass("hidden");
                jQuery(thisObj).prev('h2').hide(500);
            }
        }
    });
}



function cwp_condition_logic(selectedVal, fieldVal, Compare, Target) {
    Target = '.' + Target;
    if (Compare === '!empty') {
        if (selectedVal !== '' || typeof selectedVal != 'undefined') {
            jQuery(Target).show();
            return true;
        } else {
            jQuery(Target).hide();
            return true;
        }
    } else if (Compare === 'empty') {
        if (selectedVal === '' || typeof selectedVal == 'undefined') {
            jQuery(Target).show();
            return true;
        } else {
            jQuery(Target).hide();
            return true;
        }
    } else if (Compare === '==') {
        if (selectedVal === fieldVal) {
            jQuery(Target).show();
            return true;
        } else {
            jQuery(Target).hide();
            return true;
        }
    } else if (Compare === '!=') {
        if (selectedVal !== fieldVal && selectedVal !== '') {
            jQuery(Target).show();
            return true;
        } else {
            jQuery(Target).hide();
            return true;
        }
    }
    return false;
}


function cwp_conditional_fields() {
    var cwp_conditional_logic = jQuery('.conditional-logic');
    if (cwp_conditional_logic.length > 0) {
        cwp_conditional_logic.each(function () {
            var $this = jQuery(this),
                field = $this.attr('data-field'),
                value = $this.attr('data-value'),
                operator = $this.attr('data-operator');

                var parent = jQuery('*[name="cwp_meta[' + field + ']"]');
                var parentCheckbox = jQuery('[name="cwp_meta[' + field + '][]"]');
                var selectedVal = parent.val();
                if (parent.is(':checked') || selectedVal != '' || selectedVal == ''){
                    cwp_condition_logic(selectedVal, value, operator, field);
                }else if(parentCheckbox.is(':checked')){
                    var selectedVal = parentCheckbox.val();
                    cwp_condition_logic(selectedVal, value, operator, field);
                }
            
            jQuery(document).on('change input', '*[name="cwp_meta[' + field + ']"]', function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                var selectedVal = jQuery(this).val();
                cwp_condition_logic(selectedVal, value, operator, field);
            });

            jQuery(document).on('change', 'select[name="cwp_meta[' + field + '][]"]', function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                var selectedVal = jQuery(this).val();
                if (selectedVal.length) {
                    if(jQuery.inArray(value, selectedVal) !== -1) {
                        cwp_condition_logic(value, value, operator, field);
                    }else {
                        cwp_condition_logic(selectedVal[0], value, operator, field);
                    }
                }else {
                    cwp_condition_logic("", value, operator, field);
                }
            });

            var value_condition = '[value="' + value + '"]';
            if (operator === '!empty' || operator === 'empty' || operator === '!=') value_condition = '';
            jQuery(document).on('input', '*[name="cwp_meta[' + field + '][]"]' + value_condition, function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                var $this = jQuery(this),
                    selectedVal = '';
                if ($this.is(':checked')) selectedVal = $this.val();
                if (operator === '!empty' || operator === 'empty' || operator === '!=') {
                    jQuery('*[name="cwp_meta[' + field + '][]"]:checked').each(function () {
                        selectedVal = jQuery(this).val();
                    });
                }
                if (operator === '!=') {
                    var target_field = jQuery('*[name="cwp_meta[' + field + '][]"][value="' + value + '"]');
                    if (target_field.is(':checked')) {
                        selectedVal = target_field.val();
                    }
                }
                cwp_condition_logic(selectedVal, value, operator, field);
            });
        });
    }
}


jQuery(document).ready(function () {
    cubewp_init_resources();
});

function cwp_repreating_fields_county(thisObj) {
    if (jQuery('.cwp-repeating-field .cwp-repeating-field').length > 0) {
        thisObj.closest('.cwp-repeating-field').find('.cwp-repeating-field').each(function (i, obj) {
            jQuery(this).find('.cwp-repeating-count .count').text(Number(i) + 1);
        });
        cubewp_init_resources();
    }
}

function cubewp_init_select2(selects) {
    selects.each(function () {
        var $this = jQuery(this),
            placeholder = $this.attr('placeholder'),
            dropdown_type = $this.attr('data-dropdown-type'),
            dropdown_values = $this.attr('data-dropdown-values');

        if (!$this.hasClass('cubewp-remote-options')) {
            jQuery(this).select2({
                width: '100%',
                placeholder: placeholder,
                minimumResultsForSearch: 10
            });
        } else {
            jQuery(this).select2({
                width: '100%',
                placeholder: placeholder,
                minimumInputLength: 3,
                ajax: {
                    url: cwp_vars_params.ajax_url,
                    dataType: "json",
                    type: "POST",
                    data: function (params) {
                        return {
                            action: 'cubewp_dynamic_options',
                            dropdown_type: dropdown_type,
                            dropdown_values: dropdown_values,
                            keyword: params.term,
                            security_nonce: cwp_vars_params.nonce_option
                        };
                    },
                    processResults: function (response) {
                        if (response.success) {
                            return {
                                results: jQuery.map(response.data, function (item) {
                                    return {
                                        text: item.label,
                                        id: item.value
                                    }
                                })
                            };
                        }
                    }
                }
            });
        }
    })
}

function cubewp_init_date_pickers(data_pickers) {
    data_pickers.each(function () {
        var thisObj = jQuery(this),
            args = {
            dateFormat: 'd/m/yy',
            altField: thisObj.find('input[type="hidden"]'),
            altFormat: 'yy-mm-dd',
            changeYear: true,
            yearRange: "-100:+100",
            changeMonth: true,
            showButtonPanel: true,
            firstDay: '0',
            beforeShow: function (input, datepicker) {
                setTimeout(function () {
                    datepicker.dpDiv.find('.ui-datepicker-current')
                        .click(function () {
                            jQuery(input).datepicker('setDate', new Date());
                        });
                }, 1);
                return {};
            }
        };
        thisObj.find('input[type="text"]').datepicker(args);
    });
}

function cubewp_init_time_pickers(time_pickers) {
    time_pickers.each(function () {
        var thisObj = jQuery(this),
            args = {
                timeFormat: 'HH:mm:ss',
                altField: thisObj.find('input[type="hidden"]'),
                altFieldTimeOnly: false,
                altTimeFormat: 'HH:mm:ss',
                showButtonPanel: true,
                controlType: 'select',
                oneLine: true,
                timeOnly: true,
            };
        thisObj.find('input[type="text"]').timepicker(args);
    });
}

function cubewp_init_date_time_pickers(date_time_pickers) {
    date_time_pickers.each(function () {
        var thisObj = jQuery(this),
            args = {
                dateFormat: 'd/m/yy',
                timeFormat: 'HH:mm:ss',
                altField: thisObj.find('input[type="hidden"]'),
                altFieldTimeOnly: false,
                altFormat: 'yy-mm-dd',
                altTimeFormat: 'HH:mm:ss',
                changeYear: true,
                yearRange: "-100:+100",
                changeMonth: true,
                showButtonPanel: true,
                firstDay: '0',
                controlType: 'select',
                oneLine: true
            };
        thisObj.find('input[type="text"]').datetimepicker(args);
    });
}

function cubewp_init_resources() {
    var cwp_select2 = jQuery(".cwp-select2 select"),
        data_pickers = jQuery(".cwp-date-picker"),
        time_pickers = jQuery(".cwp-time-picker"),
        date_time_pickers = jQuery(".cwp-date-time-picker");

    /**
     * Initializing Select2 On Select2 UI Dropdowns
     */
    if (cwp_select2.length > 0) {
        cubewp_init_select2(cwp_select2);
    }

    /**
     * Initializing Date Pickers
     */
    if (data_pickers.length > 0) {
        cubewp_init_date_pickers(data_pickers);
    }

    /**
     * Initializing Time Pickers
     */
    if (time_pickers.length > 0) {
        cubewp_init_time_pickers(time_pickers);
    }

    /**
     * Initializing Date And Time Pickers
     */
    if (date_time_pickers.length > 0) {
        cubewp_init_date_time_pickers(date_time_pickers);
    }

    /**
     * Wrapping Pickers In .cwp-ui-datepicker
     */
    var pickers_wrapper = jQuery('body > #ui-datepicker-div');
    if (pickers_wrapper.length > 0) {
        pickers_wrapper.wrap('<div class="cwp-ui-datepicker" />');
    }
}