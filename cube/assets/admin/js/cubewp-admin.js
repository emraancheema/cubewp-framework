jQuery(document).ready(function () {
    
    if (jQuery(".cubewp_page_cubewp-post-types").length > 0) {
        disable_rewrite_slug();
        jQuery(document).on('change', 'select#rewrite', function (event) {
            disable_rewrite_slug();
        });
    }

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
        });

        jQuery(document).on('click', '.cwp_import_demo', function(e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: cwp_vars_params.ajax_url,
                data:'action=cwp_import_dummy_data&data_type=dummy',
                dataType: 'json',
                success: function (response) {
                    if( response.success === 'false' ){
                        alert(response.msg);
                    }else{
                        window.location.href = response.redirectURL;
                    }
                }
            });
        });
    }
    
    if (jQuery(".cwp_export").length > 0) {
        jQuery(document).on('click', '.cwp_export', function (e) {
            e.preventDefault();
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
                        jQuery.ajax({
                            type: 'POST',
                            url: cwp_vars_params.ajax_url,
                            data: 'action=cwp_user_data&export=success',
                            dataType: 'json',
                            success: function (response) {
                                if( response.success === 'false' ){
                                    alert(response.msg);
                                }else{
                                    alert(response.msg);
                                    thisObj.closest('.export-form').find('.cwp_download_content').attr('href', response.file_url);
                                    thisObj.closest('.export-form').find('.cwp_download_content').removeClass('hidden');
                                }
                            }
                        });
                    }
                }
            });
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
});