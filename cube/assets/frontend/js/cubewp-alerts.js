jQuery(document).ready(function () {
    jQuery(document).on("click", ".cwp-alert .cwp-alert-close", function () {
        var $this = jQuery(this),
            $parent = $this.closest('.cwp-alert');
        $parent.slideUp(200, function () {
            if ($parent.hasClass("cwp-js-alert")) {
                $parent.hide();
            } else {
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
    //JQuery For CubeWP Post Slider 
    if (jQuery('.cubewp-post-slider').length > 0) {
        jQuery('.cubewp-post-slider').each(function () {
            var sliderElement = jQuery(this);
            var prevArrowHtml = sliderElement.data('prev-arrow');
            var nextArrowHtml = sliderElement.data('next-arrow');
            var previcon_type = sliderElement.data('prev-icon-type');
            var nexticon_type = sliderElement.data('next-icon-type');
            var slidesToShow = sliderElement.data('slides-to-show');
            var slidesToScroll = sliderElement.data('slides-to-scroll');
            var slidesToShowTablet = sliderElement.data('slides-to-show-tablet');
            var slidesToShowTabletPortrait = sliderElement.data('slides-show-tablet-portrait');
            var slidesToShowMobile = sliderElement.data('slides-to-show-mobile');
            var slidesToScrollTablet = sliderElement.data('slides-to-scroll-tablet');
            var slidesToScrollTabletPortrait = sliderElement.data('slides-scroll-tablet-portrait');
            var slidesToScrollMobile = sliderElement.data('slides-to-scroll-mobile');
            var autoplay = sliderElement.data('autoplay') === true || sliderElement.data('autoplay') === 'true';
            var autoplaySpeed = sliderElement.data('autoplay-speed');
            var Speed = sliderElement.data('speed');
            var infinite = sliderElement.data('infinite') === true || sliderElement.data('infinite') === 'true';
            var variableWidth = sliderElement.data('variable-width') === true || sliderElement.data('variable-width') === 'true';

            if (previcon_type) {
                var prevArrowButton = '<button type="button" class="slick-prev"><i class="' + prevArrowHtml + '"></i></button>';
            } else {
                var prevArrowButton = '<button type="button" class="slick-prev">' + prevArrowHtml + '</button>';
            }
            if (nexticon_type) {
                var nextArrowButton = '<button type="button" class="slick-next"><i class="' + nextArrowHtml + '"></i></button>';
            } else {
                var nextArrowButton = '<button type="button" class="slick-next">sadsads' + nextArrowHtml + '</button>';
            }
            var CustomArrows = sliderElement.data('custom-arrows') === true || sliderElement.data('custom-arrows') === 'true';
            var CustomDots = sliderElement.data('custom-dots') === true || sliderElement.data('custom-dots') === 'true';
            var enableProgressBar = sliderElement.data('enable-progress-bar') === true || sliderElement.data('enable-progress-bar') === 'true';

            sliderElement.slick({
                slidesToShow: slidesToShow,
                slidesToScroll: slidesToScroll,
                autoplay: autoplay,
                autoplaySpeed: autoplaySpeed,
                speed: Speed,
                infinite: infinite,
                variableWidth: variableWidth,
                prevArrow: prevArrowButton,
                nextArrow: nextArrowButton,
                arrows: CustomArrows,
                dots: CustomDots,
                responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: slidesToShowTablet,
                            slidesToScroll: slidesToScrollTablet
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: slidesToShowTabletPortrait,
                            slidesToScroll: slidesToScrollTabletPortrait
                        }
                    },
                    {
                        breakpoint: 481,
                        settings: {
                            slidesToShow: slidesToShowMobile,
                            slidesToScroll: slidesToScrollMobile
                        }
                    }
                ]
            });
            if (enableProgressBar == true) {
                sliderElement.after(
                    '<div class="slick-progress"><div class="slick-progress-bar"></div></div>'
                );
                var totalSlides = sliderElement.slick("getSlick").slideCount;
                sliderElement.on("afterChange", function (event, slick, currentSlide) {
                    var progress = ((currentSlide + 1) / totalSlides) * 100;
                    sliderElement.next('.slick-progress').find('.slick-progress-bar').css("width", progress + "%");
                });
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
    if (notification_type === 'success') {
        $alert_class = 'cwp-alert-success';
    } else if (notification_type === 'warning') {
        $alert_class = 'cwp-alert-warning';
    } else if (notification_type === 'info') {
        $alert_class = 'cwp-alert-info';
    } else if (notification_type === 'error') {
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
        data: 'action=cubewp_save_post&post-id=' + pid + '&nonce=' + cwp_alert_ui_params.nonce,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if (typeof response.text != 'undefined' && response.text != '') {
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
        data: 'action=cubewp_remove_saved_posts&post-id=' + pid + '&nonce=' + cwp_alert_ui_params.nonce,
        dataType: "json",
        success: function (response) {
            cwp_notification_ui(response.type, response.msg);
            if (typeof response.text != 'undefined' && response.text != '') {
                if (action == 'remove') {
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