<?php
/**
 * display fields of custom fields.
 *
 * @version 1.0
 * @package cubewp/cube/modules/search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Search_Ajax_Hooks
 */
class CubeWp_Search_Ajax_Hooks{
    
    private static $terms = null;
    private static $map_meta_key = null;
        
    /**
     * Method cwp_search_filters_ajax_content
     *
     * @return array json to ajax
     * @since  1.0.0
     */
    public static function cwp_search_filters_ajax_content(){
        $latLng = array();
        $post_data = CubeWp_Sanitize_text_Array($_POST);
        
        $post_type = isset($post_data['post_type']) ? $post_data['post_type'] : '';
        $_DATA = apply_filters('cubewp/search/query/update',$post_data,sanitize_text_field($post_type));
        
        $page_num     =  isset($_DATA['page_num']) ? $_DATA['page_num'] : 1;
        $post_type    =  isset($_DATA['post_type']) ? $_DATA['post_type'] : '';

        self::$map_meta_key = self::cwp_map_meta_key($post_type);
        $query = new CubeWp_Query($_DATA);
        $the_query = $query->cubewp_post_query();
        
        $grid_view_html = '';
        if($the_query->have_posts()){
            ob_start();
                $data_args = array(
                    'total_posts'    => $the_query->found_posts, 
                    'terms' => self::$terms, 
                );
                $data = apply_filters('cubewp_frontend_search_data', '', $data_args);
                echo apply_filters('cubewp/frontend/before/search/loop', '');
                ?>
                <div class="cwp-grids-container cwp-row grid-view">
                    <?php
                    while($the_query->have_posts()): $the_query->the_post();
                        if(!empty(self::cwp_map_lat_lng(get_the_ID()))){
                            $latLng[] = self::cwp_map_lat_lng(get_the_ID());
                        }
                        echo apply_filters('cubewp/frontend/loop/grid/html', CubeWp_frontend_grid_HTML(get_the_ID()));
                    endwhile;
                    ?>
                </div>
                <?php
                $pagination_args = array(
                    'total_posts'    => $the_query->found_posts, 
                    'posts_per_page' => 10, 
                    'page_num'       => $page_num
                );
                echo apply_filters('cubewp_frontend_posts_pagination', '', $pagination_args);
                echo apply_filters('cubewp/frontend/after/search/loop', '');
                $grid_view_html = ob_get_contents();
            ob_end_clean();
        }else{
            $grid_view_html = self::cwp_no_result_found();
        }
        wp_reset_query();
        if(empty($latLng)) $latLng = '';
        if(empty($data)) $data = '';
        
        wp_send_json( array( 'post_data_details' => $data, 'map_cordinates' =>  $latLng, 'grid_view_html' => $grid_view_html ) );
        
    }
    
    private static function cwp_map_lat_lng($postid=''){
        $Map=array();
        if(self::$map_meta_key && !empty(self::$map_meta_key) && !empty($postid)){
            $Lat = get_post_meta($postid, self::$map_meta_key.'_lat', true);
            $Lng = get_post_meta($postid, self::$map_meta_key.'_lng', true);
            if(!empty($Lat) && !empty($Lng)){
                $Map[0] = $Lat;
                $Map[1] = $Lng;
                $Map[2] = get_the_title($postid);
                $Map[3] = get_the_permalink($postid);
                $Map[4] = cubewp_get_post_thumbnail_url($postid);
                return $Map;
            }
        }
    }
    
    private static function cwp_map_meta_key($post_type=''){
        if(empty($post_type)) return;
        $options = CWP()->get_custom_fields( 'post_types' );
        $options = $options == '' ? array() : $options;
        if(isset($options['cwp_map_meta'][$post_type]) && !empty($options['cwp_map_meta'][$post_type])){
            $MapMeta = $options['cwp_map_meta'][$post_type];
            return $MapMeta;
        }
    }
    
    private static function cwp_no_result_found(){
        return '<div class="cwp-empty-search"><img class="cwp-empty-search-img" src="'.esc_url(CWP_PLUGIN_URI.'cube/assets/frontend/images/no-result.png').'" alt=""><h2>'.esc_html__('No Results Found','cubewp-framework').'</h2><p>'.esc_html__('There are no results matching your search.','cubewp-framework').'</p></div>';
    }
}