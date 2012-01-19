<?php
/*
 The *very* simple storefront for bison/tm2012
 Only a set of uris is supported, therefore, we'll whitelist those.

 This directory is assumed to be your www root
*/



define( 'BISON_WWW_PATH', dirname(__FILE__) );
define( 'BISON_BASE_PATH', dirname(__FILE__) . '/..' );
//define( 'BISON_SOURCE_HOST', 'www.transmediale.de' );
define( 'BISON_SOURCE_HOST', 'transmediale.localhost' );

if( !isset( $_GET['s'] ) ){
    header('403 Forbidden', true, 403 );
    exit();
}

if( ( $url = parse_url( $_GET['s'] ) ) &&  isset($url['host']) && $url['host'] !==BISON_SOURCE_HOST ){
    header('400 Bad Request', true, 400 );
    exit();
}

@session_start();

require_once( BISON_BASE_PATH . '/Glue/Create.php' );
require_once( BISON_BASE_PATH . '/Glue/App.php' );

use Glue;
use Glue\App;
use Glue\Create;

if( !isset( $_SESSION['uniqid'] ) ){
    $_SESSION['uniqid'] = uniqid();
}

$uniqid              = $_SESSION['uniqid'];
$user_hotglue_path   = BISON_WWW_PATH . '/user/' . $uniqid;
$hotglue_source_url  = $_GET['s'];
$hotglue_source_json = sys_get_temp_dir() . '/json-cache-' . md5( $hotglue_source_url );

session_destroy();

if( !file_exists( $user_hotglue_path ) ){
    @mkdir( $user_hotglue_path );
    $create = new Create( $user_hotglue_path );
    $create->run();
}

$exec = array (
    'phantomjs',
    BISON_BASE_PATH . '/js/transmediale.phantom.js',
    escapeshellarg( $hotglue_source_url ),
    escapeshellarg( $hotglue_source_json )
);

exec( join( ' ', $exec ), $output );

trigger_error( print_r( $output, true ) );
$json = json_decode( file_get_contents( $hotglue_source_json ) );

$app = new App( $user_hotglue_path . '/content', $json );
$app->write();



exit();





