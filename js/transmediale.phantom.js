var domain = 'tm12.hotglue.org/hotglue2/tm/js';
var include = [
    'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js',
    'http://' + domain + '/inline.plugin.js',
    'http://' + domain + '/json2.min.js',
    'http://' + domain + '/glue.js',
];


var page = new WebPage(),
    url = 'http://www.transmediale.de';


page.open(url, function (status) {
    if (status !== 'success') {
        console.log('Unable to access network');
    }
    else {
        for( var i = 0, len = include.lenth; i < len; i++ ){
            page.injextJs(include[i]);
        }
    }
});
