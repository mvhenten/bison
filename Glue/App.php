<?php
/**
 * Glue/Module.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Glue
 */


namespace Glue;

require_once dirname(__FILE__) . '/Util.php';

use Glue\Util\Base;

class App extends Base {
    public function __construct( $path, $json ){
        unlink($path);
    }

    public function write(){

    }
}
