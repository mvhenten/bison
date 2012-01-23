//"use strict";
var glue_me = ( function () {
    /**
     * Hand-picked selectors from transmediale
     *
     */
    var selectors = [
        'p,img,a:visible',
        'h1:visible,h2,h3',
        'div.cloud',
        'div.views-row',
        'div.festival-menu',
        'div.festivalmenuhead',
        'div.festival-col',
        'div.field',
        '#block-block-219',
        '.blue',
        '.goldheader',
        '.goldtext',
        '#sidebar-right .content',
        '#sidebar-right .block',
        '.buildmode-full',
        '.field-ds-group-bluegroup'
    ].join(',');

    /**
     * Get rid of HTML comments
     */
    $('*').contents().each(function() {
        if(this.nodeType == 8) {
            $(this).remove();
        }
    });

    //$('script').remove();

    return {
        go: function(){

            var page = {
                title: $('title').text(),
                style: $('body').collectCSS()[0].style,
                elements: this.collect()
            };

            return JSON.stringify(page);
        },

        processCollect: function( obj, collector ){
            var offset = $(obj.element).offset();

            obj.style.top  = offset.top + 'px';
            obj.style.left = offset.left + 'px';
            obj.properties = {};
            var collect = true; // quick hack see below

            switch( obj.element.tagName.toLowerCase() ){
                case 'img':
                    obj.properties.src  = obj.element.src;
                    obj.type = 'image';
                    obj.text = '';
                    break;
                case 'a':
                    obj.properties.href = obj.element.href;
                    obj.type = 'link';
                    obj.text = $(obj.element).text();
                    break;
                case 'div':
                    /**
                     * ok some hacks here
                     *
                     * - filter out divs that have background images
                     * - fiter out divs that have background colors
                     * - all other stuff is not interesting.
                     */
                    if( obj.style['background-image'] != 'none' ){
                        var src = obj.style['background-image'];
                        src = /url\((.+?)\)/.exec(src);

                        if( src && src.length === 2 ){
                            var objCopy = {
                                type: 'image',
                                text: '',
                                style: obj.style, //NB need to do something with z-index
                                properties: {
                                    src: src[1]
                                }
                            };
                            collector.push(objCopy);
                        }
                        var clone = $(obj.element).clone();
                        $(clone).find('a,img,p,h1,h2,h3,h4').remove();
                        obj.text = $(clone).html();
                        obj.type = 'text';
                    }
                    else if( obj.style['background-color'] != 'rgba(0, 0, 0, 0)' ){
                        var clone = $(obj.element).clone();
                        $(clone).find('a,img,p,h1,h2,h3,h4').remove();
                        obj.text = $(clone).html();
                        obj.type = 'text';
                    }
                    else{
                        collect = false;
                    }

                    break;
                case 'h1':
                    /**
                     * WARNING this is a huge hack to work around -webkit- background
                     * images, wich obfuscate the background image fallback
                     * given in this instance. It simply assumes all H1 tags on the
                     * transmediale site carry this background!
                     */
                    var objCopy = {
                        type: 'image',
                        text: '',
                        style: obj.style, //NB need to do something with z-index
                        properties: {
                            src: 'http://www.transmediale.de/sites/www.transmediale.de/themes/ninesixty/styles/tm12/webfiles/img12/titlebg.png'
                        }
                    };
                    collector.push(objCopy);
                    // NO BREAK intentionally!
                default:
                    var clone = $(obj.element).clone();
                    $(clone).find('a,img,p,h1,h2,h3,h4').remove();
                    obj.text = $(clone).html();
                    obj.type = 'text';
                    break;
            }

            if( collect ){
                delete(obj.element);
                collector.push(obj);
            }
        },

        collect: function(){
            this.sanitizeImages();
            var collect = [];
            var self    = this;

            $( $(selectors).collectCSS() ).each(function(i, obj ){
                self.processCollect( obj, collect );
            });

            return $.makeArray(collect);
        },

        sanitizeImages: function(){
            /* need to set src attribute explicitly */
            $('img').each(function( i, el ){
                $(el).attr('src', el.src );
            });
        }
    };
})();
