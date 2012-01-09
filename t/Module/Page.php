<?php
define( 'T_BASE_DIR', dirname(__FILE__) . '/../' );

require_once( T_BASE_DIR . '/../Glue/Module/Page.php');

use Glue\Module\Page;

define('JSON_STRING','{
   "title":"in/compatible | transmediale",
   "style":{
      "background-color":"rgb(235, 235, 235)",
      "background-image":"",
      "background-repeat":"repeat",
      "background-position":"0% 0%",
      "background-attachment":"fixed"
   }
}');


class Glue_Module_Page extends PHPUnit_Framework_TestCase{
    private $json;

    protected function setUp(){
        $property = 'background-image';

        $this->json = json_decode( file_get_contents(T_BASE_DIR . '/resource/tm.main.json') );
        $this->json->style->$property = T_BASE_DIR . '/resource/tomato.jpg';
    }

    public function testToString(){
        //var_dump( $this->json->elements[1]->style );

        $module = new Page( null, $this->json );
        $expected = <<<EOF
page-background-color:rgb(235, 235, 235)
page-title:in/compatible | transmediale
page-background-file:tomato.jpg
page-background-mime:image/jpeg
page-background-attachment:fixed
EOF;

        $this->assertEquals( trim($expected), (string) $module, 'string looks expected' );    }

}
