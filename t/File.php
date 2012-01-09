<?php
define( 'T_BASE_DIR', dirname(__FILE__) );

require_once( T_BASE_DIR . '/../Glue/File.php');

use Glue\File;

class Glue_File extends PHPUnit_Framework_TestCase{

    public function testInfo(){
        $path = T_BASE_DIR . '/resource/tomato.jpg';

        $file = new File( $path );

        $expected = (object) array(
            'width' => '663',
            'height' => '800',
            'mime' => 'image/jpeg'
        );

        $this->assertEquals( $expected, $file->info, 'image info expected' );
    }

    public function testBasename(){
        $path = T_BASE_DIR . '/resource/tomato.jpg';

        $file = new File( $path );

        $expected = 'tomato.jpg';

        $this->assertEquals( $expected, $file->basename, 'image info expected' );
    }
}
