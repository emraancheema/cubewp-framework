(function ($) {
    'use strict';
    $.cubewp_form_builder = $.cubewp_form_builder || {};

     cwpform_disable_fields();
     if($('.cwp-search-filters').length <= 0 ){
         cwpform_sortable_sections();
     }
    cwpform_sortable_fields();

    cubewp_show_hide_no_section();

    $(document).on('click', '.cubewp-builder-group-widget-decrease-size', function (e) {
        $.cubewp_form_builder.field_size(this, e, 'min');
    });

    $(document).on('click', '.cubewp-builder-group-widget-increase-size', function (e) {
        $.cubewp_form_builder.field_size(this, e, 'max');
    });

    $(document).on(
        'change',
        '.cubewp-tab-switcher',
        function () {
            $.cubewp_form_builder.switch_tab(this);
        }
    );

    $(document).on(
        'click',
        '.cubewp-trigger-add-section',
        function () {
            jQuery('.cubewp-type-container.active-tab .cwpform-add-section').first().trigger('click');
        }
    );

    $(document).on(
        'click',
        '.cwpform-builder .cwpform-add-section',
        function () {

            $.cubewp_form_builder.add_section(this);
        }
    );

    $(document).on(
        'click',
        '.cwpform-builder .cubewp-builder-section-action-edit',
        function () {
            $.cubewp_form_builder.edit_section(this);
        }
    );

    $(document).on(
        'click',
        '.cwpform-builder .cubewp-builder-section-action-delete',
        function () {
            $.cubewp_form_builder.remove_section(this);
        }
    );

    $(document).on(
        'click',
        '.cwpform-save-section',
        function () {
            $.cubewp_form_builder.save_section(this);
        }
    );

    $(document).on(
        'click',
        '.cwpform-cancel-section',
        function () {
            $.cubewp_form_builder.cancel_section(this);
        }
    );

     $(document).on(
        'click',
        '.cwpform-builder .cubewp-builder-group-widget-actions .cubewp-builder-group-widget-delete',
        function () {
            $.cubewp_form_builder.remove_field(this);
        }
    );
    
    $(document).on(
        'click',
        '.cwpform-builder .cubewp-expand-trigger',
        function () {
            $.cubewp_form_builder.expande_fields(this);
        }
    );
    $(document).on(
        'click',
        '.cwpform-builder .cubewp-builder-group-widget-row-wrapper, .cubewp-builder-section-header',
        function (event) {
            var clicked_ele = $(event.target);
            if (clicked_ele.hasClass('cubewp-builder-group-widget-decrease-size') || clicked_ele.hasClass('cubewp-builder-group-widget-increase-size') || clicked_ele.hasClass('cubewp-expand-trigger') || clicked_ele.hasClass('cubewp-builder-group-widget-delete')) {
                return false;
            }
            $.cubewp_form_builder.expande_fields($(this).find('.cubewp-expand-trigger'));
        }
    );
    $(document).on(
        'click',
        '.cwpform-builder .form-settings-form',
        function () {
            $.cubewp_form_builder.form_settings(this);
        }
    );

    $(document).on(
        'click',
        '.cwpform-get-shortcode',
        function () {
            $.cubewp_form_builder.get_shortcode(this);
        }
    );

    $(document).on('click', '.cwpform-shortcode', function (e) {
        $.cubewp_form_builder.copy_shortcode(this);
    });
    
    function cubewp_show_hide_no_section() {
        var builders = jQuery('.cubewp-type-container');
        if (builders.length > 0) {
            builders.each(function (){
                var $this = jQuery(this);
                if ($this.find('.cubewp-builder-section').length === 0) {
                    $this.find('.cubewp-builder-no-section').removeClass('hidden');
                    $this.find('.cubewp-builder-sections').addClass('hidden');
                }else {
                    $this.find('.cubewp-builder-no-section').addClass('hidden');
                    $this.find('.cubewp-builder-sections').removeClass('hidden');
                }
            });
        }
    }

    function cwpform_disable_fields() {
        $('.cwpform-builder .cubewp-type-container').each(function () {
            var thisObj = $(this);
            var tabID = $(this).attr('id');
            var sideObj = $('.sidebar-'+tabID);
            
            if (sideObj.find('.sidebar-plan-tab').length > 0) {
                $('.cwpform-builder .cubewp-plan-tab').each(function () {
                    var thisObj = $(this);
                    var planID = thisObj.attr('id');
                    var planObj = $('#'+planID)
                    planObj.find('.cubewp-builder-group-widget').removeClass('disabled');
                    thisObj.find('.cubewp-builder-area .cubewp-builder-group-widget').each(function () {
                        var field_id = $(this).attr('id');
                        planObj.find('#' + field_id).addClass('disabled');
                    });
                });
            }else{
                sideObj.find('.cubewp-builder-group-widget').removeClass('disabled');
                thisObj.find('.cubewp-builder-area .cubewp-builder-group-widget').each(function () {
                    var field_id = $(this).attr('id');
                    sideObj.find('#' + field_id).addClass('disabled');
                });
            }
        });
    }

    function cwpform_sortable_sections() {
        'use strict';
        var section_sortable = $('.cubewp-builder-sections');
        if (section_sortable.length > 0) {
            section_sortable.sortable({
                handle: ".cubewp-builder-section-mover",
                containment: ".cubewp-builder-area"
            }).disableSelection();
        }
    }

    function cwpform_sortable_fields() {

        'use strict';
        var sidebar_sortable = $('.cubewp-builder-sidebar .cubewp-builder-fields-sortable'),
            builder_sortable = $('.cubewp-builder .cubewp-builder-fields-sortable');

        if (builder_sortable.length > 0) {
            builder_sortable.sortable({
                helper: "clone",
                handle: ".cubewp-builder-group-widget-mover",
                connectWith: ".cubewp-builder .cubewp-builder-fields-sortable",
                placeholder: "cubewp-builder-group-widget-placeholder",
                containment: '.cubewp-builder-container',
                start: function (event, ui) {
                    jQuery(event.currentTarget).find(".cubewp-builder-group-widget").css("max-width", jQuery('.active-tab .cubewp-builder-area').innerWidth());
                    var $sizeList = jQuery('#size-list').clone(),
                        $currentSizeText = ui.item.find(".cubewp-builder-group-widget-size .size").text(),
                        $currentSizeList = jQuery('li[data-text="' + $currentSizeText + '"]', $sizeList);
                    ui.placeholder.addClass($currentSizeList.attr('data-class'));
                },
                receive: function (event, ui) {
                    ui.item.removeClass('close').addClass('open');
                    if (before.length) before.after(clone); else parent.prepend(clone);
                    cwpform_disable_fields();
                }
            });
        }

        if (sidebar_sortable.length > 0) {
            var clone, before, parent;
            sidebar_sortable.sortable({
                helper: "clone",
                handle: ".cubewp-builder-group-widget-mover",
                connectWith: ".cubewp-builder .cubewp-builder-fields-sortable",
                placeholder: "cubewp-builder-group-widget-placeholder",
                start: function (event, ui) {
                    jQuery(ui.item).show();
                    jQuery(event.currentTarget).find(".cubewp-builder-group-widget").css("max-width", jQuery('.active-tab .cubewp-builder-area').innerWidth());
                    clone = jQuery(ui.item).clone();
                    before = jQuery(ui.item).prev();
                    parent = jQuery(ui.item).parent();
                    var $sizeList = jQuery('#size-list').clone(),
                        $currentSizeText = ui.item.find(".cubewp-builder-group-widget-size .size").text(),
                        $currentSizeList = jQuery('li[data-text="' + $currentSizeText + '"]', $sizeList);
                    ui.placeholder.addClass($currentSizeList.attr('data-class'));
                }
            }).disableSelection();
        }

    }

    $.cubewp_form_builder.switch_tab = function (t) {
        var $this = $(t),
            target = $this.find(":selected").attr("data-switcher-target"),
            have_parent_switcher = $this.closest(".cubewp-tab-switcher-target");
        target = $("." + target);
        if ($this.hasClass("cubewp-tab-switcher-have-child")) {
            var child = target.find(".cubewp-tab-switcher");
            if (child.length > 0) {
                child.trigger("change");
                return false;
            }
        }
        $(".cubewp-tab-switcher-target").removeClass("active-tab");
        target.parents(".cubewp-tab-switcher-target").addClass("active-tab");
        target.addClass("active-tab");
        if (have_parent_switcher.length > 0) {
            have_parent_switcher.addClass("active-tab");
        }
        
    }

    $.cubewp_form_builder.field_size = function (t, e, s) {
        e.preventDefault();
        var $sizeList = jQuery('#size-list').clone();
        var $currentItem = $(t).closest('.cubewp-builder-group-widget');
        var $currentSizeText = jQuery('.cubewp-builder-group-widget-size .size', $currentItem).text();
        var $currentSizeList = jQuery('li[data-text="' + $currentSizeText + '"]', $sizeList);

        $currentItem.removeClass($currentSizeList.attr('data-class'));
        if ($currentItem.attr('data-min')) {
            $sizeList.find('[data-class="' + $currentItem.attr('data-min') + '"]').addClass('min').siblings('.min').removeClass('min');
        }
        if (s == 'min') {
            if ($currentSizeList.hasClass(s)) {
                //$currentSizeList=$currentSizeList.siblings('.max');
            } else {
                $currentSizeList = $currentSizeList.prev();
            }
        } else if (s == 'max') {
            if ($currentSizeList.hasClass(s)) {
                //$currentSizeList=$currentSizeList.siblings('.max');
            } else {
                $currentSizeList = $currentSizeList.next();
            }
        }
        $currentItem.addClass($currentSizeList.attr('data-class'));
        $currentItem.find('input[data-name="field_size"]').val($currentSizeList.attr('data-class'));
        jQuery('.cubewp-builder-group-widget-size .size', $currentItem).text($currentSizeList.attr('data-text'));
    };

    $.cubewp_form_builder.add_section = function (t) {
        var form_relation = $(t).siblings('.form-relation').val();
        var form_type = $(t).siblings('.form-type').val();
        $('#section_form #form_relation').val(form_relation);
        $('#section_form #form_type').val(form_type);
        $('#section_form').find('.form-control').each(function () {
            $(this).val('');
        });
        $('#cwp-layout-builder-ovelay').show();
    };

    $.cubewp_form_builder.edit_section = function (t) {
        var form_relation = $(t).closest('.active-tab').find('.form-relation').val();
        var form_type = $(t).closest('.active-tab').find('.form-type').val();
        $('#section_form #form_relation').val(form_relation);
        $('#section_form #form_type').val(form_type);
        jQuery('#section_form').find('.form-control').each(function () {
            var field_id = jQuery(this).attr('id');
            var field_class = field_id.replace(/_/g, '-');
            var name = $(t).closest('.cubewp-builder-section-actions').find('.' + field_class).data('name');
            if (field_id == name) {
                var value = $(t).closest('.cubewp-builder-section-actions').find('.' + field_class).val();
                jQuery(this).val(value);
            }
        });
        if ($('#section_form').find('#section_type').length > 0) {
            var type = $(t).closest('.cwpform-group-settings').find('input[name="section_type"]').val();
            $('#section_type option[value="' + type + '"]').attr("selected", "selected");
        }

        jQuery('#cwp-layout-builder-ovelay').show();
        return false;
    };

    $.cubewp_form_builder.remove_section = function (t) {
        var p = $(t).closest('.cubewp-builder-section');
        p.slideUp(function () {
            $(this).remove();
            cwpform_disable_fields();
            cubewp_show_hide_no_section();
        });
    };

    $.cubewp_form_builder.copy_shortcode = function (t) {
        var $this = $(t),
            temp_text = document.createElement("input");
        if ($this.find('.inner').hasClass('copy-to-clipboard')) {
            temp_text.value = $this.find('.inner').clone().children().remove().end().text();
            document.body.appendChild(temp_text);
            temp_text.select();
            document.execCommand("copy");
            document.body.removeChild(temp_text);
        }
    };

    $.cubewp_form_builder.save_section = function (t) {

        var form = $(t).closest('#section_form'),
            section_id = form.find('#section_id').val(),
            section_title = form.find('#section_title').val(),
            section_type = form.find('#section_type').val(),
            form_relation = form.find('#form_relation').val();

        form.find('#section_title').css('border', '');
        if (section_title === '') {
            form.find('#section_title').css('border', '1px solid #ef5350');
            return false;
        }

        if (section_id === '') {
            $.ajax({
                type: 'POST',
                url: cwp_vars_params.ajax_url,
                data: $('#section_form').serialize() + '&action=cwpform_add_section',
                dataType: 'json',
                success: function (res) {
                    if ($('.cubewp-type-container.active-tab').find('.cubewp-plan-tab').length > 0) {
                        $('#type-' + form_relation).find('.cubewp-plan-tab.active-tab').find('.cubewp-builder-area .cubewp-builder-sections').append(res.section_html);
                    } else if ($('.single-layout-builder').length > 0) {
                        $('#type-' + form_relation).find('.cubewp-single-layout-builder-'+section_type+' .cubewp-builder-sections').append(res.section_html);
                    } else {
                        $('#type-' + form_relation).find('.cubewp-builder-area .cubewp-builder-sections').append(res.section_html);
                    }
                    $('#section_form')[0].reset();
                    $('#cwp-layout-builder-ovelay').hide();
                    cwpform_sortable_fields();
                    cubewp_show_hide_no_section();
                }
            });
        } else {
            $('#group-' + section_id + ' .cubewp-builder-section-header h3').text(section_title);
            jQuery('#section_form').find('.section-form-field .form-control').each(function () {
                var field_id = jQuery(this).attr('id');
                $('#group-' + section_id).find('input[name="' + field_id + '"]').val(jQuery(this).val());
            });
            $('#cwp-layout-builder-ovelay').hide();
        }

    }

    $.cubewp_form_builder.cancel_section = function (t) {
        $('#section_form')[0].reset();
        $('#cwp-layout-builder-ovelay').hide();
        return false;
    };

    $.cubewp_form_builder.expande_fields = function (t) {
        
        var $this = $(t), container = $this.closest('.cubewp-expand-container');
        
        if (container.hasClass('active-expanded')) {
            container.removeClass('active-expanded');
            $this.removeClass('expanded');
        } else {
            container.addClass('active-expanded');
            $this.addClass('expanded');
        }
            
    };

    $.cubewp_form_builder.remove_field = function (t) {
        var p = $(t).closest('.cubewp-builder-group-widget');
        p.slideUp(function () {
            $(this).remove();
            cwpform_disable_fields();
        });
    };
    
    $.cubewp_form_builder.form_settings = function (t) {
        var setting_form = $('.form-settings');
        if (setting_form.length > 0) {
            setting_form.toggle();
        }
    };

    $.cubewp_form_builder.get_shortcode = function (t) {

        var parent = jQuery('.cubewp-type-container.active-tab');
        var form_relation = parent.find('.form-relation').val();
        var form_type = parent.find('.form-type').val();
            
        parent.find('.cubewp-builder-area .cubewp-builder-section').each(function () {
            $(this).find('.cubewp-builder-section-fields').find('.cubewp-builder-group-widget').each(function () {
                var field_meta_key = $(this).find('.field-name').val();                    
                $(this).find('.group-field').each(function () {
                    var field_name = $(this).data('name');

                    if (form_type == 'search_filters' || form_type == 'search_fields') {
                        $(this).attr('name', "cwpform[" + form_relation + "][fields][" + field_meta_key + "][" + field_name + "]");
                    } 
                });
            });
        });
        if (parent.find('.cwpform-settings .form-field').length > 0) {
            parent.find('.cwpform-settings .form-field').each(function () {
                var type = $(this).attr('type');
                
                if (type == 'checkbox') {
                    var field_name = $(this).closest('.cwpform-setting-field').data('name');
                    $(this).attr('name', "cwpform[" + form_relation + "][form][" + field_name + "]");
                } else {
                    var field_name = $(this).data('name');
                    $(this).attr('name', "cwpform[" + form_relation + "][form][" + field_name + "]");
                    
                }
            });
        }
        var shortcode = '';
        if (form_type == 'search_filters') {
            shortcode = '[cwpFilters]';
        } else if (form_type == 'search_fields') {
            shortcode = '[cwpSearch type="' + form_relation + '"]';
        }
        $.ajax({
            type: 'POST',
            url: cwp_vars_params.ajax_url,
            data: parent.find('.cubewp-builder-area').find(':input').serialize() + '&form_relation=' + form_relation + '&form_type=' + form_type + '&action=cwpform_save_shortcode',
            success: function (data) {
                var $class = '';
                if (form_type === 'single_layout' || form_type === 'search_filters') {
                    shortcode = data.message;
                } else {
                    $class = 'copy-to-clipboard';
                    shortcode = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"/></svg>' + shortcode;
                }
                $('.cwpform-shortcode').show().html('<div class="inner ' + $class + '">' + shortcode + '</div>');
            }
        });
    }


})(jQuery);

(function ($) {
    'use strict';
        var onload_tab_switcher = $('.cubewp-tab-switcher-trigger-on-load');

        if (onload_tab_switcher.length > 0) {
            onload_tab_switcher.trigger("change");
        }
})(jQuery);