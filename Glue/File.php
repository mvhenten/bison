<?php
/**
 * Glue/File.php
 *
 * Contains logic needed to retrieve ( remote) files for Bison
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue;

class File {
    private $_file_src;
    private $_use_cache_dir = true;

    protected $_info;
    protected $_path;
    protected $_basename;
    protected $_source;

    /**
     * Class constructor
     *
     * @param str     $src   original source for the file
     * @param bool    $cache (optional) use local file caching
     */
    public function __construct( $src, $cache = true ) {
        $this->_file_src        = $src;
        $this->_use_cache_dir   = $cache;
    }


    /**
     * Auto getter. Triggers a _build_$name function once.
     *
     * @param str     $name
     * @return mixed
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
     * Move the file to a new location
     *
     * @param string  $dest Path to move the file to
     */
    public function move( $dest ) {
        rename( $this->path, $dest );

        $this->_path     = $dest;
        $this->_basename = basename($dest);
    }


    /**
     * lazy builder
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
     * Lazy builder - retrieves file contents from the original (remote)
     * source and copies it to a temp file.
     *
     * @return unknown
     */
    protected function _build__path() {
        $tmp_name = tempnam( sys_get_temp_dir(), 'glue_');
        $file_src = $this->source;
        $contents = file_get_contents( $file_src );

        if ( false === $contents ) {
            trigger_error( 'cannot read ' . $this->_file_src, E_USER_NOTICE );
            return false;
        }

        if ( false == file_put_contents( $tmp_name, $contents ) ) {
            trigger_error( 'cannot write to ' . $tmp_name, E_USER_NOTICE );
            return false;
        }

        return $tmp_name;
    }


    /**
     * Lazy builder - this is a wrapper around the original source, implementing
     * a local file cache wich is faster then retrieving remotely.
     *
     * @return unknown
     */
    protected function _build__source() {
        if ( ! $this->_use_cache_dir ) {
            return $this->_file_src;
        }

        $cache_dir = sys_get_temp_dir() . '/.bison-file-cache/';
        $md5hash   = md5( $this->_file_src );
        $path = $cache_dir . $md5hash;

        if ( ! file_exists( $path ) ) {
            if ( ! is_dir( $cache_dir ) ) {
                mkdir( $cache_dir );
            }

            $content = file_get_contents( $this->_file_src );

            if ( false === $content ) {
                trigger_error( 'cannot read from ' . $this->_file_src, E_USER_NOTICE );
                return;
            }

            $bytes = file_put_contents( $path, $content );

            if ( false === $bytes ) {
                trigger_error( 'unable to read/write to cache ' . $path, E_USER_NOTICE );
                return;
            }
            if ( 0 == $bytes ) {
                trigger_error( 'zero bytes written to ' . $path, E_USER_NOTICE );
                unlink($path);
            }
        }

        return $path;
    }


    /**
     * lazy builder - returns basename of the orignal file.
     *
     * @return unknown
     */
    protected function _build__basename() {
        return basename( $this->_file_src );
    }


}
