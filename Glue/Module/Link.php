<?php
/**
 * Glue/Module/Link.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue\Module;

require_once dirname(__FILE__) . '/../Module.php';

use Glue\Module;

class Link extends Module {

    protected $_link;

    protected function _build__properties() {

        return $this->_flatten(array(
                'type'          => $this->_type,
                'module'        => $this->_module,
                'object-link'   => $this->link,
            ));
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__link() {
        return $this->_element->properties->href;
    }
}
