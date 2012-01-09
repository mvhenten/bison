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

class Text extends Module {
    protected $_module     = 'text';
    protected $_type       = 'text';
}
