<?php
define( 'G_BASE_DIR', dirname(__FILE__) . '/../' );

require_once( G_BASE_DIR . 'Glue/Util.php');
require_once( G_BASE_DIR . 'Glue/App.php');

use Glue\Util;
use Glue\App;

@list( $script, $in, $out ) = $argv;

$usage = function(){
    echo "parse a json file into a hotglue content directory\n";
    echo "USAGE: " . basename(__FILE__) . " <INPUT> <TARGET>\n";
};

if( !$in || !file_exists($in) ){
    $usage();
    die("ERROR: no input file given!\n");
}
else if( ( $json = json_decode($in) ) && ! $json ){
    $usage();
    die("ERROR: not a valid json string!\n");
}
else if( !$out || !file_exists($out) ){
    $usage();

    $tempfile = tempname( dirname(__FILE__), 'hotglue-' );

    echo "no target given, writing to $tempfile\n";

    unlink($tempfile);
    mkdir($tempfile);

    $out = $tempfile;
}

$app = App( $out, $json );

$app->write();
