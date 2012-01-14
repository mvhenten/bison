var fs = require('fs');

var page = new WebPage(),
    url         = phantom.args[0],
    outputFile  = phantom.args[1];

page.onConsoleMessage = function (msg) {
    console.log(msg);
};

page.open(url, function (status) {
    if (status !== 'success') {
        console.log('Unable to access network');
    }
    else {
        var script_includes = [
            'vendor/jquery.js',
            'vendor/jquery-ui.js',
            'vendor/inline.plugin.js',
            'vendor/json2.js',
            'glue.js',
        ];

        for ( var i = 0, len = script_includes.length; i < len; i++ ){
            page.injectJs(script_includes[i]);
            console.log(script_includes[i]);
        }

        var json = page.evaluate(function(){
            var url = $('link[rel=stylesheet]').attr('href');
            var content;

            var makeHotGlueObj = function( url, style ){
                var hotGlueObj = {
                    type: 'image',
                    text: '',
                    style: style,
                    properties: {
                        src: url
                    }
                }

                return hotGlueObj;
            };

            var collectElement = function( sel, url, collect ){
                $(sel).each(function(i, el){
                    var offset = $(el).offset(),
                        height = $(el).css('height'),
                        width  = $(el).css('width');

                    if( height !== 'auto' ){
                        collect.push( makeHotGlueObj( url, {
                                top:    offset.top + 'px',
                                left:   offset.left + 'px',
                                width:  $(el).css('width'),
                                height: $(el).css('height')
                            }
                        ));
                    }
                });
            };

            var collectBackgroundImg = function( content ){
                var s = content.split(/{|}/);
                var collect = [];

                for( var i = 0, len = s.length-1; i < len; i+=2 ){
                    var sel = s[i],
                        css = s[i+1];

                    var url = css.match(/background-image:[\s+]?url\((.+?)\)/);
                    if( url !== null && sel !== null &&  !sel.match(/:hover/) ){
                        collectElement( sel, url, collect );
                    }
                }

                var page = {
                    title: $('title').text(),
                    style: $('body').collectCSS()[0].style,
                    elements: this.collect()
                };

                var JSONStr = JSON.stringify(page);
                console.log(JSONStr);

                return JSONStr;
            }

            $.get(url, collectBackgroundImg );
        });
    }
    phantom.exit();
});
