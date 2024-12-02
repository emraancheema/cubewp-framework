jQuery(document).ready(function () {
    jQuery(document).on("click", ".cwp-alert .cwp-alert-close", function () {
        var $this = jQuery(this),
            $parent = $this.closest('.cwp-alert');
        $parent.slideUp(200, function () {
            if ($parent.hasClass("cwp-js-alert")) {
                $parent.hide();
            }else {
                $parent.remove();
            }
        });
    });

    jQuery(document).on('click', '.cubewp-modal-trigger', function (event) {
        event.preventDefault();
        var $this = jQuery(this),
            target = jQuery($this.attr('data-cubewp-modal'));
        if (target.length > 0) {
            target.addClass('shown').fadeIn();
        }
    });
    jQuery(document).on('click', '.cubewp-modal-close', function (event) {
        event.preventDefault();
        var $this = jQuery(this),
            target = $this.closest('.cubewp-modal');
        target.removeClass('shown').fadeOut();
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

function cwp_notification_ui(notification_type, notification_content) {
    var $cwp_alert = jQuery(".cwp-alert.cwp-js-alert"),
        $alert_class = '',
        $cwp_alert_content = $cwp_alert.find('.cwp-alert-content');
        
    if ($cwp_alert.is(":visible") && $cwp_alert_content.html() === notification_content) {
        return false;
    }
    if ($cwp_alert.is(":visible")) {
        $cwp_alert.find('.cwp-alert-close').trigger("click");
    }
    if ( notification_type === 'success' ) {
        $alert_class = 'cwp-alert-success';
    } else if ( notification_type === 'warning' ) {
        $alert_class = 'cwp-alert-warning';
    } else if ( notification_type === 'info' ) {
        $alert_class = 'cwp-alert-info';
    } else if ( notification_type === 'error' ) {
        $alert_class = 'cwp-alert-danger';
    }
    $cwp_alert.removeClass("cwp-alert-danger cwp-alert-success cwp-alert-warning cwp-alert-info").addClass($alert_class);
    $cwp_alert.find('.cwp-alert-heading').text(notification_type + "!");
    $cwp_alert_content.html(notification_content);
    $cwp_alert.slideDown();
    setTimeout(function () {
        $cwp_alert.find('.cwp-alert-close').trigger("click");
    }, 3000);
}

jQuery(document).on('click', '.cwp-post-confirmation-wrap .cwp-confirmation-bottom-bar', function (e) {
    jQuery('.cwp-post-confirmation').slideToggle(700);
});
jQuery(document).on('click', '.cwp-post-confirmation-wrap .cwp-confirmation-bottom-bar', function (e) {
    jQuery('.cwp-post-confirmation').slideToggle(700);
});
jQuery(document).on('click', '.cwp-save-post', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    thisObj.addClass('cubewp-active-ajax');
    jQuery.ajax({
        url: cwp_alert_ui_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_save_post&post-id='+ pid + '&nonce=' + cwp_alert_ui_params.nonce,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if( typeof response.text != 'undefined' && response.text != '' ){
                thisObj.addClass('cwp-saved-post');
                thisObj.removeClass('cwp-save-post');
                thisObj.find('.cwp-saved-text').html(response.text);
                thisObj.removeClass('cubewp-active-ajax');
            }
        }
    });
});
jQuery(document).on('click', '.cwp-saved-post', function (e) {
    var thisObj = jQuery(this);
    var pid = thisObj.data('pid');
    var action = thisObj.data('action');
    thisObj.addClass('cubewp-active-ajax');
    jQuery.ajax({
        url: cwp_alert_ui_params.ajax_url,
        type: 'POST',
        data : 'action=cubewp_remove_saved_posts&post-id='+ pid + '&nonce=' + cwp_alert_ui_params.nonce,
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
                thisObj.removeClass('cubewp-active-ajax');
            }
        }
    });
});