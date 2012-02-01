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

ini_set('display_errors', "false");
ini_set('display_warnings', "false");

define( 'BISON_WWW_PATH', dirname($_SERVER['SCRIPT_FILENAME']) );
define( 'BISON_BASE_PATH', dirname(__FILE__) . '/..' );
define( 'BISON_SOURCE_HOST', 'www.transmediale.de' );
define( 'BISON_JSON_CACHE_TTL', 10800 ); // three hours
//define( 'BISON_SOURCE_HOST', 'transmediale.localhost' );

@session_start();

if ( isset( $_SESSION['uniqid'] ) && !isset( $_GET['s'] ) ) {
    $path = find_user_page( $_SESSION['uniqid'] );

    if ( $path ) {
        relative_redirect( 'user', $_SESSION['uniqid'], $path );
    }
}

if ( !isset( $_GET['s'] ) ) {
    header('Location: http://incompatible.hotglue.org/read/me');
//    header('403 Forbidden', true, 403 );
    exit();
}

if ( ( $url = parse_url( $_GET['s'] ) ) &&  isset($url['host']) && $url['host'] !==BISON_SOURCE_HOST ) {
    header('400 Bad Request', true, 400 );
    exit();
}

$pause = true;

if( $pause ){
	// enable this to stop bison temporarily
	header('Location: ' . $_GET['s']);
	exit();

}



/**
 * Creates an absolute url from various $path_parts and does a redirect
 * Not that parts must be given as separate arguments.
 *
 * @param unknown $path_parts
 */
function relative_redirect( $path_parts ) {
    $path_parts = array_merge( array('http:/', $_SERVER['SERVER_NAME']), func_get_args());

    $target = join( '/', $path_parts );
    header('Location: ' . $target, 303 );
    echo $target;
    exit();
}


/**
 * Tries to find an existing page for a given $uniqid user
 *
 * @param unknown $uniqid
 * @return unknown
 */
function find_user_page( $uniqid ) {
    $user_path = BISON_WWW_PATH . '/user/' . $uniqid;
    if ( !is_dir( $user_path ) ) {
        return false;
    }

    $content_path = $user_path . '/content/';

    $files = scandir( $content_path );

    foreach ( $files as $path ) {
        $whitelist = array( 'cache', '.', '..' );
        if ( is_dir( $content_path . $path ) &&  !in_array( basename($path), $whitelist ) ) {
            return $path;
        }
    }

    return false;
}


/**
 * Runs phantomjs in order to scrape designated URL and create a
 * json spec file for it, replacing the original cache if existing.
 *
 * @param unknown $hotglue_source_url
 * @param unknown $json_source_path
 * @return unknown
 */
function run_phantomjs( $hotglue_source_url, $json_source_path ) {
    $phantomjs = trim(shell_exec('which phantomjs' ));

    $tmp_file = tempnam( sys_get_temp_dir(), 'bison-json-cache-' );

    $args = array(
        escapeshellarg( $hotglue_source_url ),
        escapeshellarg( $tmp_file )
    );

    chdir( realpath( dirname(__FILE__) . '/../js' ) );
    $cmd = vsprintf('xvfb-run -w0 -a phantomjs transmediale.phantom.js %s %s', $args );
    exec( $cmd, $output, $status );

    if ( $status !== 0 ) {
        error_cannot_run_xvfb();
    }

    unlink( $json_source_path );
    rename( $tmp_file, $json_source_path );

    return true;
}


/**
 * Compares file mtime to cache TT, returns true
 * if mtime is older then TT.
 *
 * @param unknown $json_source_path
 * @return unknown
 */
function json_cache_expired( $json_source_path ) {
    $mtime = filemtime( $json_source_path );

    if ( $mtime < ( time() - BISON_JSON_CACHE_TTL ) ) {
        trigger_error( 'cache expired for ' . $json_source_path );
        return true;
    }
    return false;
}


/**
 * Borks out and provides a "helpful" error message (link back to tm)
 */
function error_cannot_run_xvfb() {
?>
    <h2>Ooops...</h2>
    <p>Something unexpected has happened, we are not yet sure what it could have been.</p>
    <p>Follow <a href="<?php echo $hotglue_source_url ?>">this</a> link to get back to where you wanted to go.</p>
    <?php
    exit();
}



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
$json_source_path    = sys_get_temp_dir() . '/json-cache-' . '-' . md5( $hotglue_source_url );

if ( !file_exists( $user_hotglue_path ) ) {
    @mkdir( $user_hotglue_path );
    $create = new Create( $user_hotglue_path );
    $create->run();
}

$url_parsed = parse_url( $hotglue_source_url );
$page_name       = isset( $url_parsed['path'] ) ? str_replace('/', '-', trim($url_parsed['path'], '/')) : 'start';
$hotglue_content_path = $user_hotglue_path . '/content/' . $page_name;

if ( !file_exists( $hotglue_content_path ) ) {
    if ( ! file_exists( $json_source_path ) || json_cache_expired( $json_source_path ) ) {
        trigger_error( 'running phantom js for ' . $hotglue_source_url );
        run_phantomjs( $hotglue_source_url, $json_source_path );
    }

    trigger_error( 'creating new instance for ' . $uniqid . ' ' . $hotglue_source_url );

    $json = json_decode( file_get_contents( $json_source_path ) );
    $app  = new App( $hotglue_content_path, $json );
    $app->write();
}

relative_redirect( 'user', $uniqid, $page_name );
