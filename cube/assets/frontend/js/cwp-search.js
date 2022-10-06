jQuery(document).ready(function () {
    
    if (jQuery(".cwp-submit-search").length > 0) {
        jQuery(document).on("click", ".cwp-submit-search", function () {
            jQuery(this).addClass('cubewp-processing-ajax');
        });
    }
    
    if(jQuery(".cwp-search-field-checkbox").length > 0 ){
        jQuery(document).on("change", '.cwp-search-field-checkbox input[type="checkbox"]', function() {

            var hidden_checkbox = jQuery(this).closest('.cwp-search-field-checkbox').find('input[type="hidden"]');
            var hidden_vals = hidden_checkbox.val();
            if(jQuery(this).is(':checked')){
                if( hidden_vals == '' ){
                    hidden_vals = jQuery(this).val();
                }else{
                    hidden_vals += ','+ jQuery(this).val();
                }
                jQuery(this).prop('checked', true);
            }else{
                jQuery(this).prop('checked', false);
                hidden_vals = cwp_remove_string_value(hidden_vals, jQuery(this).val() );
            }
            hidden_checkbox.val(hidden_vals);
        });
    }
    
    if(jQuery(".cwp-search-field select").length > 0 ){
        jQuery(document).on("change", '.cwp-search-field select', function() {
            if(jQuery(this).hasClass('multi-select')){
                var value = jQuery(this).val();
                if( value != '' ){
                    value.join(',');
                }
                jQuery(this).closest('.cwp-search-field-dropdown').find('input[type="hidden"]').val(value);
            }

        });
    }

    if(jQuery(".cubewp-date-range-picker").length > 0 ) {
        jQuery('.cubewp-date-range-picker').each(function () {
            var $this = jQuery(this),
                from = $this.find(".cubewp-date-range-picker-from")
                    .datepicker({
                        dateFormat: "mm/dd/yy",
                        defaultDate: "+1w", changeMonth: true, numberOfMonths: 1
                    })
                    .on("change", function () {
                        to.datepicker("option", "minDate", getDate(this));
                        $this.find('.cubewp-date-range-picker-input').val(getDateRange(from, to)).trigger('input');
                    }),
                to = $this.find(".cubewp-date-range-picker-to").datepicker({
                    dateFormat: "mm/dd/yy",
                    defaultDate: "+1w", changeMonth: true, numberOfMonths: 1
                })
                    .on("change", function () {
                        from.datepicker("option", "maxDate", getDate(this));
                        $this.find('.cubewp-date-range-picker-input').val(getDateRange(from, to)).trigger('input');
                    });

        });
    }
});
function getDateRange(from, to, separator = '-') {
    var from_val = from.val(),
        to_val   = to.val();

    if (from_val === '' && to_val === '') return '';
    return from_val + separator + to_val;
}

function getDate(element) {
    var date;
    try {
        date = jQuery.datepicker.parseDate("mm/dd/yy", element.value);
    } catch (error) {
        date = null;
    }

    return date;
}