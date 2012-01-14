<?php
define( 'G_BASE_DIR', dirname(__FILE__) . '/../' );

require_once G_BASE_DIR . 'Glue/Util.php';
require_once G_BASE_DIR . 'Glue/Create.php';

use Glue\Util;
use Glue\Create;

@list( $script, $target ) = $argv;

if( !@is_dir( $target ) ){
    mkdir( $target );
}

$creator = new Create( realpath($target) );
$creator->create();
