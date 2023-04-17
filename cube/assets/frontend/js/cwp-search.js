jQuery(document).ready(function () {
    
    if (jQuery(".cwp-submit-search").length > 0) {
        jQuery(document).on("click", ".cwp-submit-search", function () {
            jQuery(this).addClass('cubewp-processing-ajax');
        });
    }

    jQuery(document).on("change", '.cwp-search-form input[type="radio"]', function() {
        jQuery(this).closest('.cwp-field-radio-container').find('input[type="radio"]').prop('checked', false);
        jQuery(this).prop('checked', true);
        var hidden_radio = jQuery(this).closest('.cwp-radio-container').find('input[type="hidden"]');
        var hidden_vals = jQuery(this).val();
        hidden_radio.val(hidden_vals);
    });
    
    // if(jQuery(".cwp-field-container input[name=s]").length > 0 ){
    //     jQuery(document).on("keyup", '.cwp-field-container input[name=s]', function() {
    //         let thisobj = jQuery(this),
    //         val = thisobj.val(),
    //         term_array = [],
    //         maindiv = '<ul class="cwp-search-dropdown-fields">';
    //         type = thisobj.closest('form').find('input[name=post_type]').val();
    //         if(val.length < 3){
    //             return;
    //         }
    //         if(!jQuery('.cwp-search-dropdown-fields').length){
    //             thisobj.closest('.cwp-field-container').append(maindiv);
    //         }
    //         fetch('/cube/wp-json/wp/v2/'+type+'/?search='+val).then((response) =>{
    //             return response.json();
    //         }).then((posts) =>{
    //             let output = "";
    //             posts.map((values) =>{
    //                 values.taxonomies.map((tax) =>{
    //                     term_array.push(tax);
    //                 })                    
    //             })
    //             var terms = term_array.filter((v, i, a) => a.indexOf(v) === i);
    //             terms.map((unq) =>{
    //                 output += `<li><a href="#">${val}</a> In <a href="#">${unq}</a></li>`;
    //             })
    //             thisobj.closest('.cwp-field-container').find('.cwp-search-dropdown-fields').html(output);
    //         }).catch((error) =>{
    //             console.log(error);
    //         })
    //     });
    // }
    
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