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
     * @return unknown
     */
    protected function _build__info() {
        $info = getimagesize( $this->path );

        list($width, $height, $type, $attr) = $info;

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

        /**
         *
         *
         * @TODO file should be moved to
         * CONTENT_PATH or something intelligent
         */
        $tmp_name = tempnam('/tmp', 'glue_');

        file_put_contents( $tmp_name, file_get_contents( $this->_file_src ));

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