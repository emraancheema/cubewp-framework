<?php 
/**
 * CubeWp sanitize is for sanitization of all custom field data.
 *
 * @version 1.0
 * @package cubewp/cube/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CubeWp_Sanitize
 */
class CubeWp_Sanitize {

    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    array    $input        The address input.
     * @return   array    $new_array    The sanitized input.
     */
    public function sanitize_post_type_custom_fields( $input) {
        
        // Initialize the new array that will hold the sanitize values
        $new_array = array();

        // Loop through the input and sanitize each of the values
        foreach ( $input as $name => $array ) {
            foreach ( $array as $key => $val ) {            
                $new_array[$name][ $key ] = $this->sanitize_cwp_custom_field($key, $val );
            }
        }

        return $new_array;

    }
    
    /**
     *
     * @since    1.0.0
     *
     * @param    array    $input        The address input.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_taxonomy_meta( $input ) {
        
        // Initialize the new array that will hold the sanitize values
        $new_input = array();

        // Loop through the input and sanitize each of the values
        foreach ( $input as $key => $val ) {
            
            $field_type = $this->taxonomy_field_type( $key );
            
            $new_input[ $key ] = $this->sanitize_cwp_field($field_type, $val );
        }

        return $new_input;

    }

    /**
     *
     * @since    1.0.0
     *
     * @param    string    $field        field slug.
     * @return   array    $field_type    type of field.
     */
    public function taxonomy_field_type( $field = '') {
    
        if(!$field){
            return;
        }
        $fieldOptions       = CWP()->get_custom_fields( 'taxonomy' );
        foreach($fieldOptions as $options){
            $SingleFieldOptions = isset($options[$field]) ? $options[$field] : array();
            $field_type = isset($SingleFieldOptions['type']) ? $SingleFieldOptions['type'] : '';
        }
        
        return $field_type;

    }
    
    /**
     *
     * @since    1.0.0
     *
     * @param    array    $input        The address input.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_post_type_meta( $input , $fields_of ) {
        
        // Initialize the new array that will hold the sanitize values
        $new_input = array();

        // Loop through the input and sanitize each of the values
        foreach ( $input as $key => $val ) {
            
            $field_type = $this->custom_field_type( $key, $fields_of );
                    
            if($field_type == 'repeating_field'){
                
                foreach ( $val as $repeating_key => $repeating_val_array ) {
                    
                    $field_type = $this->custom_field_type( $repeating_key, $fields_of );

                    if(isset($repeating_val_array) && !empty($repeating_val_array) && count($repeating_val_array) > 0){

                        foreach ( $repeating_val_array as $repeating_single_val ) {
                            
                            $new_input[ $key ][$repeating_key][] = $this->sanitize_cwp_field($field_type, $repeating_single_val );

                        }

                    }else{
                        $new_input[ $key ][$repeating_key] = array();
                    }

                }

            }else{
                
                $new_input[ $key ] = $this->sanitize_cwp_field($field_type, $val );
                
            }
                
        }

        return $new_input;

    }

    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    string    $field        field slug.
     * @return   array    $field_type    type of field.
     */

    public function custom_field_type( $field = '' , $fields_of = '') {
    
        if(!$field){
            return;
        }
        $fieldOptions  = CWP()->get_custom_fields( $fields_of );
        $singleField = isset($fieldOptions[$field]) ? $fieldOptions[$field] : '';
        if ( empty( $singleField ) ) {
            $custom_cubes_args = array(
                'name' => $field
            );
            $singleField = apply_filters( 'cubewp/custom/cube/field/options', $custom_cubes_args );
        }
        $field_type = isset($singleField['type']) ? $singleField['type'] : '';
        
        return $field_type;

    }
    
    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    string    $field_type   type of field.
     * @param    mix      $val           value of field to sanitize.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_cwp_field($field_type, $val ) {
                
        if(is_array($val)){
            return $this->sanitize_text_array($val);
        }
        
        $new_input = $val;
        
        switch ( $field_type ) {

			case 'text':

				$new_input = sanitize_text_field( $val );
				break;

			case 'number':

				$new_input = sanitize_text_field( $val );
				break;
                
            case 'color':

				$new_input = sanitize_hex_color( $val );
				break;
			case 'email':

				$new_input = sanitize_email( $val );
				break;

			case 'url':

				$new_input = sanitize_url( $val );
				break;

			case 'password':

				$new_input = sanitize_text_field( $val );
				break;
            case 'textarea':

                $new_input = sanitize_textarea_field( $val );
                break;
            case 'wysiwyg_editor':

                $new_input = wp_kses_post( $val );
                break;
            case 'oembed':

                $new_input = sanitize_url( $val );
                break;
            case 'file':

                $new_input = sanitize_file_name( $val );
                break;
            case 'image':

				$new_input = sanitize_file_name( $val );
				break;
            case 'url':

				$new_input = sanitize_text_field( $val );
				break;

			case 'gallery':

				$new_input = sanitize_text_field( $val );
				break;
            case 'switch':

                $new_input = sanitize_text_field( $val );
                break;
            case 'dropdown':

                $new_input = sanitize_text_field( $val );
                break;
            case 'checkbox':

                $new_input = sanitize_text_field( $val );
                break;
            case 'radio':

                $new_input = sanitize_text_field( $val );
                break;
                
            case 'google_address':

				$new_input = sanitize_text_field( $val );
				break;

		}
        
        return $new_input;

    }
    
    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    string    $field_type   type of field.
     * @param    mix      $val           value of field to sanitize.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_cwp_custom_field($field_type, $val ) {
        
        if(!$field_type){
            return;
        }
        $new_input = $val;
        
        switch ( $field_type ) {

			case 'label':

				$new_input = sanitize_text_field( $val );
				break;

			case 'name':

				$new_input = sanitize_text_field( $val );
				break;
                
            case 'type':

				$new_input = sanitize_text_field( $val );
				break;
			case 'description':

				$new_input = wp_strip_all_tags( wp_unslash($val) );
				break;

			case 'default_value':

				$new_input = sanitize_text_field( $val );
				break;

			case 'placeholder':

				$new_input = sanitize_text_field( $val );
				break;
            case 'options':

                $new_input = $this->sanitize_options( $val );
                break;
            case 'filter_post_types':

                $new_input = sanitize_text_field( $val );
                break;
            case 'filter_taxonomy':

                $new_input = sanitize_text_field( $val );
                break;
            case 'filter_user_roles':

                $new_input = sanitize_text_field( $val );
                break;
            case 'appearance':

				$new_input = sanitize_file_name( $val );
				break;
            case 'required':

				$new_input = sanitize_text_field( $val );
				break;
            case 'validation_msg':

				$new_input = sanitize_text_field( $val );
				break;

			case 'id':

				$new_input = sanitize_text_field( $val );
				break;
            case 'class':

                $new_input = sanitize_text_field( $val );
                break;
            case 'container_class':

                $new_input = sanitize_text_field( $val );
                break;
            case 'conditional_field':

                $new_input = sanitize_text_field( $val );
                break;
            case 'conditional_operator':

                $new_input = sanitize_text_field( $val );
                break;
                
            case 'conditional_value':

				$new_input = sanitize_text_field( $val );
				break;

		}
        
        return $new_input;

    }
    
    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    array    $input        The address input.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_options( $input ) {
        if ( is_array( $input ) && count( $input ) == 0 ) {
           return;
        }
        // Initialize the new array that will hold the sanitize values
        $new_input = array();
        // Loop through the input and sanitize each of the value
        foreach ( $input as $name => $options ) {
           foreach ( $options as $key => $val ) {
              $label[ $key ] = sanitize_text_field( $val );
           }
           $new_input[ $name ] = $label;
        }
     
        return $new_input;
     }
    
    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    string    $field_type   type of field.
     * @param    mix      $val           value of field to sanitize.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_text_array( $input ) {
        
        if(is_array($input) && count($input)==0){
            return;
        }
        
        // Initialize the new array that will hold the sanitize values
        $new_input = array();

        // Loop through the input and sanitize each of the values
        foreach ( $input as $key => $val ) {
            $key = sanitize_text_field($key);
            if ( is_array( $val ) ) {
                $new_input[ $key ] = $this->sanitize_text_array( $val );
            }else {
                $new_input[ $key ] = sanitize_text_field( $val );
            }
        }

        return $new_input;

    }

    /**
     * A custom sanitization function that will take the incoming input, and sanitize
     * the input before handing it back to WordPress to save to the database.
     *
     * @since    1.0.0
     *
     * @param    mix      $input        value of field to sanitize.
     * @return   array    $new_input    The sanitized input.
     */
    public function sanitize_multi_array( $input ) {
        
        if(is_array($input) && count($input)==0){
            return;
        }
        
        // Initialize the new array that will hold the sanitize values
        $new_input = array();

        // Loop through the input and sanitize each of the values
        foreach ( $input as $key => $val ) {
            foreach ( $val as $_key => $_val ) {
                $new_input[$key][ $_key ] = sanitize_text_field( $_val );
            }
        }

        return $new_input;

    }
}