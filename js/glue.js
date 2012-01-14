"use strict";
var glue_me = ( function () {
    var selectors = 'img,a:visible,p,h1,.festival-col, .view-content, #sidebar-right';

    $('*').contents().each(function() {
        if(this.nodeType == 8) {
            $(this).remove();
        }
    });

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
                default:
                    if( obj.style['background-image'] != 'none' ){
                        try {
                            var src = obj.style['background-image'];
                            console.log(src);
                            src = /url\((.+?)\)/.exec(src)[1];
                            
                            var objCopy = {
                                type: 'image',
                                text: '',
                                stle: obj.style,
                                properties: {
                                    src: src
                                }
                            }
                                                        
                            collect.push(objCopy);                            
                        }
                        catch(e){
                            console.log(e);
                        }
                    }                    
                    
                    //$(obj.element).find('*').inlineCSS();
                    var clone = $(obj.element).clone();

                    $(clone).find('a,img,p').remove();
                    obj.text = $(clone).html();
                    obj.type = 'text';
                    break;
            }

            delete(obj.element);
            collector.push(obj);
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
