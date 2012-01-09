<?php
/**
 * Glue/Module/Image.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue\Module;

require_once dirname(__FILE__) . '/../Module.php';
require_once dirname(__FILE__) . '/../File.php';

use Glue\Module;
use Glue\File;

class Image extends Module {
    protected $_type    = 'image';
    protected $_module  = 'image';

    protected $_file;
    protected $_repeat;

    /**
     *
     *
     * @return unknown
     */
    protected function _build__properties() {

        return $this->_flatten(array(
                'type'                    => $this->_type,
                'module'                  => $this->_module,
                'image-background-repeat' => $this->repeat,
                'image-file'              => $this->file->basename,
                'image-file-mime'         => $this->file->info->mime,
                'image-file-width'        => $this->file->info->width,
                'image-file-height'       => $this->file->info->height,
            ));
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__file() {
        return new File( $this->_element->properties->src );
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__repeat() {
        $style = $this->style;
        if ( isset($style['background-repeat']) ) {
            return $style['background-repeat'];
        }
        return 'no-repeat';
    }


}
