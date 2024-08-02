function cubewp_frontend_form_validation(from) {
    var is_valid = true;
    from.find(".is-required:not(:hidden)").each(function () {
        var _thisObj = jQuery(this);
        _thisObj.find('.cwp-required-field-notice').remove();
        if (_thisObj.parents('.cwp-repeating-single-field-template').length) {
            return true;
        }
        var type = _thisObj.data('type'), id = _thisObj.data('id'), validation_msg = _thisObj.data('validation_msg');
        if (validation_msg === '' || typeof validation_msg == 'undefined') {
            validation_msg = 'This field is required';
        }
        var error_msg = '<div class="cwp-required-field-notice">\
            <p>' + validation_msg + '</p>\
        </div>';
        var val = '';
        if (type === 'wysiwyg_editor') {
            val = tinymce.get(id).getContent();
        } else if (type === 'checkbox') {
            _thisObj.find('.required:checked').each(function () {
                val = 1;
            });
        } else if (type === 'dropdown') {
            var attr = _thisObj.find('.required:not([disabled])').attr("multiple");
            if (typeof attr !== 'undefined' && attr !== false ) {
                if (_thisObj.find('.required:not([disabled])').val().length > 0) {
                    val = 1;
                }
            }else {
                val = 1;
                val = _thisObj.find('.required:not([disabled])').val();
            }
        } else if (type === 'radio'|| type === 'review_star') {
            _thisObj.find('.required:checked').each(function () {
                val = 1;
            });
        } else if (type === 'file' || type === 'image') {
            if (_thisObj.find(".cwp-file-field-preview input").length > 0) {
                val = _thisObj.find(".cwp-file-field-preview input").val();
            } else {
                val = 1;
                val = _thisObj.find('.required:not([disabled])').val();
            }
        } else {
            val = 1;
            val = _thisObj.find('.required:not([disabled])').val();
        }
        if ((typeof val == "string" && !val.replace(/\s/g, '').length) || val === '' || val === 0) {
            if (type === 'checkbox') {
                jQuery(error_msg).insertBefore(_thisObj.find('.cwp-field-checkbox-container'));
            } else if (type === 'radio' || type === 'review_star') {
                jQuery(error_msg).insertBefore(_thisObj.find('.cwp-field-radio-container'));
            } else {
                jQuery(error_msg).insertAfter(_thisObj.find('label'));
            }
            if (is_valid === true) {
                jQuery('html, body').animate({
                    scrollTop: _thisObj.offset().top - 70
                }, 1000);
            }
            is_valid = false;
        }
    });

    return is_valid;
}