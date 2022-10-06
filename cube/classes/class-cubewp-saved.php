<?php
/**
 * CubeWp saved will handle all save/bookmark post methods.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Saved
 */
class CubeWp_Saved{
    public function __construct() {
        add_shortcode('cwpSaved', array($this, 'cubewp_saved_post_shortcode'));
        add_action('wp_ajax_cubewp_save_post', array($this, 'cubewp_save_post'));
        add_action('wp_ajax_nopriv_cubewp_save_post', array($this, 'cubewp_save_post'));
        add_action('wp_ajax_cubewp_remove_saved_posts', array($this, 'cubewp_remove_saved_posts'));
        add_action('wp_ajax_nopriv_cubewp_remove_saved_posts', array($this, 'cubewp_remove_saved_posts'));

    }    
    /**
     * Method cubewp_saved_post_cookies
     *
     * @return array
     * @since  1.0.0
     */
    public static function cubewp_saved_post_cookies(){
        // Load current favourite posts from cookie
        $savePosts = (isset($_COOKIE['CWP_Saved'])) ? explode(',', (string) sanitize_text_field( $_COOKIE['CWP_Saved'] )) : array();
        $savePosts = array_map('absint', $savePosts); // Clean cookie input, it's user input!
        return $savePosts;
    }
        
    /**
     * Method cubewp_save_post
     *
     * @return array Json to ajax
     * @since  1.0.0
     */
    public static function cubewp_save_post(){
        $post_id = isset($_POST['post-id']) ? sanitize_text_field($_POST['post-id']) : 0;
        if( isset($post_id) && $post_id > 0 ){
            $savePosts = self::cubewp_saved_post_cookies();
            
            if(is_user_logged_in()){
                $uid = get_current_user_id();
                $savedListing = get_user_meta($uid, 'cwp_save_user_post', true);
                if(!empty($savedListing)){
                    $savedListing = get_user_meta($uid, 'cwp_save_user_post', true);
                }else{
                    $savedListing = array();
                }
                $savedListing[]=$post_id;
                update_user_meta($uid, 'cwp_save_user_post', array_unique($savedListing));
            }else{
                $savePosts[]=$post_id;
                $time_to_live = 3600 * 24 * 30; // 30 days
                setcookie('CWP_Saved', implode(',', array_unique($savePosts)), time() + $time_to_live ,"/");
            }

            wp_send_json(
                array(
                    'type'        =>  'success',
                    'msg'         =>  sprintf(__('Success! Your %s has been saved.', 'cubewp-framework'), get_post_type($post_id)),
                    'text'        =>  sprintf(__('Saved', 'cubewp-framework'), get_post_type($post_id)),
                )
            );
        }
    }
        
    /**
     * Method cubewp_remove_saved_posts
     *
     * @return array Json to ajax
     * @since  1.0.0
     */
    public static function cubewp_remove_saved_posts(){
        $post_id = isset($_POST['post-id']) ? sanitize_text_field($_POST['post-id']) : 0;
        if( isset($post_id) && $post_id > 0 ){
            $savePosts = self::cubewp_saved_post_cookies();

            if(is_user_logged_in()){
                $uid = get_current_user_id();
                $savedinMeta = get_user_meta($uid, 'cwp_save_user_post', true);
                
                if(!empty($savedinMeta)){
                    foreach($savedinMeta as $index => $value){
                        if($value == $post_id){
                            unset($savedinMeta[$index]);
                        }
                    }
                }
                update_user_meta($uid, 'cwp_save_user_post', $savedinMeta);
            }
            // Add (or remove) favourite post IDs
            else{
                foreach($savePosts as $index => $value){
                    if($value == $post_id)
                    {
                        unset($savePosts[$index]);
                    }
                }
                $time_to_live = 3600 * 24 * 30; // 30 days
                setcookie('CWP_Saved', implode(',', array_unique($savePosts)), time() + $time_to_live ,"/");
            }

            wp_send_json(
                array(
                    'type'        =>  'success',
                    'msg'         =>  sprintf(__('Success! Your %s has been removed from saved posts.', 'cubewp-framework'), get_post_type($post_id)),
                    'text'        =>  sprintf(__('Save', 'cubewp-framework'), get_post_type($post_id)),
                )
            );
        }

    }    
    /**
     * Method is_cubewp_post_saved
     *
     * @param int $postid [explicite description]
     * @param bool $onlyicon=true $onlyicon 
     * @param bool $class=true $class
     *
     * @return string
     * @since  1.0.0
     */
    public static function is_cubewp_post_saved($postid,$onlyicon=true,$class=true){
        if(is_user_logged_in()){
            $uid = get_current_user_id();
            $savePosts = get_user_meta($uid, 'cwp_save_user_post', true);
            if( !is_array( $savePosts ) )
            {
                $savePosts   =   (array) $savePosts;
            }
        }else{
            $savePosts = self::cubewp_saved_post_cookies();
        }
        if($onlyicon == true){
            if (in_array($postid,$savePosts )) {
                return 'fa-bookmark';
            }else{
                return 'fa-bookmark-o';
            }
        }elseif($class == true){
            if (in_array($postid,$savePosts )) {
                return 'cwp-saved-post';
            }else{
                return 'cwp-save-post';
            }
        }else{
            if (in_array($postid,$savePosts )) {
                return esc_html__('Saved', 'cubewp-framework');
            }else{
                return esc_html__('Save', 'cubewp-framework');
            }
        }

    }
        
    /**
     * Method cubewp_get_saved_posts
     *
     * @return array
     * @since  1.0.0
     */
    public static function cubewp_get_saved_posts(){
        $favPPosts = array();
        if(is_user_logged_in()){
            $uid = get_current_user_id();
            $savePosts = get_user_meta($uid, 'cwp_save_user_post', true);
            if(!empty($savePosts)){
                foreach($savePosts as $spost){
                    if ( FALSE === get_post_status( $spost ) ) {
                    }else{
                        $favPPosts[] = $spost;
                    }
                }
                update_user_meta($uid, 'cwp_save_user_post', $favPPosts);
                $savePosts = $favPPosts;
            }
        }else{
            $savePosts = self::cubewp_saved_post_cookies();

            if(!empty($savePosts)){
                foreach($savePosts as $spost){
                    if ( FALSE === get_post_status( $spost ) ) {
                    }else{
                        $favPPosts[] = $spost;
                    }
                }

                $savePosts = $favPPosts;

            }

        }
        return $savePosts;
    }
        
    /**
     * Method cubewp_saved_post_shortcode
     *
     * @return string html
     * @since  1.0.0
     */
    public static function cubewp_saved_post_shortcode(){
        wp_enqueue_style('cwp-posts-shortcode');
        $savedPosts = self::cubewp_get_saved_posts();
        if(isset($savedPosts) && !empty($savedPosts)){
            $args = array(
                'post_type'	      => get_post_types(),
                'posts_per_page'  => -1,
                'post_status'     => 'publish',
                'post__in'        => $savedPosts,
            );

            $the_query = new WP_Query($args);
            $grid_view_html = '';
            if($the_query->have_posts()){
                if($the_query->have_posts()){
                    ob_start();
                        ?>
                        <div class="cwp-container">
                            <div class="cwp-row">
                                <?php
                                while($the_query->have_posts()): $the_query->the_post();
                                    $post_id=get_the_ID();
                                    echo CubeWp_frontend_grid_HTML($post_id, $col_class = 'cwp-col-12 cwp-col-md-6');
                                endwhile;
                                ?>
                            </div>
                        </div>
                        <?php
                        $grid_view_html = ob_get_contents();
                    ob_end_clean();
                }
                wp_reset_query();
            }
            return $grid_view_html;
        }
    }
        
    /**
     * Method init
     *
     * @return void
     */
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}