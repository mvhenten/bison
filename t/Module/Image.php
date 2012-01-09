<?php
define( 'T_BASE_DIR', dirname(__FILE__) . '/../' );

require_once( T_BASE_DIR . '/../Glue/Module/Image.php');

use Glue\Module\Image;

define('JSON_STRING','{
   "elements":[
      {
         "style":{
            "font-size":"13px",
            "font-family":"arial, verdana, helvetica, sans-serif",
            "font-weight":"bold",
            "color":"rgb(22, 21, 243)",
            "letter-spacing":"normal",
            "width":"60px",
            "height":"26px",
            "float":"none",
            "display":"inline",
            "position":"static",
            "background-color":"rgba(0, 0, 0, 0)",
            "background-image":"none",
            "background-repeat":"repeat",
            "background-position":"0% 0%",
            "background-attachment":"scroll",
            "top":"19px",
            "left":"162px"
         },
         "properties":{
            "src":""
         },
         "type":"image",
         "text":""
      }
   ]
}');

class Glue_Module_Image extends PHPUnit_Framework_TestCase{
    private $json;

    protected function setUp(){
        $this->json = json_decode(JSON_STRING);//json_decode( file_get_contents(T_BASE_DIR . '/resource/tm.main.json') );
        $this->json->elements[0]->properties->src = T_BASE_DIR . '/resource/tomato.jpg';
    }

    public function testToString(){
        $module = new Image( null, $this->json->elements[0] );

        $expected = <<<EOF
type:image
module:image
image-background-repeat:repeat
image-file:tomato.jpg
image-file-mime:image/jpeg
image-file-width:663
image-file-height:800
object-height:26px
object-left:162px
object-top:19px
object-width:60px
text-background-color:rgba(0, 0, 0, 0)
text-font-color:rgb(22, 21, 243)
text-font-family:arial, verdana, helvetica, sans-serif
text-font-size:13px
text-font-weight:bold
text-letter-spacing:normal
EOF;

        $this->assertEquals( $expected, (string) $module, 'string looks expected' );
    }

}
