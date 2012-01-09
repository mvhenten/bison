<?php
define( 'T_BASE_DIR', dirname(__FILE__) . '/../' );

require_once( T_BASE_DIR . '/../Glue/Module/Text.php');

use Glue\Module\Text;

define('JSON_STRING','{
   "elements":[
      {
         "style":{
            "font-size":"13px",
            "font-family":"arial, verdana, helvetica, sans-serif",
            "font-weight":"normal",
            "color":"rgb(0, 0, 0)",
            "letter-spacing":"normal",
            "width":"220px",
            "height":"114px",
            "float":"none",
            "display":"block",
            "position":"static",
            "background-color":"rgba(0, 0, 0, 0)",
            "background-image":"none",
            "background-repeat":"repeat",
            "background-position":"0% 0%",
            "background-attachment":"scroll",
            "top":"1231px",
            "left":"402px"
         },
         "properties":{

         },
         "type":"link",
         "text":"<div class=\"xtra\"><i>hello,</i>\n go to <a href=\"#here\">if</a> <strong>text</strong>"
      }
   ]
}');

class Glue_Module_Text extends PHPUnit_Framework_TestCase{
    private $json;

    protected function setUp(){
        $this->json = json_decode(JSON_STRING);
    }

    public function testToString(){
        $module = new Text( null, $this->json->elements[0] );

        $expected = <<<EOF
type:text
module:text
object-height:114px
object-left:402px
object-top:1231px
object-width:220px
text-background-color:rgba(0, 0, 0, 0)
text-font-color:rgb(0, 0, 0)
text-font-family:arial, verdana, helvetica, sans-serif
text-font-size:13px
text-font-weight:normal
text-letter-spacing:normal

<i>hello,</i> go to <a href="#here">if</a> <strong>text</strong>
EOF;

        $this->assertEquals( $expected, (string) $module, 'string looks expected' );
    }

}
