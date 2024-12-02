jQuery(document).ready(function () {
    
    if (jQuery(".cubwp-welcome").length > 0) {
        jQuery('.Section-Faqs').click(function(e) {
            var currentAttrValue = jQuery(this).attr('href');

            if (jQuery(e.target).is('.active')) {
                close_accordion_section();
            } else {
                close_accordion_section();

                jQuery(this).addClass('active');
                jQuery('.Faqs ' + currentAttrValue).slideDown(300).addClass('open');
            }

            e.preventDefault();
        });
    }

    if (jQuery(".cwpform-shortcode").length > 0) {
        jQuery(document).on('click', '.cwpform-shortcode', function (e) {
                var $this = jQuery(this),
                    temp_text = document.createElement("input");
                if ($this.find('.inner').hasClass('copy-to-clipboard')) {
                    temp_text.value = $this.find('.inner').clone().children().remove().end().text();
                    document.body.appendChild(temp_text);
                    temp_text.select();
                    document.execCommand("copy");
                    document.body.removeChild(temp_text);
                }
        });
    }

    if (jQuery(".cubewp_page_cubewp-post-types").length > 0) {
        disable_rewrite_slug();
        jQuery(document).on('change', 'select#rewrite', function (event) {
            disable_rewrite_slug();
        });
    }

    function close_accordion_section() {
        jQuery('.Faqs .Section-Faqs').removeClass('active');
        jQuery('.Faqs .Faqs-section-content').slideUp(300).removeClass('open');
    };

    function disable_rewrite_slug() {
        jQuery('input#rewrite_slug').parents('tr').hide();
        var $this = jQuery('select#rewrite'),
            select = $this.val();

        if ("1" === select) {
            $this.parents('tr').next('tr').show();
        }
    };

    if (jQuery(".cwp-post-type-wrape").length > 0) {
        jQuery(document).on('submit', '.cwp-post-type-wrape form', function (event) {
            var $this = jQuery(this),
                select = $this.find('select[name="action"]').val();

            if ("delete" === select) {
                if ( ! confirm(cwp_vars_params.confirm_text.multiple)){
                    event.preventDefault();
                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    return false;
                }
            }
        });
    }

    jQuery(document).on('click', '.cwp-post-type-wrape .delete a', function (event) {
        if ( ! confirm(cwp_vars_params.confirm_text.single)){
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return false;
        }
    });
    
    var posttype_menu_icon = jQuery(".cwp-selectMenuIcons > span");
    if (posttype_menu_icon.length > 0) {
        posttype_menu_icon.on("click", function (event) {
            event.preventDefault();
            jQuery(this)
                .closest("td")
                .find("#icon")
                .val(jQuery(this)
                    .attr("data-class"));
        });
    }
    
    if (jQuery(".cwp_import").length > 0) {
        jQuery(document).on('click', '.cwp_import', function(e) {
            e.preventDefault();
            if ( confirm( 'Are You Sure?' ) ) {
                var formData = new FormData(document.getElementById('import_form'));
                jQuery.ajax({
                    type: 'POST',
                    url: cwp_vars_params.ajax_url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if( response.success === 'false' ){
                            alert(response.msg);
                        }else{
                            window.location.href = response.redirectURL;
                        }
                    }
                });
            }
        });
        jQuery(document).on('click', '.cwp_import_demo', function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to proceed?\n\nNote: If you are importing the demo on an existing website, you may need to reset the WordPress Installation or remove existing pages to avoid conflicts.")) {
                jQuery(this).append('<div class="loader"></div>');
                jQuery(this).addClass('processing');
                jQuery(this).prop( "disabled", 1 );
                jQuery.ajax({
                    type: 'POST',
                    url: cwp_vars_params.ajax_url,
                    data:'action=cwp_import_dummy_data&data_type=dummy&nonce='+cwp_vars_params.nonce,
                    dataType: 'json',
                    success: function (response) {
                        if( response.success === 'true' ){
                            if( response.content === 'true' ){
                                jQuery.ajax({
                                    type: 'POST',
                                    url: cwp_vars_params.ajax_url,
                                    data:'action=cwp_import_dummy_data&data_type=dummy&content=true&nonce='+cwp_vars_params.nonce,
                                    dataType: 'json',
                                    success: function (response) {
                                        if( response.success === 'false' ){
                                            alert(response.msg);
                                            jQuery(this).prop( "disabled", 0 );
                                        }else{
                                            if(response.redirectURL != null && response.redirectURL != ''){
                                                window.location.href = response.redirectURL;
                                            }else if(response.success_message != null && response.success_message != ''){
                                                jQuery(response.success_message.selecter).text(response.success_message.message);
                                                jQuery(response.success_message.selecter).addClass('done');
                                            }
                                        }
                                    }
                                });
                            }else{
                                if(response.redirectURL != null && response.redirectURL != ''){
                                    window.location.href = response.redirectURL;
                                }else if(response.success_message != null && response.success_message != ''){
                                    jQuery(response.success_message.selecter).text(response.success_message.message);
                                    jQuery(response.success_message.selecter).addClass('done');
                                }
                            }
                        }else if( response.success === 'false' ){
                            alert(response.msg);
                            jQuery(this).prop( "disabled", 0 );
                        }
                    }
                });
            }
        });
    }
    
    if (jQuery(".cwp_export").length > 0) {
        jQuery(document).on('click', '.cwp_export', function (e) {
            e.preventDefault();
            if ( confirm( 'Are You Sure?' ) ) {
                var thisObj = jQuery(this);
                jQuery.ajax({
                    type: 'POST',
                    url: cwp_vars_params.ajax_url,
                    data: jQuery('.export-form').serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if( response.success === 'false' ){
                            alert(response.msg);
                        }else{
                            var export_custom_forms = false;
                            if ( jQuery('.export-form').find('#custom-forms-fields').length > 0 ) {
                                if ( jQuery('.export-form').find('#custom-forms-fields').is(':checked') ) {
                                    export_custom_forms = true;
                                }
                            }
                            var _ajax_data = 'action=cwp_user_data&export=success&nonce='+cwp_vars_params.nonce;
                            if (export_custom_forms) {
                                _ajax_data += '&download_now=false';
                            }
                            jQuery.ajax({
                                type: 'POST',
                                url: cwp_vars_params.ajax_url,
                                data: _ajax_data,
                                dataType: 'json',
                                success: function (response) {
                                    if( response.success === 'false' ){
                                        alert(response.msg);
                                    }else{
                                        if (export_custom_forms) {
                                            jQuery.ajax({
                                                type: 'POST',
                                                url: cwp_vars_params.ajax_url,
                                                data: 'action=cwp_custom_forms&export=success&nonce='+cwp_vars_params.nonce,
                                                dataType: 'json',
                                                success: function (response) {
                                                    if( response.success === 'false' ){
                                                        alert(response.msg);
                                                    }else{
                                                        alert(response.msg);
                                                        thisObj.hide();
                                                        thisObj.closest('.export-form').find('.cwp_download_content').attr('href', response.file_url);
                                                        thisObj.closest('.export-form').find('.cwp_download_content').removeClass('hidden');
                                                    }
                                                }
                                            });
                                        }else {
                                            alert(response.msg);
                                            thisObj.hide();
                                            thisObj.closest('.export-form').find('.cwp_download_content').attr('href', response.file_url);
                                            thisObj.closest('.export-form').find('.cwp_download_content').removeClass('hidden');
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });
    }
    
    if (jQuery('.cwp-widget-select-posttype').length > 0) {
        jQuery(document).on('change', '.cwp-widget-select-posttype', function () {
            let $this = jQuery(this),
                form = $this.closest('form'),
                termSelect = form.find('.cwp-widget-select-term'),
                data = {
                    action: 'cwp_get_terms_by_post_type',
                    post_type: $this.val(),
                    nonce: cwp_vars.nonce
                };
            $this.attr("disabled", "disabled");
            termSelect.attr("disabled", "disabled");
            jQuery.ajax({
                type: 'POST',
                url: cwp_vars.url,
                dataType: 'json',
                data: data,
                success: function (resp) {
                    if (resp.success === true) {
                        $this.removeAttr("disabled");
                        termSelect.empty();
                        var terms = resp.data;
                        if (terms.length > 0) {
                            terms.forEach(function (term) {
                                var termName = term['0'],
                                    termValue = term['1'],
                                    selected = false;
                                if (term['2'] !== "") selected = true;
                                termSelect.append(new Option(termValue, termName, selected));
                            });
                        }
                        termSelect.removeAttr("disabled");
                    }
                }
            });
        });
    }

    if(jQuery('#ctb-add-template-dialog').length > 0){
        jQuery('#ctb-add-template-dialog').dialog({
            autoOpen: false,
            modal: true,
            width: 990,  // Set your desired width here
        });
        
    }

    // Open the dialog
    jQuery('.ctb-add-new-template').on('click', function() {
        jQuery('#ctb-add-template-dialog').dialog('open');
        var editLocation = jQuery(this).data('tlocation'),
            edittype = jQuery(this).data('ttype'),
            editname = jQuery(this).data('tname'),
            editid = jQuery(this).data('tid');
        //Empty all values    
        jQuery('#template_name').val('');
        jQuery('#template_type').val('');
        jQuery('#template_location').val('');

        if(jQuery('.ctb-hidden-post-id').length > 0){
            jQuery('.ctb-hidden-post-id').remove();
        }

        if(editname){
            jQuery('#template_name').val(editname);
        }
        if(edittype){
            jQuery('#template_type').val(edittype);
            jQuery('#template_type').trigger('change');
        }
        if(editLocation){
            setTimeout(function() {
                jQuery('#template_location').val(editLocation);
            }, 1000);
        }else{
            jQuery('#template_location').append('<option value="">Select Template Display Position</option>');
        }
        if(editname){
            jQuery('#add-template-form').append('<input type="hidden" class="ctb-hidden-post-id" name="ctb_edit_template_id" value="'+ editid +'" />');
        }
        
    });

    jQuery('.cwp-save-template').on('click', function() {
        var $form = jQuery(this).closest('form');
        $form.addClass('loader');
        submitForm(jQuery(this).val());
        $form.removeClass('loader');
    });

    function submitForm(action) {
        var formData = jQuery('#add-template-form').serialize();
        jQuery.ajax({
            type: 'POST',
            url: cwp_vars_params.ajax_url,
            data: {
                action: 'cubewp_theme_builder_template',
                template_action: action,
                data: formData
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        location.reload();
                    }
                } else {
                    alert(response.data.message || 'There was an error saving the template.');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert('There was an error processing the request.');
            }
        });
    }

    jQuery('#template_type').on('change', function() {
        var templateType = jQuery(this).val();
        cwp_load_theme_builder_rules(templateType);
    });

    // Trigger change event on page load to set initial state
    //jQuery('#template_type').trigger('change');
    
});

function cwp_load_theme_builder_rules( templateType = ''){
    
    var templateLocation = jQuery('#template_location');
    var excludeLocation = jQuery('#exclude_location');

    templateLocation.empty();
    excludeLocation.empty();

    jQuery.ajax({
        url: ajaxurl, // URL to WordPress admin-ajax.php
        type: 'POST',
        data: {
            action: 'get_template_options',
            template_type: templateType
        },
        success: function(response) {
            if (response.success) {
                templateLocation.append(response.data.template_options);
                excludeLocation.append(response.data.exclude_options);

                if (templateType === '404' || templateType === 'mega-menu' || templateType === 'shop') {
                    templateLocation.closest('.form-fileds').hide();
                } else {
                    templateLocation.closest('.form-fileds').show();
                }
            } else {
                console.log(response.data.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error: ' + status + error);
        }
    });
}