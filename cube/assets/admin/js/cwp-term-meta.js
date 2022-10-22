jQuery(document).ready(function ($) {

    jQuery('.color-field').wpColorPicker();

    jQuery(document).on('click', '.cwp-image-upload-button', function (e) {
        e.preventDefault();
        var thisObj = jQuery(this), custom_uploader = wp.media({
            multiple: false, library: {type: 'image'},
        }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowed_mime = Array('image/png', 'image/jpg', 'image/gif', 'image/jpeg');
            if (jQuery.inArray(attachment.mime, allowed_mime) !== -1) {
                thisObj.closest('.cwp-upload-field').find('input[type="text"]').val(attachment.url);
                thisObj.closest('.cwp-upload-field').find('input[type="hidden"]').val(attachment.id);
                thisObj.closest('.cwp-upload-field').find('.cwp-remove-upload-button').show();
            } else {
                alert(attachment.mime + ' Not allowed')
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
                    <input type="hidden" name="cwp_term_meta['+ gallery_id +'][]" value="'+ attachment_data.id +'">\
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

    jQuery( function( $ ) {
        jQuery('ul.cwp-gallery-list').sortable({
            items: 'li',
            cursor: '-webkit-grabbing',
            scrollSensitivity: 40,
        });
    });

});