<?php
/**
 * Glue/Module/Page.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue\Module;

require_once dirname(__FILE__) . '/../Module.php';
require_once dirname(__FILE__) . '/../File.php';

use Glue\Module;
use Glue\File;


class Page extends Module {

    protected $_color;
    protected $_title;
    protected $_file;
    protected $_attachment;

    protected $_css = '';

    /**
     *
     *
     * @return unknown
     */
    protected function _build__properties() {
        $properties = array(
            'page-background-color'      => $this->color,
            'page-title'                 => $this->title,
        );

        if ( $this->file ) {
            $properties = array_merge( $properties, array(
                    'page-background-file'       => $this->file->basename,
                    'page-background-mime'       => $this->file->info->mime,
                    'page-background-attachment' => $this->attachment,
                ));
        }

        return $this->_flatten( $properties );
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__color() {
        return $this->style('background-color');
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__title() {
        return $this->_element->title;
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__file() {
        if ( ( $src = $this->style('background-image') ) && isset($src) ) {
            return new file( $src );
        }
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__attachment() {
        return $this->style('background-attachment');
    }


}
