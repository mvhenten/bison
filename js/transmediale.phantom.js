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
            return glue_me.go();
        });

        fs.write( outputFile, json, 'w' );
    }
    phantom.exit();
});
