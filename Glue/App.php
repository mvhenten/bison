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
     *
     *
     * @param unknown $path
     * @param unknown $json
     */
    public function __construct( $path, $json ) {
        $this->_path = $path;
        $this->_json = $json;
    }


    /**
     *
     */
    public function write() {
        mkdir( $this->pagepath );

        file_put_contents( $this->_getFileName('page'), (string) $this->page );

        foreach ( $this->elements as $item ) {
            file_put_contents( $this->_getFileName(), (string) $item );
        }
    }


    /**
     *
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
     *
     *
     * @return unknown
     */
    protected function _build__basenum() {
        return (int) ( 1000 * microtime( true ) );
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__page() {
        return new Page( null, $this->_json );
    }


    /**
     *
     *
     * @return unknown
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
     * @param unknown $name (optional)
     * @return unknown
     */
    private function _getFileName( $name = null ) {
        if ( ! $name ) {
            $name = $this->basenum;
            $this->_basenum++;
        }

        return $this->pagepath . '/' . $name;
    }


    /**
     *
     *
     * @param unknown $type
     * @param unknown $element
     * @return unknown
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
