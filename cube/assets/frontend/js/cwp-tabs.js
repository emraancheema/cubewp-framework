jQuery(document).ready(function () {
    jQuery(document).on('click', '.cwp-tabs li a', function(x) {
        var $this = jQuery(this);
        if ($this.hasClass('cwp-not-tab')) {
            return;
        }
        x.preventDefault();
        if ($this.hasClass('cwp-active-tab')) return false;
        var tabContent = $this.attr('href');
        jQuery('.cwp-tab-content').removeClass('cwp-active-tab-content');
        jQuery('.cwp-tabs li').removeClass('cwp-active-tab');
        jQuery($this).closest('li').addClass('cwp-active-tab');
        jQuery(tabContent).addClass('cwp-active-tab-content');
    });
});