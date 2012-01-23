<?php
/**
 * ../Glue/Util.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue\Util {
    abstract class Base {

        /**
         *
         *
         * @param unknown $name
         * @return unknown
         */
        public function __get( $name ) {
            if ( ($property = "_$name")
                && (isset($this->$property) || property_exists( $this, $property ))) {
                if ( ! isset( $this->$property )
                    && method_exists( $this, "_build_$property" ) ) {
                    $method = "_build_$property";
                    $this->$property = $this->$method();
                }
                return $this->$property;
            }
        }


    }


}


namespace Glue {


    /**
     *
     *
     * @param unknown $function
     * @param unknown $array
     * @return unknown
     */
    function map( $function, $array ) {
        return array_map( $function, array_keys($array), array_values($array) );
    }


    /**
     *
     *
     * @param array   $array
     * @param unknown $keys
     * @return unknown
     */
    function slice( array $array, $keys ) {
        $args  = func_get_args();
        $input = array_shift($args);

        if ( is_array( $args[0] ) ) {
            $args = $args[0];
        }

        $args = array_flip($args);

        return array_intersect_key( $input, $args );
    }


    /**
     *
     *
     * @param unknown $str
     * @return unknown
     */
    function qw( $str ) {
        return array_filter( array_map('trim', preg_split( '/\s/', trim($str) ) ) );
    }


    /**
     *
     *
     * @param unknown $args
     * @return unknown
     */
    function optparse( $args ) {
        $options = func_get_args();

        $longopts = array();
        $shortopt = '';

        $keys   = qw('long short required switch default help');
        $lookup = array();


        foreach ( $options as $option ) {
            @list( $long, $short, $required, $switch, $default, $help ) = slice( $option, $keys );

            if ( $required ) {
                $shortopts .= "$short:";
                $longopts[] = "$long:";
            }
            else if ( !$switch ) {
                    $shortopts .= "$short::";
                    $longopts[] = "$long::";
                }

            $lookup[$short] = $option;
            $lookup[$long]  = $option;
        }

        return getopt( $opts );
    }


}
