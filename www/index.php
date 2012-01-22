<?php
/**
 * www/index.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


/*
 The *very* simple storefront for bison/tm2012
 Only a set of uris is supported, therefore, we'll whitelist those.

 This file is assumed to be in your www root
*/

//ini_set('display_errors', "true");
//ini_set('display_warnings', "true");

define( 'BISON_WWW_PATH', dirname($_SERVER['SCRIPT_FILENAME']) );
define( 'BISON_BASE_PATH', dirname(__FILE__) . '/..' );
define( 'BISON_SOURCE_HOST', 'www.transmediale.de' );
//define( 'BISON_SOURCE_HOST', 'transmediale.localhost' );

if ( !isset( $_GET['s'] ) ) {
    header('403 Forbidden', true, 403 );
    exit();
}

if ( ( $url = parse_url( $_GET['s'] ) ) &&  isset($url['host']) && $url['host'] !==BISON_SOURCE_HOST ) {
    header('400 Bad Request', true, 400 );
    exit();
}

function error_cannot_run_xvfb() {
?>
    <h2>Ooops...</h2>
    <p>Something unexpected has happened, we are not yet sure what it could have been.</p>
    <p>Follow <a href="<?php echo $hotglue_source_url ?>">this</a> link to get back to where you wanted to go.</p>
    <?php
    exit();
}


@session_start();

require_once BISON_BASE_PATH . '/Glue/Create.php';
require_once BISON_BASE_PATH . '/Glue/App.php';
require_once BISON_BASE_PATH . '/Glue/Util.php';

use Glue\App;
use Glue\Util;
use Glue\Create;

if ( !isset( $_SESSION['uniqid'] ) ) {
    $_SESSION['uniqid'] = uniqid();
}

$uniqid              = $_SESSION['uniqid'];
$user_hotglue_path   = BISON_WWW_PATH . '/user/' . $uniqid;
$hotglue_source_url  = $_GET['s'];
$json_source_path    = sys_get_temp_dir() . '/json-cache-' . $uniqid . '-' . md5( $hotglue_source_url );

if ( !file_exists( $user_hotglue_path ) ) {
    @mkdir( $user_hotglue_path );
    $create = new Create( $user_hotglue_path );
    $create->run();
}

$url_parsed = parse_url( $hotglue_source_url );
$page_name       = isset( $url_parsed['path'] ) ? str_replace('/', '-', trim($url_parsed['path'], '/')) : 'start';
$hotglue_content_path = $user_hotglue_path . '/content/' . $page_name;

if ( !file_exists( $hotglue_content_path ) ) {
    $phantomjs = trim(shell_exec('which phantomjs' ));

    $args = array(
        escapeshellarg( $hotglue_source_url ),
        escapeshellarg( $json_source_path )
    );

    chdir( realpath( dirname(__FILE__) . '/../js' ) );
    $cmd = vsprintf('xvfb-run -w0 -a phantomjs transmediale.phantom.js %s %s', $args );
    exec( $cmd, $output, $status );

    if ( $status !== 0 ) {
        error_cannot_run_xvfb();
    }

    $json = json_decode( file_get_contents( $json_source_path ) );

    $app  = new App( $hotglue_content_path, $json );
    $app->write();
}

$target = join( '/', array('http:/', $_SERVER['SERVER_NAME'], 'user', $uniqid, $page_name ) );

header('Location: ' . $target, 303 );
echo $target;
