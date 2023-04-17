<?php
/**
 * CubeWp pagination to show pagination for post loop.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Pagination
 */
class CubeWp_Pagination{
    
    protected $total_posts     = '';
    protected $archive_page = '';
    protected $extra_query = '';
    protected $posts_per_page  = '10';
    protected $page_num        = 1;
    protected $total_page      = 0;
    protected $loop_start      = '';
    protected $loop_end        = '';
    
    public function __construct() {
        add_filter('cubewp_frontend_posts_pagination', array($this, 'cubewp_archive_pagination'), 10, 2);
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
       
    /**
     * Method cubewp_archive_pagination
     *
     * @param string $output 
     * @param array $args
     *
     * @return string html
     * @since  1.0.0
     */
    public function cubewp_archive_pagination( $output = '', $args = array() ) {
        extract($args);
        $this->total_posts     = isset($total_posts) ? $total_posts : '';
        $this->archive_page    = isset($archive) ? $archive : '';
        $this->extra_query    = isset($query_string) ? $query_string : '';
        $this->posts_per_page  = isset($posts_per_page) ? $posts_per_page : '';
        $this->page_num        = isset($page_num) ? $page_num : '';
        if ( $this->total_posts <= $this->posts_per_page ) return '';
        
        return $this->pagination();
    }
        
    /**
     * Method pagination
     *
     * @return string html
     * @since  1.0.0
     */
    public function pagination( ) {
        $output      = '';
        if ( $this->total_posts != 0 ){
            $this->total_page = ceil($this->total_posts / $this->posts_per_page);
        }

        $this->loop();

        $output .= '<div class="cwp-pagination">';
            $output .= '<ul>';

                $output .= $this->prev_page();

                $output .= $this->to_first_page();

                $output .= $this->dots();

                $output .= $this->loop_links();

                $output .= $this->loop_dots();

                $output .= $this->to_last_page();

                $output .= $this->next_page();

            $output .= "</ul>";
        $output .= "</div>";

        return $output;
    }
        
    /**
     * Method loop
     *
     * @return int 
     * @since  1.0.0
     */
    public function loop( ) {
        
        $this->loop_start = $this->page_num - 2;
        $this->loop_end   = $this->page_num + 2;

        if( $this->page_num < 3 ){
            $this->loop_start = 1;
            if ( $this->total_page < 5 ){
                $this->loop_end = $this->total_page;
            }else{
                $this->loop_end = 5;
            }
        } else if( $this->page_num >= $this->total_page - 1 ){
            if ( $this->total_page < 5 ){
                $this->loop_start = 1;
            }else{
                $this->loop_start = $this->total_page - 4;
            }
            $this->loop_end = $this->total_page;
        }
        
    }
        
    /**
     * Method prev_page
     *
     * @return string html
     * @since  1.0.0
     */
    public function prev_page( ) {
        
        if ( $this->page_num > 1 ) {
            if($this->archive_page == 'false'){
                $output = '<li><a href="?page_num=' . ($this->page_num - 1) .'">';
            }
            else{
                $output = '<li><a href="javascript:void(0);" onclick="cubewp_posts_pagination_ajax(\'' . ($this->page_num - 1) . '\');">';
            }
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg></a></li>';
            return $output;
        }
        
    }
        
    /**
     * Method to_first_page
     *
     * @return string html
     * @since  1.0.0
     */
    public function to_first_page( ) {
        
        if( $this->page_num > 3 && $this->total_page > 5 ){
            if($this->archive_page == 'false'){
                $output = '<li><a href="?page_num=1">';
            }
            else{
                $output = '<li><a href="javascript:void(0);" onclick="cubewp_posts_pagination_ajax(\'' . (1) . '\');">';
            }
            $output .= '1</a></li>';
            return $output;
        }
        
    }
        
    /**
     * Method dots
     *
     * @return string html
     * @since  1.0.0
     */
    public function dots( ) {
        
        if ( $this->page_num > 4 && $this->total_page > 6 ) {
            return '<li class="disabled"><span><a>. . .</a></span><li>';
        }
        
    }
        
    /**
     * Method loop_links
     *
     * @return string html
     * @since  1.0.0
     */
    public function loop_links( ) {
        $output = '';
        if( $this->total_page > 1 ){
            for( $i = $this->loop_start; $i <= $this->loop_end; $i ++ ){
                if( $i != $this->page_num ){
                    if($this->archive_page == 'false'){
                        $output .= '<li><a href="?page_num=' . $i .$this->extra_query.'">';
                    }
                    else{
                        $output .= '<li><a href="javascript:void(0);" onclick="cubewp_posts_pagination_ajax(\'' . ($i) . '\');">';
                    }
                    $output .= $i . '</a></li>';
                }else{
                    $output .= '<li class="active"><span><a class="page-number">' . $i . '</a></span></li>';
                }
            }
        }
        return $output;
        
    }
        
    /**
     * Method loop_dots
     *
     * @return string html
     * @since  1.0.0
     */
    public function loop_dots( ) {
        
        if( $this->loop_end != $this->total_page && $this->loop_end != $this->total_page - 1 ){
            return '<li><a>. . .</a></li>';
        }
        
    }
        
    /**
     * Method to_last_page
     *
     * @return string html
     * @since  1.0.0
     */
    public function to_last_page( ) {
        
        if( $this->loop_end != $this->total_page ) {
            if($this->archive_page == 'false'){
                $output = '<li><a href="?page_num=' . $this->total_page .'">';
            }
            else{
                $output = '<li><a href="javascript:void(0);" onclick="cubewp_posts_pagination_ajax(\'' . ($this->total_page) . '\');">';
            }
            $output .= $this->total_page . '</a></li>';
            return $output;
        }
        
    }
        
    /**
     * Method next_page
     *
     * @return string html
     * @since  1.0.0
     */
    public function next_page( ) {
        
        if( $this->total_posts > 0 && $this->page_num < ($this->total_posts / $this->posts_per_page) ){
            if($this->archive_page == 'false'){
                $output = '<li><a href="?page_num=' . ($this->page_num + 1) .'">';
            }
            else{
                $output = '<li><a href="javascript:void(0);" onclick="cubewp_posts_pagination_ajax(\'' . ($this->page_num + 1) . '\');">';
            }
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></a></li>';
            return $output;
        }
        
    }
    
}