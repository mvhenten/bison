<?php
ini_set('display_errors', "false");
ini_set('display_warnings', "false");

define( 'BISON_WWW_PATH', dirname($_SERVER['SCRIPT_FILENAME']) );
define( 'BISON_BASE_PATH', dirname(__FILE__) . '/..' );
define( 'BISON_SOURCE_HOST', 'www.transmediale.de' );
define( 'BISON_USER_CACHE_TTL', 1800 ); // half an hour
define( 'BISON_PAGE_LIST_CACHE', sys_get_temp_dir() . '/bison-page-list.json');
//define( 'BISON_SOURCE_HOST', 'transmediale.localhost' );

function page_cache_expired(){
    $mtime = filemtime( BISON_PAGE_LIST_CACHE );

    if ( $mtime < ( time() - BISON_USER_CACHE_TTL ) ) {
        trigger_error( 'cache expired for ' . BISON_PAGE_LIST_CACHE );
        return true;
    }
    return false;
}

function get_content_dir_list(){
    $args = array(
        escapeshellarg( BISON_WWW_PATH ),
    );

    chdir( BISON_WWW_PATH . '/user/' );
    $cmd = vsprintf('find . | grep content', $args );
    exec( $cmd, $output, $status );
    return $output;
}

function get_page_list(){
    $files = get_content_dir_list();
    $pages = array();

    foreach( $files as $path ){
        $match = get_page_name($path);
        if( $match ){
            list( $dir, $uid, $page_name ) = $match;

            if( $page_name == 'cache' ){
                continue;
            }

            if(!isset($pages[$page_name])){
                $pages[$page_name] = array();
            }

            $collect = $pages[$page_name];

            array_push( $collect, $uid );
            $pages[$page_name] = array_unique($collect);
        }
    }

    return $pages;
}

function get_page_name( $path ){
    if( preg_match('/\.\/(\w+)\/content\/(.+?)\//', $path, $match) ){
        return $match;
    }
    return false;
}

function get_page_cache(){
    if( !file_exists( BISON_PAGE_LIST_CACHE ) || page_cache_expired() ){
        $pages = get_page_list();
        write_page_cache( $pages );
    }

    $json_str = file_get_contents( BISON_PAGE_LIST_CACHE );

    return (array) json_decode( $json_str );
}

function write_page_cache( $pages ){
    $tempname = tempnam( sys_get_temp_dir(), 'bison-page-list-');

    file_put_contents( $tempname, json_encode( $pages ) );

    unlink( BISON_PAGE_LIST_CACHE );
    rename( $tempname, BISON_PAGE_LIST_CACHE );
}

function normalize_page_name( $url ){
    $url_parsed = parse_url( $url );

    $page_name = isset( $url_parsed['path'] ) ? str_replace('/', '-', trim($url_parsed['path'], '/')) : 'start';

    return $page_name;
}

function get_page_url( $path_parts ) {
    $path_parts = array_merge( array('http:/', $_SERVER['SERVER_NAME'], 'user' ), func_get_args());
    $target = join( '/', $path_parts );

    return $target;
}

function get_page_urls( $page_name, array $user_ids ){
    $collect = array();

    foreach( $user_ids as $uid ){
        $collect[] = get_page_url( $uid, $page_name );
    }

    return $collect;
}

if( isset( $_GET['src'] ) ){
    $page_name = normalize_page_name( $_GET['src'] );
    $pages     = get_page_cache();
    $callback  = isset($_GET['pjson']) ? $_GET['pjson'] : 'bison_callback';

    if( isset($pages[$page_name]) ){
        $urls  = get_page_urls( $page_name, $pages[$page_name]);
        $jsonp = sprintf('%s(%s)', $callback, json_encode($urls));

        header( 'Content-Type: application/json');
        header( 'Content-Lenght: ' . strlen($jsonp) );
        echo $jsonp;

        exit();
    }
}
else{
    echo '{error: "you must provide a source url and a callback"}';
}
