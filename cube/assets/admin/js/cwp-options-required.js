(function ($) {
    'use strict';

    $.cubewp = $.cubewp || {};

    $(document).on('change', '.cwp-field-container select, .cwp-field-container input[type=checkbox], .cwp-field-container input[type=radio], .cwp-field-container input[type=hidden]', function () {
        $.cubewp.check_dependencies(this);
    });

    $.each(cubewp_settings_params.folds, function (key, val) {
        if (val == 'hide') {
            var i = jQuery("#cwp-" + key);
            i.parents("tr:first").addClass("hide");
        }
    });

    $.cubewp.check_dependencies = function (variable) {
        var t = $(variable).parents(".cwp-field-container:first").data("id");
        if (cubewp_settings_params.required.hasOwnProperty(t)) {
            $.each(cubewp_settings_params.required[t], function (e) {
                jQuery.each(cubewp_settings_params.required_child[e], function (f, r) {
                    var parentValue = $.cubewp.getContainerValue(r.parent);
                    var show = $.cubewp.check_dependencies_visibility(parentValue, r);
                    var i = jQuery("#cwp-" + e);
                    if (show == true) {
                        $(this).removeClass("hide");
                        i.parents("tr:first").css('display', 'flex');
                    } else {
                        $(this).addClass("hide");
                        i.parents("tr:first").css('display', 'none');
                    }
                });
            });
        }
    }

    $.cubewp.getContainerValue = function (e) {
        var r = $("#cwp-" + e).serializeForm();
        return r[e];
    };

    $.cubewp.check_dependencies_visibility = function (parentValue, data) {
        var show = false;
        var checkValue = data.checkValue;
        var operation = data.operation;
        var arr;

        if ($.isPlainObject(parentValue)) {
            parentValue = Object.keys(parentValue).map(function (key) {
                return [key, parentValue[key]];
            });
        }

        switch (operation) {
            case '=':
            case 'equals':
                if ($.isArray(parentValue)) {
                    $(parentValue[0]).each(function (idx, val) {
                        idx = null;

                        if ($.isArray(checkValue)) {
                            $(checkValue).each(function (i, v) {
                                i = null;
                                if ($.cubewp.makeBoolStr(val) === $.cubewp.makeBoolStr(v)) {
                                    show = true;

                                    return true;
                                }
                            });
                        } else {
                            if ($.cubewp.makeBoolStr(val) === $.cubewp.makeBoolStr(checkValue)) {
                                show = true;

                                return true;
                            }
                        }
                    });
                } else {
                    if ($.isArray(checkValue)) {
                        $(checkValue).each(function (i, v) {
                            i = null;

                            if ($.cubewp.makeBoolStr(parentValue) === $.cubewp.makeBoolStr(v)) {
                                show = true;
                            }
                        });
                    } else {
                        if ($.cubewp.makeBoolStr(parentValue) === $.cubewp.makeBoolStr(checkValue)) {
                            show = true;
                        }
                    }
                }
                break;

            case '!=':
            case 'not':
                if ($.isArray(parentValue)) {
                    $(parentValue[0]).each(function (idx, val) {
                        idx = null;

                        if ($.isArray(checkValue)) {
                            $(checkValue).each(function (i, v) {
                                i = null;

                                if ($.cubewp.makeBoolStr(val) !== $.cubewp.makeBoolStr(v)) {
                                    show = true;

                                    return true;
                                }
                            });
                        } else {
                            if ($.cubewp.makeBoolStr(val) !== $.cubewp.makeBoolStr(checkValue)) {
                                show = true;

                                return true;
                            }
                        }
                    });
                } else {
                    if ($.isArray(checkValue)) {
                        $(checkValue).each(function (i, v) {
                            i = null;

                            if ($.cubewp.makeBoolStr(parentValue) !== $.cubewp.makeBoolStr(v)) {
                                show = true;
                            }
                        });
                    } else {
                        if ($.cubewp.makeBoolStr(parentValue) !== $.cubewp.makeBoolStr(checkValue)) {
                            show = true;
                        }
                    }
                }
                break;

            case '>':
            case 'greater':
            case 'is_larger':
                if (parseFloat(parentValue) > parseFloat(checkValue)) {
                    show = true;
                }
                break;

            case '>=':
            case 'greater_equal':
            case 'is_larger_equal':
                if (parseFloat(parentValue) >= parseFloat(checkValue)) {
                    show = true;
                }
                break;

            case '<':
            case 'less':
            case 'is_smaller':
                if (parseFloat(parentValue) < parseFloat(checkValue)) {
                    show = true;
                }
                break;

            case '<=':
            case 'less_equal':
            case 'is_smaller_equal':
                if (parseFloat(parentValue) <= parseFloat(checkValue)) {
                    show = true;
                }
                break;

            case 'contains':
                if ($.isPlainObject(parentValue)) {
                    parentValue = Object.keys(parentValue).map(function (key) {
                        return [key, parentValue[key]];
                    });
                }

                if ($.isPlainObject(checkValue)) {
                    checkValue = Object.keys(checkValue).map(function (key) {
                        return [key, checkValue[key]];
                    });
                }

                if ($.isArray(checkValue)) {
                    $(checkValue).each(function (idx, val) {
                        var breakMe = false;
                        var toFind = val[0];
                        var findVal = val[1];

                        idx = null;

                        $(parentValue).each(function (i, v) {
                            var toMatch = v[0];
                            var matchVal = v[1];

                            i = null;

                            if (toFind === toMatch) {
                                if (findVal === matchVal) {
                                    show = true;
                                    breakMe = true;

                                    return false;
                                }
                            }
                        });

                        if (true === breakMe) {
                            return false;
                        }
                    });
                } else {
                    if (parentValue.toString().indexOf(checkValue) !== -1) {
                        show = true;
                    }
                }
                break;

            case 'doesnt_contain':
            case 'not_contain':
                if ($.isPlainObject(parentValue)) {
                    arr = Object.keys(parentValue).map(function (key) {
                        return parentValue[key];
                    });

                    parentValue = arr;
                }

                if ($.isPlainObject(checkValue)) {
                    arr = Object.keys(checkValue).map(function (key) {
                        return checkValue[key];
                    });

                    checkValue = arr;
                }

                if ($.isArray(checkValue)) {
                    $(checkValue).each(function (idx, val) {
                        idx = null;

                        if (parentValue.toString().indexOf(val) === -1) {
                            show = true;
                        }
                    });
                } else {
                    if (parentValue.toString().indexOf(checkValue) === -1) {
                        show = true;
                    }
                }
                break;

            case 'is_empty_or':
                if ('' === parentValue || checkValue === parentValue) {
                    show = true;
                }
                break;

            case 'not_empty_and':
                if ('' !== parentValue && checkValue !== parentValue) {
                    show = true;
                }
                break;

            case 'is_empty':
            case 'empty':
            case '!isset':
                if (!parentValue || '' === parentValue || null === parentValue) {
                    show = true;
                }
                break;

            case 'not_empty':
            case '!empty':
            case 'isset':
                if (parentValue && '' !== parentValue && null !== parentValue) {
                    show = true;
                }
                break;
        }

        return show;
    };

    $.cubewp.makeBoolStr = function (val) {
        if ('false' === val || false === val || '0' === val || 0 === val || null === val || '' === val) {
            return 'false';
        } else if ('true' === val || true === val || '1' === val || 1 === val) {
            return 'true';
        } else {
            return val;
        }
    };
})(jQuery);