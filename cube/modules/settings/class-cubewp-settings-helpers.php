<?php
/**
 * CubeWP Settings Helpers Container Class
 *
 * @class       CubeWP_Settings_Helpers
 * @package     CubeWP
 * @since       1.0
 */

if (!defined('ABSPATH'))
    exit;

class CubeWp_Settings_Helpers {

    public function make_bool_str( $var ) {
        
        
        if ( false === $var || 'false' === $var || 0 === $var || '0' === $var || '' === $var || empty( $var ) ) {
            return 'false';
        } elseif ( true === $var || 'true' === $var || 1 === $var || '1' === $var ) {
            return 'true';
        } else {
            return $var;
        }
    }
    
    public static function parse_str( $string ) {
        if ( '' === $string ) {
                return false;
        }

        $result = array();
        $pairs  = explode( '&', $string );

        foreach ( $pairs as $key => $pair ) {
            // use the original parse_str() on each element.
            parse_str( $pair, $params );

            $k = key( $params );
            if ( ! isset( $result[ $k ] ) ) {
                $result += $params;
            } elseif ( is_array( $result[ $k ] ) && is_array( $params[ $k ] ) ) {
                $result[ $k ] = self::array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
            }
        }

        return $result;
    }
    
    public static function array_merge_recursive_distinct( array $array1, array $array2 ) {
        $merged = $array1;
        foreach ( $array2 as $key => $value ) {
            if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                $merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
            } elseif ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
                $merged[] = $value;
            } else {
                $merged[ $key ] = $value;
            }
        }

        return $merged;
    }

}