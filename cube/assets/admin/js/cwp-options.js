jQuery(document).ready(function () {

    jQuery(document).on('click', '.cubewp-setting-tab', function (e) {
        e.preventDefault();
        var $this = jQuery(this), $target = $this.attr('data-target-id');

        if (!$this.hasClass('active')) {
            jQuery('.cubewp-setting-tab').removeClass('active');
            $this.addClass('active');
            jQuery('.cubewp-settings-tabs-content').removeClass('active');
            jQuery('#' + $target).addClass('active');
        }
        jQuery(".cwp-single-select").select2();
        jQuery(".cwp-multi-select").select2();
    });

    jQuery(document).on('click', '.cwp-save-settings, .cwp-reset-section, .cwp-reset-settings', function (O) {
        O.preventDefault();
        var $this = jQuery(this),
        $form = $this.closest('form#cwp-options-form'),
        activeTab = $form.find('.cubewp-setting-tab.active').attr('data-target-id'),
        options;

        if( typeof tinymce != "undefined" ) {
            $form.find(".wp-editor-area").each(function() {
                var editor = jQuery(this),
                    editor_id = editor.attr('id'),
                    postContent = tinymce.get(editor_id).getContent();
                editor.val( postContent );
            });
        }

        options = $form.serialize();
        if (!$form.hasClass('processing')) {
            $this.addClass('processing');
            $form.addClass('processing');
            jQuery('.cwp-options-alert').hide('slow');
            var reset = 'none';
            if ($this.hasClass('cwp-reset-section')) {
                reset = 'section';
            } else if ($this.hasClass('cwp-reset-settings')) {
                reset = 'all';
            }
            jQuery.ajax({
                type: 'POST', url: cubewp_settings_params.ajax_url, dataType: 'json', data: {
                    action: 'cwp_save_options',
                    cwpOptions: options,
                    activeTab: activeTab,
                    reset: reset,
                    cwpNonce: jQuery('#_wpnonce').val()
                }, success: function (data) {
                    $form.removeClass('processing');
                    $this.removeClass('processing');
                    if ($this.hasClass('cwp-save-settings')) {
                        $form.closest('div').append(data.html);
                    }else{
                        $form.closest('div').append('<div class="alert-success mr-4 cwp-options-alert" role="alert">Reset Successfully</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                    setTimeout(function () {
                        $this.find('i').remove();
                        jQuery('.cwp-options-alert').hide('slow');
                    }, 2000);
                }, error: function (thrownError) {
                    $form.removeClass('processing');
                    $this.removeClass('processing');
                    setTimeout(function () {
                        jQuery('.cwp-options-alert').hide('slow');
                    }, 2000);
                }
            });
        }
    });

    // Image Uploader
    jQuery(document).on('click', '.image_upload_button', function (e) {
        e.preventDefault();
        var $this = jQuery(this), parent = $this.closest('td'), img = parent.find('.image_preview'),
            input = parent.find('.image_data_field'), multiple = jQuery(this).data('multiple');
        var custom_uploader = wp.media({
            title: jQuery(this).data('uploader_title'), library: {
                // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                type: 'image'
            }, button: {
                text: jQuery(this).data('uploader_button_text') // button label text
            }, multiple: multiple
        }).on('select', function () { // it also has "open" and "close" events
            var attachments = null;
            if (!multiple) {
                attachments = custom_uploader.state().get('selection').first().toJSON();
                img.attr('src', attachments.url);
                input.val(attachments.id);
            } else {

                var uploaded_images = custom_uploader.state().get('selection');
                var attachment_ids = uploaded_images.map(function (attachment) {
                    attachment = attachment.toJSON();
                    var url = attachment.url, id = attachment.id,
                        html = '<a href="' + url + '" target="_blank" class="cwp-gallery-image-parent"><span aria-hidden="true" class="cwp-remove-this-img" data-id="' + id + '">×</span><img class="image_preview cwp-preview-gallery" alt="image" src="' + url + '"><input id="' + $this.data('input-id') + '" class="image_data_field" type="hidden" name="' + $this.data('input-id') + '[]" value="' + id + '"></a>';
                    if (input.val() !== '') {
                        input.val(input.val() + ',' + id);
                    } else {
                        input.val(id);
                    }
                    parent.prepend(html);
                }).join();
            }
        }).open();
    });
    // on remove button click
    jQuery(document).on('click', '.image_delete_button', function (e) {
        e.preventDefault();
        var $this = jQuery(this), parent = $this.closest('td'), img = parent.find('.image_preview'),
            input = parent.find('.image_data_field');

        img.attr('src', '(unknown)');
        input.val('');

    });
    jQuery(document).on('click', '.cwp-remove-this-img', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        $this.closest('a').remove();
    });

    if (jQuery(".cwp-single-select").length > 0) {
        jQuery(".cwp-single-select").select2();
    }
    if (jQuery(".cwp-multi-select").length > 0) {
        jQuery(".cwp-multi-select").select2();
    }
    if (jQuery(".cwp-color-field").length > 0) {
        jQuery('.cwp-color-field').wpColorPicker();
    }

    jQuery(document).on('change', '.cwp-typography-container .cwp-typography-family', function (e) {
        var thisObj = jQuery(this);
        var _val = thisObj.val();
        var s_font_style = thisObj.closest('.cwp-typography-container').find('.typography-style select').data('val');
        var s_font_subset = thisObj.closest('.cwp-typography-container').find('.typography-subset select').data('val');

        jQuery.ajax({
            type: 'POST', url: cwp_settings.ajax_url, dataType: 'json', data: {
                action: 'cwp_get_font_attributes', font_family: _val,
            }, success: function (response) {
                thisObj.closest('.cwp-typography-container').find('.typography-style select').html(response.font_styles);
                thisObj.closest('.cwp-typography-container').find('.typography-style select').val(s_font_style).select2("destroy").select2();
                thisObj.closest('.cwp-typography-container').find('.typography-subset select').html(response.font_subsets);
                thisObj.closest('.cwp-typography-container').find('.typography-subset select').val(s_font_subset).select2("destroy").select2();
            }
        });

    });

    jQuery(document).on('click', '.cwp-image_select-container .cwp-image-select label', function (e) {
        jQuery(this).closest('.cwp-image_select-container').find('label').removeClass('cwp-image-select-selected');
        jQuery(this).addClass('cwp-image-select-selected');

    });

    jQuery(document).on('click', '.cwp-switch-options .cb-enable', function (e) {
        var parent = jQuery(this).parents('.cwp-switch-options');
        jQuery('.cb-disable', parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox-input', parent).val(1).trigger('change');
    });

    jQuery(document).on('click', '.cwp-switch-options .cb-disable', function (e) {
        var parent = jQuery(this).parents('.cwp-switch-options');
        jQuery('.cb-enable', parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox-input', parent).val(0).trigger('change');
    });
    
    if (jQuery(".ace-editor").length > 0) {
        jQuery( '.ace-wrapper .ace-editor' ).each(
            function( index, element ) {
                var params    = JSON.parse( jQuery( this ).parent().find( '.localize_data' ).val() );
                var editor    = jQuery( element ).attr( 'data-editor' );
                var aceeditor = ace.edit( editor );
                aceeditor.setTheme( 'ace/theme/' + jQuery( element ).attr( 'data-theme' ) );
                aceeditor.getSession().setMode( 'ace/mode/' + jQuery( element ).attr( 'data-mode' ) );
                aceeditor.setOptions( params );
                aceeditor.on(
                    'change',
                    function() {
                            jQuery( '#' + element.id ).val( aceeditor.getSession().getValue() );
                            aceeditor.resize();
                    }
                );
            }
        );
    }

});