jQuery(document).ready(function ($){
    if (jQuery(".field-map_use-checkbox").length > 0) {
        jQuery(document).on('click', '.field-map_use-checkbox', function (e) {
            jQuery(".field-map_use-checkbox").prop("checked", false);
            jQuery(this).prop("checked", true);
        });
    }
    
    jQuery(document).on('click', '.cubewp-locked-field span', function (e) {
        var $this = jQuery(this),
            parent = $this.closest(".cubewp-locked-field"),
            input = parent.find('*:not(span)');

        if (parent.hasClass("unlocked")){
            parent.removeClass("unlocked");
            input.prop("readonly", 1);
            $this.removeClass("dashicons-unlock").addClass("dashicons-lock");
        }else {
            parent.addClass("unlocked");
            input.prop("readonly", 0);
            $this.removeClass("dashicons-lock").addClass("dashicons-unlock");
        }
    });
  
    cwp_fields_sortable();
    cwp_conditional_fields();
    jQuery('#cwp-add-new-field-btn').click(function (e){
        e.preventDefault();
        var data = {
            action: 'process_add_field',
            nonce: cubewp_custom_fields_params.nonce
        };
        jQuery.post(cubewp_custom_fields_params.url, data, function (response){
            jQuery('#poststuff #post-body .cwp-group-fields').append(response.data);
            cwp_update_custom_field_type();
            cwp_find_group_field();
            cwp_find_sub_fields();
            cwp_fields_sortable();
        });
    });
    
    jQuery('#cwp-add-new-user-field-btn').click(function (e){
        e.preventDefault();
        var data = {
            action: 'cwp_add_user_custom_field',
            nonce: cubewp_custom_fields_params.nonce
        };
        jQuery.post(cubewp_custom_fields_params.url, data, function (response){
            jQuery('#poststuff #post-body .cwp-group-fields').append(response.data);
            cwp_update_custom_field_type();
            cwp_find_group_field();
            cwp_find_sub_fields();
            cwp_fields_sortable();
        });
    });
    
    jQuery(document).on('click', '.add-sub-field', function (e) {
        e.preventDefault();
        var e_this = jQuery(this);
        var parent_field = e_this.data('parent_field');
        var data = {
            action: 'cwp_add_custom_sub_field',
            parent_field: parent_field,
            nonce: cubewp_custom_fields_params.nonce
        };
        jQuery.post(cubewp_custom_fields_params.url, data, function (response){
            e_this.closest('.sub-fields-holder').find('.sub-fields') .append(response.data);
            cwp_update_custom_field_type();
            cwp_find_group_field();
            cwp_fields_sortable();
        });
    });
    
    jQuery(document).on('click', '.add-user-sub-field', function (e) {
        e.preventDefault();
        var e_this = jQuery(this);
        var parent_field = jQuery(this).data('parent_field');
        var data = {
            action: 'cwp_add_user_custom_sub_field',
            parent_field: parent_field,
            nonce: cubewp_custom_fields_params.nonce
        };
        jQuery.post(cubewp_custom_fields_params.url, data, function (response){
            e_this.closest('.sub-fields-holder').find('.sub-fields') .append(response.data);
            cwp_update_custom_field_type();
            cwp_find_group_field();
            cwp_fields_sortable();
        });
    });
    
    jQuery('.cwp-custom-fields-post-types').change(function() {
        
        var selected_post_types = jQuery(".cwp-custom-fields-post-types:checkbox:checked").map(function(){
            return jQuery(this).val();
        }).get().join(',');
        
        var data = {
            action: 'cwp_get_taxonomies_by_post_types',
            post_types: selected_post_types,
            nonce: cubewp_custom_fields_params.nonce
        };
        
        jQuery.ajax({
            type: 'POST',
            url: cubewp_custom_fields_params.url,
            data: data,
            success: function (data) {
                jQuery('.custom-fields-conditional-taxonomies-list').html(data);
            }
        });
        
    });
    
    jQuery(document).on('keyup', '.field-label', function () {
        var val = jQuery(this).val();
        if( val === '' ){
            val = jQuery(this).closest('.cwp-field-set').find('.field-title').data('label');
        }
        jQuery(this).closest('.cwp-field-set').find('.field-title .field-label').text(val);
    });
    
    jQuery(document).on('change', '.field-required-checkbox', function () {
        if(jQuery(this).is(":checked")){
            jQuery(this).parent().parent().next('.validation-msg-row').show(300);
        }else{
            jQuery(this).parent().parent().next('.validation-msg-row').hide(300);
        }
    });
    
    jQuery(document).on('change', '.field-conditional', function () {
        if(jQuery(this).is(":checked")){
            jQuery(this).parent().parent().next('.conditional-rule').show(300);
        }else{
            jQuery(this).parent().parent().next('.conditional-rule').hide(300);
        }
    });
    
    
    cwp_update_custom_field_type();
    cwp_find_group_field();
    cwp_find_sub_fields();
    cwp_field_options_sortable();
    jQuery(document).on('change', 'select.field-type', function () {
        jQuery(this).closest('.cwp-field-set').find('.field-title .field-type').text(jQuery(this).find('option:selected').text());
        cwp_update_custom_field_type(jQuery(this));
        cwp_find_sub_fields();
    });
    
    jQuery(document).on('click', '.field-actions .remove-field', function () {
        jQuery(this).closest('.cwp-field-set').slideUp(500, function() {
            jQuery(this).remove();
            cwp_find_group_field();
        });
    });
    
    jQuery(document).on('click', '.field-actions .edit-field', function () {
        jQuery(this).closest('.cwp-field-set').find('.cwp-collapsible-inner').slideToggle(300);
        if(jQuery(this).closest('.parent-field-header').hasClass('closed')){
            jQuery(this).closest('.parent-field-header').removeClass('closed');
        }else{
            jQuery(this).closest('.parent-field-header').addClass('closed');
        }
    });
    
    jQuery(document).on('click', '.field-actions .edit-sub-field', function () {
        jQuery(this).closest('.cwp-field-set').find('.cwp-sub-field-inner').slideToggle(300);
        if(jQuery(this).closest('.sub-field-header').hasClass('closed')){
            jQuery(this).closest('.sub-field-header').removeClass('closed');
        }else{
            jQuery(this).closest('.sub-field-header').addClass('closed');
        }
    });
    
    jQuery(document).on('change', '.field-options-table .option-label, .field-options-table .option-value', function () {
        if( jQuery(this).val() !== '' ){
            jQuery(this).css('border', '');
        }
    });
    
    jQuery(document).on('keyup', '.field-options-table .option-value', function () {
        jQuery(this).closest('tr.sortable').find('.default-option').val(jQuery(this).val());
    });
    jQuery(document).on('change', '.field-options-table .option-value', function () {
        jQuery(this).closest('tr.sortable').find('.default-option').val(jQuery(this).val());
    });
    
    jQuery(document).on('click', '.field-options-table .add-option', function () {
        var empty_options = false;
        jQuery(this).closest('.field-options-table').find("tr.sortable").each(function(){
            var option_label = jQuery(this).find('.option-label').val();
            var option_value = jQuery(this).find('.option-value').val().trim();
            if( option_label === '' ){
                jQuery(this).find('.option-label').css('border', '1px solid red');
                empty_options = true;
            }
            if( option_value === '' ){
                jQuery(this).find('.option-value').css('border', '1px solid red');
                empty_options = true;
            }
        });
        if( empty_options === false ){
            jQuery('<tr class="sortable">'+ jQuery(this).closest('.field-options-table').find('tr.clone-option').html() +'</tr>').insertAfter(jQuery(this).closest('tr.sortable'));
            cwp_field_options_sortable();
        }
    });
    
    jQuery(document).on('click', 'tr.sortable label', function () {
        jQuery(this).closest('td').find('.default-option').trigger('click');
    });
    
    jQuery(document).on('click', '.field-options-table .remove-option', function () {
        if(jQuery(this).closest('.field-options-table').find('tr.sortable').length > 1 ){
            jQuery(this).closest('tr').remove();
        }else{
            alert('Please choose at least one option');
        }
    });

    var conditional_rule_operator = jQuery(".conditional-rule-operator select");
    if (conditional_rule_operator.length > 0) {
        conditional_rule_operator.on("change", function (){
            var $this = jQuery(this),
                parent = $this.closest("tr"),
                value_field = parent.find(".conditional-rule-value"),
                value = $this.val();
            if (value !== "!empty" && value !== "empty") {
                value_field.show();
            }else {
                value_field.hide();
            }
        });
        conditional_rule_operator.trigger("change");
    }
    
});

function cwp_find_group_field() {
    if(jQuery(".cwp-field-set").length > 0){
        jQuery('.group-fields').show();
    }else{
        jQuery('.group-fields').hide();
    }
}

function cwp_update_custom_field_type(){
    
    jQuery(".conditional-field").each(function(){
        var thisObj               = jQuery(this);
        var equal_fields          = thisObj.data('equal');
        var not_equal_fields      = thisObj.data('not_equal');
        var field_type            = thisObj.closest('.cwp-field-set').find('select.field-type').val();
        if( typeof not_equal_fields !== 'undefined' && not_equal_fields !== '' ){
            var not_equal_fieldsArr = not_equal_fields.split(',');
            if(jQuery.inArray( field_type, not_equal_fieldsArr) !== -1 ){
                thisObj.addClass('hidden');
            }else{
                thisObj.removeClass('hidden');
            }
            
            if( typeof equal_fields !== 'undefined' && equal_fields !== '' ){
                var equal_fieldsArr = equal_fields.split(',');
                if(jQuery.inArray( field_type, equal_fieldsArr) !== -1 ){
                    thisObj.removeClass('hidden');
                }else{
                    if(!thisObj.hasClass('hidden')){
                        thisObj.addClass('hidden');
                    }
                }
            }
        }else{
            if( typeof equal_fields !== 'undefined' && equal_fields !== '' ){
                var equal_fieldsArr = equal_fields.split(',');
                if(jQuery.inArray( field_type, equal_fieldsArr) !== -1 ){
                    thisObj.removeClass('hidden');
                }else{
                    thisObj.addClass('hidden');
                }
            }
        }
    });
    
}

function cwp_find_sub_fields() {
    if(jQuery(".parent-field.cwp-field-set").length > 0){
        jQuery(".parent-field.cwp-field-set").each(function(){
            var field_type = jQuery(this).find('select.field-type').val();
            if( field_type === 'repeating_field' ){
                jQuery(this).find('.sub-fields-holder').removeClass('hidden');
            }else{
                jQuery(this).find('.sub-fields-holder').addClass('hidden');
            }
        });
    }
}

function cwp_fields_sortable(){
    
    jQuery(".cwp-group-fields").sortable({
        items: ".cwp-add-form-feild",
        handle: '.field-order'
    }).disableSelection();
    
    jQuery(".cwp-group-fields .sub-fields").sortable({
        items: ".cwp-add-form-feild",
        handle: '.sub-field-order'
    }).disableSelection();
}

function cwp_field_options_sortable(){
    
    jQuery(".field-options-table tbody").sortable({
        items: "tr.sortable",
        handle: 'td.move-option'
    }).disableSelection();
    
}

function cwp_conditional_fields(){
    var add_custom_fields = jQuery(".cwp-group-fields .parent-field.cwp-add-form-feild");
    if(add_custom_fields.length > 0){
        var conditional_fields = {};
        add_custom_fields.each(function(){
            var thisObj      =   jQuery(this),
                field_label  =  thisObj.find('input.field-label').val(),
                field_name   =  thisObj.find('input.field-name').val(),
                field_type   =  thisObj.find('select.field-type').val();
            if (
                field_type !== 'file' &&
                field_type !== 'image' &&
                field_type !== 'gallery' &&
                field_type !== 'repeating_field' &&
                field_type !== 'wysiwyg_editor' &&
                field_type !== 'google_address'

            ) {
                conditional_fields[field_name] = field_label;
            }
        });
        add_custom_fields.each(function(){
            var thisObj                    =   jQuery(this);
            var field_name                 =  thisObj.find('input.field-name').val();
            var conditional_field          =  thisObj.find('.conditional-rule-field select');
            var selected_conditional_field = conditional_field.data('value');
            var conditional_fields_options = '';
            jQuery.each( conditional_fields, function( key, value ){
                var selected = '';
                if( selected_conditional_field === key ){
                    selected = 'selected';
                }
                if( field_name ===  key ){
                    conditional_fields_options += '<option '+ selected +' disabled value="'+ key +'">'+ value +' (this field)</option>';
                }else{
                    conditional_fields_options += '<option '+ selected +' value="'+ key +'">'+ value +'</option>';
                }
            });
            conditional_field.append(conditional_fields_options);
        });
    }
}