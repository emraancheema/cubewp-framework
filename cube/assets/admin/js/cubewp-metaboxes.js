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
            allowed_types = 'application/pdf,application/zip,text/plain,text/calendar,application/gzip,application/x-7z-compressed,application/x-zip-compressed,multipart/x-zip,application/x-compressed',
            custom_allowed_types = thisObj.attr("data-allowed-types");
        if (custom_allowed_types !== '') {
            allowed_types = custom_allowed_types;
        }
        var custom_uploader = wp.media({
            multiple: false,
            library : {type : allowed_types},
        }).on('select', function(){
            var attachment = custom_uploader.state().get('selection').first().toJSON(),
                allowed_types_arr = allowed_types.split(",");
            if(jQuery.inArray( attachment.mime, allowed_types_arr) !== -1 ){
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
        var thisObj = jQuery(this),
            allowed_types = "image/png,image/jpg,image/gif,image/jpeg",
            custom_allowed_types = thisObj.attr("data-allowed-types");
        if (custom_allowed_types !== '') {
            allowed_types = custom_allowed_types;
        }
        var custom_uploader = wp.media({
            multiple: false,
            library : {type : allowed_types},
        }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowed_mime = allowed_types.split(",");
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
    
    jQuery(document).on('change', 'label.cwp-switch input.cwp-switch-field', function () {
        if(jQuery(this).is(":checked")){
            jQuery(this).closest('label').find('input[type="hidden"]').val('Yes').trigger("input");
        }else{
            jQuery(this).closest('label').find('input[type="hidden"]').val('').trigger("input");
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
            allowed_types = "image/png,image/jpg,image/gif,image/jpeg",
            custom_allowed_types = thisObj.attr("data-allowed-types");
        if (custom_allowed_types !== '') {
            allowed_types = custom_allowed_types;
        }
        var custom_uploader = wp.media({
            title: 'Add Images to Gallery',
            library : {type : allowed_types},
            multiple: true
        }).on('select', function() {
            var attachments = custom_uploader.state().get('selection').map(function( attachment_data ) {
                    attachment_data.toJSON();
                    return attachment_data;
                }),
                repeating_name = '',
                repeating_rand = '';
            var attachments_list = '',
                rand_id = makeid(5);
            jQuery.each( attachments, function( key, attachment_data ) {
                var input_name = 'cwp_meta['+ gallery_id +'][]';
                
                if (thisObj.closest(".cwp-field").find(".cwp-gallery-have-custom-name").length > 0) {
                    input_name = thisObj.closest(".cwp-field").find(".cwp-gallery-have-custom-name").val() + '[]';
                }
                if (thisObj.closest('.cwp-repeating-fields').length > 0) {
                    var repeating_id = thisObj.closest('.cwp-repeating-fields').closest(".cwp-field").find('.cwp-add-row-btn').data('id'),
                        gallery_list = thisObj.closest(".cwp-field").find(".cwp-gallery-list").find(".cwp-gallery-item");
                    repeating_name = "[" + repeating_id + "]";
                    repeating_rand = "[" + rand_id + "]";
                    if (gallery_list.length > 0) {
                        gallery_list = gallery_list.first().find("input[type=hidden]").attr("name");
                        input_name = gallery_list;
                    }else {
                        input_name = 'cwp_meta' + repeating_name + '['+ gallery_id +']' + repeating_rand + '[]';
                    }
                }
    
                var attachment = '<li class="cwp-gallery-item" data-id="'+ attachment_data.id +'">\
                    <input type="hidden" name="' + input_name + '" value="'+ attachment_data.id +'">\
                    <div class="thumbnail">\
                        <img src="'+ attachment_data.attributes.url +'" alt="'+ attachment_data.attributes.title +'">\
                    </div>\
                    <div class="cwp-gallery-actions">\
                        <a class="remove-gallery-item" href="javascript:void(0);"><span class="dashicons dashicons-trash"></span></a>\
                    </div>\
                </li>';
                attachments_list += attachment;
            });
            thisObj.closest(".cwp-field").find(".cwp-gallery-list").append(attachments_list);
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
                var new_row = thisObj.closest('.cwp-repeating-field').find('.cwp-repeating-table').append(resp.sub_field_html);
                cwp_repreating_fields_county(thisObj);
                if (typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
                    if (new_row.find(".required").length > 0) {
                        wp.data.dispatch('core/editor').lockPostSaving('cubewp_have_required_fields');
                    }
                }
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
    
    jQuery(document).on('input', 'input[maxlength]', function () {
        var maxDigits = jQuery(this).attr('maxlength');
        var inputValue = jQuery(this).val().trim();
    
        if (inputValue.length > maxDigits) {
            jQuery(this).val(inputValue.slice(0, maxDigits)).trigger('input');
        }
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
    setTimeout(function () {
        var address_field = jQuery('.address.pac-target-input');
        if (address_field.length > 0) {
            address_field.each(function() {
                if (jQuery(this).val() !== '') {
                    if (jQuery(this).hasClass('gm-err-autocomplete')) {
                        jQuery(this).removeClass("gm-err-autocomplete").removeAttr("style disabled").prop("placeholder", jQuery(this).attr("data-placeholder"));
                    }
                }
            });
        }
    }, 5000);
});

function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

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
                jQuery(thisObj).removeClass("cwp-hide");
            }else{
                jQuery(thisObj).addClass("cwp-hide");
            }
        }
    });
}



function cwp_condition_logic(selectedVal, fieldVal, Compare, Target) {
    Target = '.' + Target;
    if (Compare === '!empty') {
        if (selectedVal !== '' && typeof selectedVal != 'undefined') {
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
        if (fieldVal.includes('--OR--')) {
            var exploded = fieldVal.split('--OR--');
            if (exploded.includes(selectedVal)) {
                jQuery(Target+fieldVal).show();
                return true;
            }else {
                jQuery(Target+fieldVal).hide();
                jQuery(Target+fieldVal).find('input,select').val('').trigger('input');
                return true;
            }
        }else {
            if (selectedVal === fieldVal) {
                jQuery(Target+fieldVal).show();
                return true;
            } else {
                jQuery(Target+fieldVal).hide();
                jQuery(Target+fieldVal).find('input,select').val('').trigger('input');
                return true;
            }
        }
    }else if (Compare === '!=') {
        if (selectedVal !== fieldVal && selectedVal !== '') {
            jQuery(Target+fieldVal).show();
            return true;
        } else {
            jQuery(Target+fieldVal).hide();
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
                var selectedVal = jQuery(this).val();
                cwp_condition_logic(selectedVal, value, operator, field);
            });

            jQuery(document).on('change', 'select[name="cwp_meta[' + field + '][]"]', function (event) {
                event.preventDefault();
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
    var delete_relation = jQuery('.cubewp-delete-relation');
    if (delete_relation.length > 0) {
        delete_relation.on('click', function (event) {
            event.preventDefault();
            var $this = jQuery(this),
                parent = $this.closest('.cubewp-post-relation'),
                relation_id   = $this.attr('data-relation-id'),
                relation_of   = $this.attr('data-relation-of'),
                relation_with = $this.attr('data-relation-with');
            if ( ! parent.hasClass('cubewp-active-ajax')) {
                if (confirm(cubewp_metaboxes_params.confirm_remove_relation)) {
                    parent.addClass('cubewp-active-ajax');
                    jQuery.ajax({
                        type: 'POST',
                        url: cwp_vars_params.ajax_url,
                        data: {
                            action: 'cubewp_remove_relation',
                            'relation_id': relation_id,
                            'relation_of': relation_of,
                            'relation_with': relation_with,
                            'nonce': cubewp_metaboxes_params.remove_relation_nonce
                        },
                        dataType : 'json',
                        success: function (resp) {
                            if (resp.status === 'success') {
                                parent.slideUp(function () {
                                    parent.remove();
                                });
                            }else {
                                alert(resp.msg);
                                parent.removeClass('cubewp-active-ajax');
                            }
                        }
                    });
                }
            }
        });
    }

    cubewp_init_resources();
});

function cwp_repreating_fields_county(thisObj) {
    if (jQuery('.cwp-repeating-field .cwp-repeating-field').length > 0) {
        thisObj.closest('.cwp-repeating-field').find('.cwp-repeating-field').each(function (i, obj) {
            jQuery(this).find('.cwp-repeating-count .count').text(Number(i) + 1);
        });
        cubewp_init_resources();
        initialize_google_address();
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
                language: {
                    inputTooShort: function() {
                        if (dropdown_type === 'user') {
                            return "Please enter username.";
                        }
                    }
                },
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
                timeFormat: 'hh:mm:ss TT',
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

function cubewp_init_range_pickers(range_picker) {
    range_picker.each(function () {
        var thisObj = jQuery(this),
            current_value_input = thisObj.closest("td");
        if (!current_value_input.find('.cubewp-current-value').length) {
            current_value_input.prepend("<span class='cubewp-current-value'>" + thisObj.val() + "</span>");
            thisObj.on("input", function () {
                jQuery(this).closest("td").find(".cubewp-current-value").text(jQuery(this).val());
            });
        }
    });
}

function cubewp_init_resources() {
    var cwp_select2 = jQuery(".cwp-select2 select"),
        data_pickers = jQuery(".cwp-date-picker"),
        time_pickers = jQuery(".cwp-time-picker"),
        date_time_pickers = jQuery(".cwp-date-time-picker"),
        range_picker = jQuery(".cwp-field-range");

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
     * Initializing range field
     */
    if (range_picker.length > 0) {
        cubewp_init_range_pickers(range_picker);
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
