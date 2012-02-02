<?php

#@list( $script, $in, $out ) = $argv;

ini_set('display_errors', "false");
ini_set('display_warnings', "false");

//define( 'BISON_WWW_PATH', '/home/matthijs/tmp' );
define( 'BISON_WWW_PATH', '/home/hotglue/www-transglue' );
//define( 'BISON_PAGE_LIST_CACHE', sys_get_temp_dir() . '/bison-page-list.json');

define( 'BISON_INSTANCE_MAX_AGE', '-6 hours' );
//define( 'BISON_INSTANCE_MAX_AGE', '-2 minutes' );
define( 'BISON_MIN_PAGE_COUNT', 4 );

// list all user dirs
// loop over all user dirs
// for each user dir, find the latest mtime
// for each user dir, find the pages for that dir
// check the amount of pages in page cache
// if there are enough pages
// and mtime < max_age
// collect this user id
// delete it - it's old

function list_user_dirs(){
    chdir( BISON_WWW_PATH . '/user/' );
    $cmd = 'find . -maxdepth 1 -type d -printf "%A@ %p\n"';

    exec( $cmd, $output, $status );
	
    usort( $output, 'sort_find_line' );

    $collect = array();
	

    foreach( $output as $str ){
        list($time, $path) = explode(' ', $str );
        $collect[] = $path;
    }

    return $collect;	
}

function user_dir_pages( $user_dir ){
    chdir( BISON_WWW_PATH . '/user/' );
	
	$cmd = sprintf('find %s/content/ -maxdepth 1 -type d', escapeshellarg( $user_dir ) );
	exec( $cmd, $output, $status );
	
	return $output;
}

function remove_user_dir( $user_dir ){
	@list( $dot, $uid ) = explode( '/', $user_dir );	
	
	$path = BISON_WWW_PATH . '/user/' . $uid;	
	$cmd = sprintf('rm -rf %s', escapeshellarg($path) );
	exec($cmd);
}

function sort_find_line( $a, $b ){
    $sa = explode(' ', $a);
    $sb = explode(' ', $b);

    return $sa[0] < $sb[0] ? 1 : -1;
}

function user_dir_mtime( $user_dir ){
    chdir( BISON_WWW_PATH . '/user/' );
	
	
	$cmd = sprintf('find %s/content/ -type f -printf "%%A@\n"', escapeshellarg( $user_dir ) );
	exec( $cmd, $output, $status );
	
	$out = array_map( 'intval', $output );
	
	return $output;
}

function user_dir_page_mtime( $user_dir, $page_dir ){
    chdir( BISON_WWW_PATH . '/user/' );
	
	$cmd = sprintf('find %s/content/%s -type f -printf "%%A@\n"',
		escapeshellarg( $user_dir ), escapeshellarg( $page_dir ) );
	
	exec( $cmd, $output, $status );
	
	$out = array_map( 'intval', $output );
	
	return $output;	
}

function user_dir_pages_check( $user_dir, &$page_cache ){
	$user_pages = user_dir_pages( $user_dir );
	
	foreach( $user_pages as $str ){
		@list( , $uid, , $page_name ) = explode( '/', $str );
		if( $page_name == 'cache' || $page_name == '' ) continue;
		
		if( !isset($page_cache[$page_name]) ){
			$page_cache[$page_name] = 0;
		}
		
		$mtimes = user_dir_page_mtime( $user_dir, $page_name );
		
		$min_mtime = min($mtimes);
		$max_mtime = max($mtimes);
		
		$mod_delta = $max_mtime - $min_mtime;
		
		if( $mod_delta > 5 ){
			echo "PAGE HAS MODIFICATIONS: $uid/$page_name\n";
			echo "MOD_DELTA: $mod_delta\n";			
		}
		
		//if( count(array_unique($mtimes)) > 20 ){
		//	echo "PAGE HAS MODIFICATIONS: $uid/$page_name\n";
		//}
			
		
		if( $page_cache[$page_name] < BISON_MIN_PAGE_COUNT ){
			$page_cache[$page_name] += 1;
			return false;
		}
	}
	
	return true;
}

$page_cache = array();
$user_dirs  = list_user_dirs();
$collect    = array();
$max_age    = strtotime( BISON_INSTANCE_MAX_AGE );

foreach( $user_dirs as $mtime => $user_dir ){
	if( $user_dir == '.' ) continue;
	
	$mtimes = user_dir_mtime( $user_dir );
	
//	echo count($mtimes) . "\n";
	
	$max_mtime 	= max($mtimes);
	$diff_count = count(array_unique($mtimes));
	
//	echo "USER: $user_dir, DIFF: $diff_count\n";
	
	if( ( ($max_mtime < $max_age) && $diff_count < 300 ) ){
		if( user_dir_pages_check( $user_dir, $page_cache ) ){
			$collect[] = $user_dir;
		}
	}	
}

foreach( $collect as $user_dir ){
	echo "removing $user_dir\n";
//	remove_user_dir( $user_dir );	
}

@unlink(BISON_PAGE_LIST_CACHE);
