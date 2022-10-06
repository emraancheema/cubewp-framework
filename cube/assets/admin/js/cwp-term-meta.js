jQuery(document).ready(function ($) {

    jQuery('.color-field').wpColorPicker();

    jQuery(document).on('click', '.cwp-image-upload-button', function (e) {
        e.preventDefault();
        var thisObj = jQuery(this), custom_uploader = wp.media({
            multiple: false,
            library : {type : 'image'},
        }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            var allowed_mime = Array('image/png','image/jpg','image/gif','image/jpeg');
            if(jQuery.inArray( attachment.mime, allowed_mime) !== -1 ){
                thisObj.closest('.cwp-upload-field').find('input[type="text"]').val(attachment.url);
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
});