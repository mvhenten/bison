<?php
define( 'T_BASE_DIR', dirname(__FILE__) . '/../' );

require_once( T_BASE_DIR . '/../Glue/Module/Link.php');

use Glue\Module\Link;

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
            "height":"16px",
            "float":"none",
            "display":"inline",
            "position":"static",
            "background-color":"rgba(25, 233, 234, 56)",
            "background-image":"none",
            "background-repeat":"repeat",
            "background-position":"0% 0%",
            "background-attachment":"scroll",
            "top":"32px",
            "left":"162px"
         },
         "properties":{
            "href":"http://www.google.com/doodles"
         },
         "type":"link",
         "text":"I am feeling lucky"
      }
   ]
}');

class Glue_Module_Image extends PHPUnit_Framework_TestCase{
    private $json;

    protected function setUp(){
        $this->json = json_decode(JSON_STRING);
    }

    public function testToString(){
        $module = new Link( null, $this->json->elements[0] );

        $expected = <<<EOF
type:text
module:text
object-link:http://www.google.com/doodles
object-height:16px
object-left:162px
object-top:32px
object-width:60px
text-background-color:rgba(25, 233, 234, 56)
text-font-color:rgb(22, 21, 243)
text-font-family:arial, verdana, helvetica, sans-serif
text-font-size:13px
text-font-weight:bold
text-letter-spacing:normal

I am feeling lucky
EOF;

        $this->assertEquals( $expected, (string) $module, 'string looks expected' );
    }

}
