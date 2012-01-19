<?php
/**
 * t/Create.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


define( 'T_BASE_DIR', dirname(__FILE__) );

require_once T_BASE_DIR . '/../Glue/Create.php';
require_once T_BASE_DIR . '/../Glue/Util.php';

use Glue\Util;
use Glue\Create;

class Glue_Create extends PHPUnit_Framework_TestCase{

    static $_tmpdirs = array();

    /**
     * remove all tmp dirs
     */
    public function tearDown() {
        foreach ( self::$_tmpdirs as $path ) {
            exec( "rm -rf $path" );
        }
    }


    /**
     * tmp dir
     *
     * @return unknown
     */
    private function _tmpdir() {
        $name = tempnam( '.', 'bison_');
        unlink( $name );
        mkdir( $name );
        array_push( self::$_tmpdirs, $name );

        return $name;
    }


    /**
     * test for existence of support dir in default setup
     */
    public function testSupportDir() {
        $this->assertFileExists( BISON_SUPPORT_DIR . '/hotglue2/' );
    }


    /**
     * will it float
     */
    public function testConstructor() {
        $target = $this->_tmpdir();

        $create = new Create( '' );
    }


    /**
     * Must blacklist these files
     */
    public function testBlackList() {
        $blacklisted = array(
            '.',
            '..',
            'index.php',
            'content',
            'htaccess-dist',
            '.htaccess',
            'user-config.inc.php'
        );

        $create = new Create('');

        foreach ( $blacklisted as $file ) {
            $this->assertNotContains( $file, $create->source_files );
        }
    }


    /**
     * source_files array must contain files from the original
     */
    public function testSourceFiles() {
        exec('ls ' . BISON_SUPPORT_DIR . '/hotglue2/*.inc.php', $output );
        $output = array_map( 'basename', $output );

        $create = new Create('');


        foreach ( $output as $file ) {
            $this->assertContains( $file, $create->source_files );
        }
    }


    /**
     * create must create actual files in an expected location
     */
    public function testRun() {
        $target = $this->_tmpdir();

        $create = new Create($target);
        $create->run();

        foreach ( $create->source_files as $file ) {
            $this->assertFileExists( $target . '/' . $file );
        }

    }


}
