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
        <div class="cwp-alert cwp-js-alert">
            <h6 class="cwp-alert-heading"></h6>
            <div class="cwp-alert-content"></div>
            <button type="button" class="cwp-alert-close">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                </svg>
            </button>
        </div>
        <?php
    }
    
    public static function cubewp_post_views($post_id) {
        // Adding Post Views
        if (session_status() === PHP_SESSION_NONE && ! headers_sent()) {
            session_start();
        }
        if ( ! isset($_SESSION['cubewp_added_post_view'][$post_id])) {
            $post_views_count = get_post_meta($post_id, "cubewp_post_views", true);
            $post_views_count = ! empty($post_views_count) ? $post_views_count : 0;
            $post_views_count += 1;
            if ( isset($_SESSION['cubewp_added_post_view']) && ! is_array($_SESSION['cubewp_added_post_view'])) {
                $_SESSION['cubewp_added_post_view'] = array();
            }
            $_SESSION['cubewp_added_post_view'][$post_id] = true;
            update_post_meta($post_id, "cubewp_post_views", $post_views_count);
        }
    }

    public function cubewp_post_confirmation( $post_id = 0 ){
        global $cwpOptions;

        self::cubewp_post_views($post_id);
        // Post Action Bar For Post Author
	    $user_id = get_current_user_id();
        $author_id = get_post_field ('post_author', $post_id);
        if ($user_id == $author_id) {
            $post_type = get_post_type($post_id);
	        $submit_edit_page = isset($cwpOptions['submit_edit_page'][$post_type]) ? $cwpOptions['submit_edit_page'][$post_type] : '';
	        $post_admin_approved  =  isset($cwpOptions['post_admin_approved'][ $post_type ]) ? $cwpOptions['post_admin_approved'][ $post_type ] : 'pending';
	        $paid_submission      =  isset($cwpOptions['paid_submission']) ? $cwpOptions['paid_submission'] : '';
	        $postStatus           =  get_post_status($post_id);
	        $plan_id              =  get_post_meta($post_id, 'plan_id', true);
	        $plan_price           =  get_post_meta($plan_id, 'plan_price', true);
	        $payment_status       =  get_post_meta($post_id, 'payment_status', true);
	        if( $payment_status == 'pending' && $paid_submission == 'yes' && $plan_price > 0 ){
		        $payment_status = apply_filters('cubewp_check_post_payment_status', '', $plan_id, $post_id);
	        }
            if(!empty($submit_edit_page) || $payment_status == 'pending' || ($post_admin_approved != 'pending' && $postStatus == 'pending')){
                ?>
                <div class="cubewp-post-author-actions">
                <?php if (!empty($submit_edit_page)) { ?>
                    <a href="<?php echo esc_url(add_query_arg(array('pid' => $post_id), get_permalink($submit_edit_page))); ?>">
                        <button class="cube-post-edit-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"  viewBox="0 0 16 16">
                              <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                            </svg>
                            <?php echo sprintf(esc_html__('Edit this %s', 'cubewp-framework'), get_post_type_object( $post_type )->labels->singular_name) ?>
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
    }

    public function cubewp_single_page_notification( $post_id = 0 ){
        $postStatus = get_post_status($post_id);
        $authorID   = get_post_field( 'post_author', $post_id );
        
        if( $postStatus == "pending" && is_user_logged_in() && is_single() && get_current_user_id() == $authorID){
            ?>
            <script>
                jQuery(window).load(function(){
                    cwp_notification_ui('info', '<?php echo wp_kses_post($this->cubewp_get_notification_msg()); ?>');
                });
            </script>
            <?php
        }
    }
    
    public function cubewp_get_notification_msg(){
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
            return sprintf(__('Your %s is pending for review.', 'cubewp-framework'), get_post_type_object( get_post_type($post_id) )->labels->singular_name);
        }else{
            return sprintf(__('Your %s is pending! Please proceed to make it published', 'cubewp-framework'), get_post_type_object( get_post_type($post_id) )->labels->singular_name);
        }
        
    }
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}