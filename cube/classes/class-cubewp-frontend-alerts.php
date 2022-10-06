<?php
class CubeWp_Frontend_Alerts{
    
    public function __construct() {
        add_action('wp_footer', array($this, 'cubewp_notification_ui'));
        add_action('cubewp_single_page_notification', array($this, 'cubewp_single_page_notification'), 10, 1);
        add_action('cubewp_post_confirmation', array($this, 'cubewp_post_confirmation'), 10, 1);
    }
    
    public function cubewp_notification_ui(){
        wp_enqueue_style('cwp-alerts');
        wp_enqueue_script('cwp-alerts');
        ?>
        <div class="cwp-notification-area cwp-notification-info">
            <div class="cwp-notification-wrap">
                <div class="cwp-notification-icon">
                    <img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAE2SURBVFhH7dhBaoQwFMZxoZu5w5ygPc7AlF6gF5gLtbNpwVVn7LKQMG4b3c9ZCp1E3jdEEI1JnnGRP7h5Iv4wKmiRy+U8qkT7Wkn1VpblA43Yqn7abSWb+luqRxpNZ3D6oP+zUO+cSIPT57jqc/1p4I7G0xmUwXEibdxJ/j7T2D1OZDAOcSD7y9ruaexfTGR0HIqBZMOhECQ7DvkgF8OhOcjFccgFmQyHxpDJcWgIuRoc6iFl87kqHOqunFQfBtltQr3QrnVkLWsHxHLT7rTZ95y5cvflXgNy6IHo3ZNCHZMhx55WQh6TIV1eJcmQLji0OHIODi2G9MEhdmQIDrEhY+BQdGRMHIqG5MChYKSNC/puHSkIqQ+qOXGoh5TqQOPpvi7N06x/JQF1SI0TQmxolMvl3CuKG6LJpCW33jxQAAAAAElFTkSuQmCC">
                </div>
                <div class="cwp-notification-content">
                    <h4></h4>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function cubewp_post_confirmation( $post_id = 0 ){
        global $cwpOptions;
        // Adding Post Views
        $post_views_count = get_post_meta($post_id, "cubewp_post_views", true);
        $post_views_count = ! empty($post_views_count) ? $post_views_count : 0;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ( ! isset($_SESSION['cubewp_added_post_view']) || ! is_array($_SESSION['cubewp_added_post_view'])) {
            $post_views_count += 1;
            if ( ! is_array($_SESSION['cubewp_added_post_view'])) {
                $_SESSION['cubewp_added_post_view'] = array();
            }
            $_SESSION['cubewp_added_post_view'][$post_id] = true;
        }
        update_post_meta($post_id, "cubewp_post_views", $post_views_count);

        // Post Action Bar For Post Author
	    $user_id = get_current_user_id();
        $author_id = get_post_field ('post_author', $post_id);
        if ($user_id == $author_id) {
            $post_type = get_post_type($post_id);
	        $submit_edit_page = isset($cwpOptions['submit_edit_page'][$post_type]) ? $cwpOptions['submit_edit_page'][$post_type] : '';
	        $submit_edit_post     =  isset($cwpOptions['submit_edit_post']) ? $cwpOptions['submit_edit_post'] : '';
	        $post_admin_approved  =  isset($cwpOptions['post_admin_approved']) ? $cwpOptions['post_admin_approved'] : '';
	        $paid_submission      =  isset($cwpOptions['paid_submission']) ? $cwpOptions['paid_submission'] : '';
	        $postStatus           =  get_post_status($post_id);
	        $plan_id              =  get_post_meta($post_id, 'plan_id', true);
	        $plan_price           =  get_post_meta($plan_id, 'plan_price', true);
	        $payment_status       =  get_post_meta($post_id, 'payment_status', true);
	        if( $payment_status == 'pending' && $paid_submission == 'yes' && $plan_price > 0 ){
		        $payment_status = apply_filters('cubewp_check_post_payment_status', '', $plan_id, $post_id);
	        }
            ?>
            <div class="cubewp-post-author-actions">
            <?php if (!empty($submit_edit_page)) { ?>
                <a href="<?php echo esc_url(add_query_arg(array('pid' => $post_id), get_permalink($submit_edit_page))); ?>">
                    <button class="cube-post-edit-btn">
                        <?php esc_html_e("Edit", "cubewp-framework"); ?>
                    </button>
                </a>
            <?php } ?>
                <?php if($payment_status == 'pending') { ?>
                    <button class="cwp-pay-publish-btn" data-pid="<?php echo absint($post_id); ?>">
	                    <?php esc_html_e("Pay & Publish", "cubewp-framework"); ?>
                    </button>
                <?php }else if($post_admin_approved != 'pending' && $postStatus == 'pending'){ ?>
                    <button class="cwp-publish-btn" data-pid="<?php echo absint($post_id); ?>">
                        <?php esc_html_e("Publish", "cubewp-framework"); ?>
                    </button>
                <?php } ?>
            </div>
            <?php
        }
    }

    public function cubewp_single_page_notification( $post_id = 0 ){
        $postStatus = get_post_status($post_id);
        $authorID   = get_post_field( 'post_author', $post_id );
        
        if( $postStatus == "pending" && is_user_logged_in() && is_single() && get_current_user_id() == $authorID){
            ?>
            <script>
                jQuery(window).load(function(){
                    jQuery('.cwp-notification-area').removeClass('cwp-notification-info').removeClass('cwp-notification-error').removeClass('cwp-notification-success').removeClass('cwp-notification-warning');
                    jQuery('.cwp-notification-area').addClass('cwp-notification-info').addClass('active-wrap');
                    jQuery('.cwp-notification-area .cwp-notification-content h4').html('<?php echo wp_kses_post($this->cubewp_get_notification_msg('info')); ?>');
                });
            </script>
            <?php
        }
    }
    
    public function cubewp_get_notification_msg( $notification_type = 'info' ){
        $free_msg =  true;
        if(isset($_GET['p']) && isset($_GET['post_type']) && !is_admin()) {
            $post_id   =   wp_kses_post($_GET['p']);
            $plan_id   =   get_post_meta($post_id, 'plan_id', true);
            if( $plan_id > 0 ) {
                $plan_price =   get_post_meta($plan_id, 'plan_price', true);
                if( $plan_price > 0 ) {
                    $free_msg = false;
                }
            }
        }
        if( $free_msg ){
            return sprintf(__('Your %s is pending for review.', 'listingpro'), get_post_type($post_id));
        }else{
            return sprintf(__('Your %s is pending! Please proceed to make it published', 'cubewp-framework'), get_post_type($post_id));
        }
        
    }
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}