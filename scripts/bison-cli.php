<?php
/**
 * scripts/bison-cli.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


define( 'G_BASE_DIR', dirname(__FILE__) . '/../' );

require_once G_BASE_DIR . 'Glue/Util.php';
require_once G_BASE_DIR . 'Glue/App.php';

use Glue\Util;
use Glue\App;

@list( $script, $in, $out ) = $argv;

global $json;

/**
 *
 */
$usage = function() {
    echo "parse a json file into a hotglue content directory\n";
    echo "USAGE: " . basename(__FILE__) . " <INPUT> <TARGET>\n";
};

if ( !$in || !file_exists($in) ) {
    $usage();
    die("ERROR: no input file given!\n");
}
else {
    $json = json_decode( file_get_contents($in) );
}

if ( ! $json ) {
    $usage();
    die("ERROR: not a valid json string!\n");
}
elseif ( !$out || !file_exists($out) ) {
    $usage();

    $tempfile = tempnam( getcwd(), 'hotglue-' );

    echo "no target given, writing to $tempfile\n";

    unlink($tempfile);
    mkdir($tempfile);

    $out = $tempfile;
}
else {
    echo "parsing $in to $out";
}


$app = new App( $out, $json );
$app->write();
