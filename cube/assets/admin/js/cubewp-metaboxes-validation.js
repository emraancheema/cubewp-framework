jQuery(document).ready(function ($) {
    jQuery(document).on("input", "#post_type_slug", function (event) {
        var thisObj = jQuery(this), value = cubewp_validate_input_value(thisObj.val()),
            prev_slug = thisObj.data('prev_slug');
        thisObj.prop('value', value);
        thisObj.closest('td').find('.cwp-error-message').remove();

        if (cubewp_metaboxes_validation_params.existing_post_types.hasOwnProperty(value) && value !== prev_slug) {
            thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>Slug already exist</p></div>');
            jQuery('.cwp-save-button').prop('disabled', true);
        } else {
            jQuery('.cwp-save-button').prop('disabled', false);
        }
    });

    jQuery(document).on("input", "#taxonomy_slug", function (event) {
        var thisObj = jQuery(this), value = cubewp_validate_input_value(thisObj.val()),
            prev_slug = thisObj.data('prev_slug');
        thisObj.prop('value', value);
        thisObj.closest('td').find('.cwp-error-message').remove();

        if (cubewp_metaboxes_validation_params.existing_taxonomies.hasOwnProperty(value) && value !== prev_slug) {
            thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>Slug already exist</p></div>');
            jQuery('.cwp-save-button').prop('disabled', true);
        } else {
            jQuery('.cwp-save-button').prop('disabled', false);
        }
    });

    jQuery(document).on("input", ".field-name,.field-id,.option-value", function (event) {
        var thisObj = jQuery(this),
            value = cubewp_validate_input_value(thisObj.val());
        thisObj.prop('value', value);
        thisObj.closest('td').find('.cwp-error-message').remove();
    });

    if (jQuery('form#post').length > 0) {
        jQuery(document).on('submit', 'form#post', function (event) {
            if (!cubewp_admin_fields_validation()) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
            }
        });
    }else if (typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
        if (cubewp_admin_fields_validation() === false) {
            wp.data.dispatch('core/editor').lockPostSaving('cubewp_have_required_fields');
        }
        var conditional_fields = jQuery('.conditional-logic');
        if (conditional_fields.length > 0) {
            conditional_fields.each(function () {
                var field_id = jQuery(this).attr('data-field-id');
                jQuery(document).on('input change', '#' + field_id, function () {
                    if (cubewp_admin_fields_validation() === false) {
                        wp.data.dispatch( 'core/editor' ).lockPostSaving( 'cubewp_have_required_fields' );
                    }else {
                        wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'cubewp_have_required_fields' );
                    }
                    cubewp_admin_fields_validation();
                });
            });
        }
        jQuery(document).on('input change', '.cwp-validation .required', function () {
            if (cubewp_admin_fields_validation() === false) {
                wp.data.dispatch( 'core/editor' ).lockPostSaving( 'cubewp_have_required_fields' );
            }else {
                wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'cubewp_have_required_fields' );
            }
            cubewp_admin_fields_validation();
        });
        jQuery(document).on('cubewp-tinymce-trigger',function () {
            if (cubewp_admin_fields_validation() === false) {
                wp.data.dispatch( 'core/editor' ).lockPostSaving( 'cubewp_have_required_fields' );
            }else {
                wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'cubewp_have_required_fields' );
            }
            cubewp_admin_fields_validation();
        });
        jQuery(document).on('click', '.cwp-remove-repeating-field', function () {
            setTimeout(function () {
                if (cubewp_admin_fields_validation() === false) {
                    wp.data.dispatch( 'core/editor' ).lockPostSaving( 'cubewp_have_required_fields' );
                }else {
                    wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'cubewp_have_required_fields' );
                }
                cubewp_admin_fields_validation();
            }, 1000)
        });
    }else if (jQuery('form#createuser').length > 0) {
        jQuery(document).on('submit', 'form#createuser', function (event) {
            if (!cubewp_admin_fields_validation()) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
            }
        });
    }else if (jQuery('form#your-profile').length > 0) {
        jQuery(document).on('submit', 'form#your-profile', function (event) {
            if (!cubewp_admin_fields_validation()) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
            }
        });
    }

    function cubewp_admin_fields_validation() {
        var is_valid = true,
            validation_msg;
            jQuery('.parent-field.cwp-field-set').removeClass('cwp-required-container');
        jQuery(".cwp-validation .required:not([disabled])").each(function () {
            var _thisObj = jQuery(this), tagname = _thisObj.prop("tagName");
            var hiddenTR = _thisObj.closest('tr.conditional-field').is(':hidden');
            var hiddenRW = _thisObj.closest('tr.conditional-logic').is(':hidden');
            var hiddenPB = _thisObj.closest('.postbox').hasClass('hidden');
            if(hiddenTR || hiddenPB || hiddenRW){            
            }else if (tagname === 'TR') {
                if (_thisObj.find('input').length > 0) {
                    var haveChecked = false;
                    _thisObj.find('td').find('.cwp-error-message').remove();
                    _thisObj.find('input').each(function () {
                        var __thisObj = jQuery(this);
                        if (__thisObj.is(':checked')) {
                            haveChecked = true;
                        }
                    });
                    if (!haveChecked) {
                        is_valid = false;
                        validation_msg = _thisObj.find('input').data('validation_msg');
                        if(validation_msg == undefined){
                            validation_msg = _thisObj.data('validation_msg');
                        }
                        if (validation_msg === '') {
                            validation_msg = 'This field is required.';
                        }
                        _thisObj.find('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                        _thisObj.closest('.parent-field.cwp-field-set.cwp-add-form-feild').addClass('cwp-required-container');
                    }
                }else if (_thisObj.find('.wp-editor-wrap').length > 0) {
                    var id = _thisObj.find('.wp-editor-area').attr('id'),
                        val;
                    if (tinymce.get(id) !== null) {
                        val = tinymce.get(id).getContent();
                        _thisObj.find('td').find('.cwp-error-message').remove();
                        if (val === '') {
                            is_valid = false;
                            validation_msg = _thisObj.data('validation_msg');
                            if (validation_msg === '' || validation_msg === 'undefined') {
                                validation_msg = 'This field is required.';
                            }
                            _thisObj.find('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                            _thisObj.closest('.parent-field.cwp-field-set.cwp-add-form-feild').addClass('cwp-required-container');
                        }
                    }else {
                        setTimeout(function () {
                            jQuery(document).trigger('cubewp-tinymce-trigger');
                        }, 100);
                    }
                }
            } else {
                _thisObj.closest('td').find('.cwp-error-message').remove();
                if ('multiple' === _thisObj.attr("multiple")) {
                    if ( ! _thisObj.val().length) {
                        is_valid = false;
                        validation_msg = _thisObj.data('validation_msg');
                        if (validation_msg === '') {
                            validation_msg = 'This field is required.';
                        }
                        _thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                        _thisObj.closest('.parent-field.cwp-field-set.cwp-add-form-feild').addClass('cwp-required-container');
                    }
                }else {
                    if (_thisObj.val() === '') {
                        is_valid = false;
                        validation_msg = _thisObj.data('validation_msg');
                        if (validation_msg === '') {
                            validation_msg = 'This field is required.';
                        }
                        _thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                        _thisObj.closest('.parent-field.cwp-field-set.cwp-add-form-feild').addClass('cwp-required-container');
                    }else {
                        if (typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
                            if (_thisObj.attr('type') === 'email') {
                                const validateEmail = (email) => {
                                    return email.match(
                                        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                                    );
                                };
                                if ( ! validateEmail(_thisObj.val())) {
                                    is_valid = false;
                                    validation_msg = 'Please enter valid email.';
                                    _thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
                                    _thisObj.closest('.parent-field.cwp-field-set.cwp-add-form-feild').addClass('cwp-required-container');
                                }
                            }
                        }
                    }
                }
            }
        });
        jQuery('.button, [type="submit"]').removeClass('disabled');
        jQuery('.spinner').removeClass('is-active').css('display', 'none');

        return is_valid;
    }

    jQuery(document).on('click', '.editor-post-publish-button', function (e) {
        var thisObj = jQuery(this);
        var is_valid = true;
        jQuery(".cwp-validation .required").each(function () {
            var _thisObj = jQuery(this);
            _thisObj.closest('td').find('.cwp-error-message').remove();
            if (_thisObj.val() === '') {
                is_valid = false;
                var validation_msg = _thisObj.data('validation_msg');
                if (validation_msg == '') {
                    var validation_msg = 'This field is required.';
                }
                _thisObj.closest('td').append('<div class="cwp-notice cwp-error-message"><p>' + validation_msg + '</p></div>');
            }
        });
        if ( ! is_valid && jQuery(".cwp-error-message").length > 0) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".cwp-error-message").first().offset().top - 300
            }, 1000);
        }
        jQuery('.button, [type="submit"]').removeClass('disabled');
        jQuery('.spinner').removeClass('is-active').css('display', 'none');

        return is_valid;
    });
});

function cubewp_validate_input_value(value) {
    if (/^[a-zA-Z0-9- ]*$-/.test(value) === false) {
        var _value = value.replace(/ /g, "_");
        _value = _value.toLowerCase();
        _value = replaceSpecialCharacters(_value);
        if (value !== _value) {
            return _value;
        }
    }

    return value;
}

function replaceSpecialCharacters(string) {
    string = string.replace(/[^a-z0-9\s-]/gi, '_');
    return string;
}