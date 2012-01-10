<?php
/**
 * Glue/Module.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Glue
 */


namespace Glue;

require_once dirname(__FILE__) . '/Util.php';
require_once dirname(__FILE__) . '/Module/Page.php';
require_once dirname(__FILE__) . '/Module/Image.php';
require_once dirname(__FILE__) . '/Module/Link.php';
require_once dirname(__FILE__) . '/Module/Text.php';

use Glue\Util;
use Glue\Util\Base;
use Glue\Module\Page;
use Glue\Module\Image;
use Glue\Module\Link;
use Glue\Module\Text;


class App extends Base {
    protected $_path      = null;
    protected $_json      = null;
    protected $_elements  = null;

    protected $_page;
    protected $_pagepath;
    protected $_basenum;

    /**
     * Constructor required at leas a base path and json
     *
     * @param unknown $path
     * @param unknown $json
     */
    public function __construct( $path, $json ) {
        $this->_path = $path;
        $this->_json = $json;
    }


    /**
     * Write hotglue project to disk
     *
     */
    public function write() {
        mkdir( $this->pagepath );
        mkdir( $this->path . '/shared' );

        $this->_putFileContents( $this->page, 'page' );

        foreach ( $this->elements as $item ) {
            $this->_putFileContents( $item );
        }
    }


    /**
     * Builder: return module instances for all elements in the json object
     *
     * @return unknown
     */
    protected function _build__elements() {
        $collect = array();

        foreach ( $this->_json->elements as $element ) {
            $collect[] = $this->_getModule( $element->type, $element );
        }

        return $collect;
    }


    /**
     * Builder: returns a number (current microtime * 1000 ) to use
     * in Hotglue path defs
     *
     * @return unknown
     */
    protected function _build__basenum() {
        return (int) ( 1000 * microtime( true ) );
    }


    /**
     * Builder: returns a Glue\Module\Page instance
     *
     * @return Page $page
     */
    protected function _build__page() {
        return new Page( null, $this->_json );
    }


    /**
     * Builder: returns a "sanitized" page name to use as path
     *
     * @return string $path
     */
    protected function _build__pagepath() {
        $path = preg_replace( '/\W+/', '', $this->_json->title );
        $path = preg_replace( '/_+/', '-', $path );
        $path = trim($path, '-');

        $path = $this->_path . '/' . $path;

        return $path;
    }



    /**
     *
     *
     * @param unknown $item
     * @param unknown $name (optional)
     */
    private function _putFileContents( $item, $name = null ) {
        $path = $this->_getFileName( $name );

        if ( $item->file ) {
            $this->_moveImageFile( $item->file );
        }

        if ( false === file_put_contents( $path, (string) $item ) ) {
            die("cannot write to $path");
        }

        if ( $item->file ) {
            // unlink( $item->file->path );
        }
    }



    /**
     *
     *
     * @param unknown $file
     */
    private function _moveImageFile( $file ) {
        $path = $this->path . '/shared/' . $file->basename;

        if ( file_exists( $path ) ) {
            if ( md5_file( $path ) != md5_file( $file->path ) ) {
                $tries = 0;

                while ( file_exists($path) ) {
                    $tries++;

                    @list( $filename, $ext ) = slice( pathinfo($file->path), qw('filename extension'));
                    $path = sprintf('%s/shared/%s_%04d.%s', $this->path, $filename, $tries, $ext );
                }
            }
            else {
                return;
            }
        }

        rename( $file->path, $path );
        $file->set_basename( basename($path) );
    }


    /**
     * Hotglue filenames are based on unix timestamp;
     * So we'll do too, and create a fully qualified filename to be
     *
     * @param unknown $name (optional) Optional name of the file
     * @return fully qualified path
     */
    private function _getFileName( $name = null ) {
        if ( ! $name ) {
            $name = $this->basenum;
            $this->_basenum++;
        }

        return $this->pagepath . '/' . $name;
    }



    /**
     * Returns an instance of $type
     *
     * @param string  $type
     * @param object  $element
     * @return Glue\Module $type
     */
    private function _getModule( $type, $element ) {
        switch ( $type ) {
        case 'text':
            return new Text( null, $element );
            break;
        case 'image':
            return new Image( null, $element );
            break;
        case 'link':
            return new link( null, $element );
            break;
        default:
            throw new Exception( 'not a valid type: ' . $type );
        }
    }



}
