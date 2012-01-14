
var url ='http://www.transmediale.de/sites/www.transmediale.de/themes/ninesixty/styles/tm12/webfiles/img12/titlebg.png';
s.match(/background-image:-webkit-gradient\(.+?;/g);

function stripWebkitBackgroundGradient(){
    var link = $('link[rel=stylesheet]').attr('href');
    var c;
    $('link[rel=stylesheet]').remove();
    
    $.get(link, function(e){ c = e;
        c = c.replace(/background-image:-webkit-linear-gradient\(.+?;/g, '');
        c = c.replace(/background-image: linear-gradient\(.+?;/g, '');
        c = c.replace(/background-image:-webkit-gradient.+?;/g, '');
        c = c.replace(/background-image:url\((.+?)\);/, 'background-image: url($1) !important;');
    
        c = c.replace('gradient','');
        
        var ref = document.createElement('style');
        ref.setAttribute("rel", "stylesheet");
        ref.setAttribute("type", "text/css");
        ref.appendChild(document.createTextNode(c));
        document.getElementsByTagName("head")[0].appendChild(ref);
    });    
}

$('h1').css('background-image');

$.get(link, function(e){ c = e;});

////////////

var ref = document.createElement('style');
ref.setAttribute("rel", "stylesheet");
ref.setAttribute("type", "text/css");
ref.appendChild(document.createTextNode("body { border: 3px solid #ff0; } #main h1, #main h2 {background:none !important;}"));
document.getElementsByTagName("head")[0].appendChild(ref);

ref.appendChild(document.createTextNode("body { border: 3px solid #f00; }"));

ref.appendChild(document.createTextNode("body { border: 3px solid #f00; } #main h1, #main h2 {background-image: url(http://www.transmediale.de/sites/www.transmediale.de/themes/ninesixty/styles/tm12/webfiles/img12/titlebg.png);}"));

document.getElementsByTagName("head")[0].appendChild(ref);


var m = c.match( /(+?){.+?background-image:.+?}/ );