<?php
/**
 * Glue/File.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue;

class File {
    private $_file_src;

    protected $_info;
    protected $_path;
    protected $_basename;

    /**
     *
     *
     * @param unknown $src
     */
    public function __construct( $src ) {
        $this->_file_src = $src;
    }


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



    /**
     *
     *
     * @param unknown $new_name
     */
    public function set_basename( $new_name ) {
        $this->_basename = $new_name;
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__info() {
        $info = array();

        if ( false !== $this->path ) {
            $info = getimagesize( $this->path );
        }

        @list($width, $height, $type, $attr) = $info;

        return (object) array(
            'width'     => $width,
            'height'    => $height,
            'mime'      => $info['mime']
        );
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__path() {
        $tmp_name = tempnam( sys_get_temp_dir(), 'glue_');
        $file_src = $this->_file_src;
        $contents = file_get_contents( $file_src );

        if ( false === $contents ) {
            trigger_error( 'cannot read ' . $this->_file_src );
            return false;
        }

        if ( false == file_put_contents( $tmp_name, $contents ) ) {
            trigger_error( 'cannot write to ' . $tmp_name );
            return false;
        }

        print "WROTE TO $tmp_name \n";

        return $tmp_name;
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__basename() {
        return basename( $this->_file_src );
    }


}
