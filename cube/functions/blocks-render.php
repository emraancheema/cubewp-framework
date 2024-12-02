<?php 
/**
 * CubeWp Dynamic block rendering.
 *
 * @version 1.1.7
 * @package cubewp/cube/functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'render_block', 'cubewp_render_blocks', 10, 3 );
function cubewp_render_blocks($block_content, $block, $wp_block) {
    //cwp_pre($block_content);
        $block_content = html_entity_decode($block_content);
        if ( 'core/image' === $block['blockName'] ||  'kadence/image' === $block['blockName'] ) {
            preg_match('/src="([^"]+)"/', $block_content, $matches);
            
            if (isset($matches[1])) {
                $src = $matches[1];

                // Parse the URL
                $parsedUrl = parse_url($src);

                // Check if the URL has query strings
                if (isset($parsedUrl['query'])) {
                    
                    // Get the URL without query strings
                    $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

                    // Parse the query strings
                    parse_str($parsedUrl['query'], $queryStrings);

                    // Access individual query strings
                    foreach ($queryStrings as $name => $value) {
                        if('data-type' == $name){
                            if($value == 'post_custom_fields'){
                                $c_source = get_the_ID();
                            }elseif($value == 'user_custom_fields'){
                                global $current_user;
                                $c_source = $current_user->ID;
                            }
                            $args['f_type'] = $value;
                        }elseif('data-source' == $name){
                            $args['f_source'] = $value;
                        }elseif('data-content-source' == $name){
                            if($value == 'current-source'){
                                $args['p_id'] = $c_source;
                            }else{
                                $args['p_id'] = $value;
                            }
                        }elseif('data-name' == $name){
                            $args['f_name'] = $value;
                        }
                    }
                    $output = get_any_field_value($args);
                    $imageIDAttr = $block['attrs']['id'];
                    if (is_string($imageIDAttr) && strpos($imageIDAttr, '_') !== false) {
                        $parts = explode('_', $imageIDAttr);
                        $imgID = $parts[0];
                        $ImgPostfix = $parts[1];
                    }
                    if(count($output) <= $imgID){
                        if(is_numeric($output)){
                            $field = get_field_options($args['f_name']);
                            if($field['type'] == 'image' && $field['files_save'] == 'ids'){
                                $output = wp_get_attachment_url( $output, 'full' );
                            }
                        }else if(is_array($output)){
                            $field = get_field_options($args['f_name']);

                            if($field['type'] == 'gallery' && $field['files_save'] == 'ids'){
                                if(isset($output[$imgID])){
                                    $value = wp_get_attachment_url( $output[$imgID], 'full' );
                                    return preg_replace('/(<img[^>]+)src="([^"]+)"/', '$1src="' . $value . '"', $block_content);
                                }
                            }
                        }
                        if(!is_array($output)){
                            //return preg_replace('/(<img[^>]+)src="([^"]+)"/', '$1src="' . $output . '"', $block_content);
                        }
                    }
                }
            }
            //return $block_content;
        }

        if ( 'core/gallery' === $block['blockName'] ) {
            preg_match('/src="([^"]+)"/', $block_content, $matches);
            
            if (isset($matches[1])) {
                $src = $matches[1];

                // Parse the URL
                $parsedUrl = parse_url($src);

                // Check if the URL has query strings
                if (isset($parsedUrl['query'])) {
                    
                    // Get the URL without query strings
                    $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

                    // Parse the query strings
                    parse_str($parsedUrl['query'], $queryStrings);

                    // Access individual query strings
                    foreach ($queryStrings as $name => $value) {
                        if('data-type' == $name){
                            if($value == 'post_custom_fields'){
                                $c_source = get_the_ID();
                            }elseif($value == 'user_custom_fields'){
                                global $current_user;
                                $c_source = $current_user->ID;
                            }
                            $args['f_type'] = $value;
                        }elseif('data-source' == $name){
                            $args['f_source'] = $value;
                        }elseif('data-content-source' == $name){
                            if($value == 'current-source'){
                                $args['p_id'] = $c_source;
                            }else{
                                $args['p_id'] = $value;
                            }
                        }elseif('data-name' == $name){
                            $args['f_name'] = $value;
                        }
                    }
                    $output = get_any_field_value($args);
                    
                    if(is_array($output)){
                        $field = get_field_options($args['f_name']);
                        foreach($output as $imgID){
                            if($field['type'] == 'gallery' && $field['files_save'] == 'ids'){
                                $value = wp_get_attachment_url( $imgID, 'full' );
                                return preg_replace('/(<img[^>]+)src="([^"]+)"/', '$1src="' . $value . '"', $block_content);
                            }
                        }
                    }
                    if(!is_array($output)){
                        //return preg_replace('/(<img[^>]+)src="([^"]+)"/', '$1src="' . $output . '"', $block_content);
                    }
                }
            }
            return;
        }

        $pattern = '/<span\s+((?:data-[\w-]+=(?:"|\').*?(?:"|\')\s+)+)class=(?:"|\').*?cwp-dynamic-field.*?(?:"|\')\s*>(.*?)<\/span>/s';
        $block_content = preg_replace_callback($pattern, function ($matches) {
            $attribute_str = $matches[1];
            $content_value = $matches[2];

            // Extract attribute values
            preg_match_all('/([\w-]+)=(?:"|\')(.*?)(?:"|\')\s+/s', $attribute_str, $attribute_matches, PREG_SET_ORDER);

            // Process each attribute match
            $attributes = array();
            foreach ($attribute_matches as $attribute_match) {
                $attribute_name = $attribute_match[1];
                $attribute_value = $attribute_match[2];
                $attributes[$attribute_name] = $attribute_value;
            }
            $args = []; 
            // Output attribute values and content
            foreach ($attributes as $name => $value) {
                if('data-type' == $name){
                    if($value == 'post_custom_fields'){
                        $c_source = get_the_ID();
                    }elseif($value == 'user_custom_fields'){
                        global $current_user;
                        $c_source = $current_user->ID;
                    }
                    $args['f_type'] = $value;
                }elseif('data-source' == $name){
                    $args['f_source'] = $value;
                }elseif('data-content-source' == $name){
                    if($value == 'current-source'){
                        $args['p_id'] = $c_source;
                    }else{
                        $args['p_id'] = $value;
                    }
                }elseif('data-name' == $name){
                    $args['f_name'] = $value;
                }
            }
            $output = get_any_field_value($args);
            // Return the replacement
            return $output;
        }, $block_content);

        
    return $block_content;
}