<?php
/**
 * cubewp ajax.
 *
 * @package cubewp/cube/classes
 * @version 1.0
 * @since  1.0.0
 * 
 * CubeWp_Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CubeWp_Ajax {
    
    private $AjaxHandle = '';
    private $className;
    const WPAJAX = 'wp_ajax_';
    
    const WPAJAXNOPRIV = 'wp_ajax_nopriv_';

    public function __construct($AjaxHandle='', $className='', $callback='') {
        $this->AjaxHandle = $this->__ajax_handle($AjaxHandle,$callback);
        $this->className = $className;
        add_action( $this->AjaxHandle, array ( $this->className, $callback ) );
    }    
    /**
     * Method __ajax_handle to call ajax
     *
     * @param $AjaxHandle $AjaxHandle [explicite description]
     * @param $callback $callback [explicite description]
     *
     * @return string ajax handle name
     * @since  1.0.0
     */
    public function __ajax_handle($AjaxHandle,$callback) {
        if(empty($AjaxHandle)){
            return self::WPAJAX.$callback;
        }else{
            return self::WPAJAXNOPRIV.$callback;
        }
    }
}