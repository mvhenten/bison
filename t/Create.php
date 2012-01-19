<?php
define( 'T_BASE_DIR', dirname(__FILE__) );

require_once( T_BASE_DIR . '/../Glue/Create.php');
require_once( T_BASE_DIR . '/../Glue/Util.php');

use Glue\Util;
use Glue\Create;

class Glue_Create extends PHPUnit_Framework_TestCase{

    static $_tmpdirs = array();

    public function tearDown(){
        foreach( self::$_tmpdirs as $path ){
            exec( "rm -rf $path" );
        }
    }

    private function _tmpdir(){
        $name = tempnam( '.', 'bison_');
        unlink( $name );
        mkdir( $name );
        array_push( self::$_tmpdirs, $name );

        return $name;
    }

    private function _suportdir(){
        return BISON_SUPPORT_DIR . '/hotglue2/';
    }

    public function testSupportDir() {
        $this->assertFileExists( $this->_suportdir() );
    }

    public function testConstructor(){
        $target = $this->_tmpdir();

        $create = new Create( '' );
    }

    public function testBlackList(){
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

        foreach( $blacklisted as $file ){
            $this->assertNotContains( $file, $create->source_files );
        }
    }

    public function testSourceFiles() {
        exec('ls ' . BISON_SUPPORT_DIR . '/hotglue2/*.inc.php', $output );
        $output = array_map( 'basename', $output );

        $create = new Create('');


        foreach( $output as $file ){
            $this->assertContains( $file, $create->source_files );
        }
    }

    public function testCreate(){
        $target = $this->_tmpdir();

        $create = new Create($target);
        $create->create();

        foreach( $create->source_files as $file ){
            $this->assertFileExists( $target . '/' . $file );
        }

    }

}
