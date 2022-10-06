jQuery(document).ready(function () {
    jQuery(document).on("click", ".cwp-alert .cwp-alert-close", function () {
        jQuery(this).closest('.cwp-alert').slideUp(200, function () {
            jQuery(this).remove();
        });
    });

    var view_all_child_terms = jQuery('.cwp-taxonomy-term-child-terms-see-more');
    if (view_all_child_terms.length > 0) {
        view_all_child_terms.on('click', function (e) {
            e.preventDefault();
            var $this = jQuery(this),
                more = $this.attr('data-more'),
                less = $this.attr('data-less'),
                all_child_terms = $this.closest('.cwp-taxonomy-term-child-terms').find('.cwp-taxonomy-term-child-terms-more');
            if ($this.hasClass('cwp-viewing-less')) {
                $this.text(more);
                $this.removeClass('cwp-viewing-less');
                all_child_terms.slideUp('hide');
            } else {
                $this.text(less);
                $this.addClass('cwp-viewing-less');
                all_child_terms.slideDown('show');
            }
        });
    }
});

function cwp_alert_ui(alert_type, alert_content) {
    var alert_class = 'cwp-alert-danger';
    if (alert_type == 'success') {
        alert_class = 'cwp-alert-success';
    } else if (alert_type == 'warning') {
        alert_class = 'cwp-alert-warning';
    } else if (alert_type == 'primary') {
        alert_class = 'cwp-alert-primary';
    }
    var alert_ui = '<div class="cwp-alert ' + alert_class + '">\
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">\
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>\
        </svg>\
        <div>\
            ' + alert_content + '\
        </div>\
        <button type="button" class="cwp-alert-close">\
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">\
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>\
            </svg>\
        </button>\
    </div>';

    return alert_ui;
}

function cwp_notification_ui(notification_type, notification_content) {
    jQuery('.cwp-notification-area').removeClass('cwp-notification-info').removeClass('cwp-notification-error').removeClass('cwp-notification-success').removeClass('cwp-notification-warning');
    jQuery('.cwp-notification-area').addClass('cwp-notification-' + notification_type).addClass('active-wrap');
    jQuery('.cwp-notification-area .cwp-notification-content h4').html(notification_content);
    setTimeout(function () {
        jQuery(".cwp-notification-area.active-wrap .cwp-notification-icon").trigger("click");
    }, 3000);
}

jQuery(document).on('click', '.cwp-notification-area .cwp-notification-icon', function (e) {
    jQuery('.cwp-notification-area').removeClass('cwp-notification-info').removeClass('cwp-notification-error').removeClass('cwp-notification-success').removeClass('cwp-notification-warning').removeClass('active-wrap');
    jQuery('.cwp-notification-area .cwp-notification-content h4').html('');
});

jQuery(document).on('click', '.cwp-post-confirmation-wrap .cwp-confirmation-bottom-bar', function (e) {
    jQuery('.cwp-post-confirmation').slideToggle(700);
});
jQuery(document).on('click', '.cwp-post-confirmation-wrap .cwp-confirmation-bottom-bar', function (e) {
    jQuery('.cwp-post-confirmation').slideToggle(700);
});
jQuery(document).on('click', '.cwp-save-post', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    jQuery.ajax({
        url: cwp_alert_ui_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_save_post&post-id='+ pid,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if( typeof response.text != 'undefined' && response.text != '' ){
                thisObj.addClass('cwp-saved-post');
                thisObj.removeClass('cwp-save-post');
                thisObj.find('.cwp-saved-text').html(response.text);
            }
        }
    });
});
jQuery(document).on('click', '.cwp-saved-post', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    var action = thisObj.data('action');
    jQuery.ajax({
        url: cwp_alert_ui_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_remove_saved_posts&post-id='+ pid,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if( typeof response.text != 'undefined' && response.text != '' ){
                if(action == 'remove'){
                    thisObj.closest('tr').remove();
                }
                thisObj.addClass('cwp-save-post');
                thisObj.removeClass('cwp-saved-post');
                thisObj.find('.cwp-saved-text').html(response.text);
            }
        }
    });
});