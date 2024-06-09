'use strict';

var TRUMBOWYG = TRUMBOWYG || (function () {
    return {
        trumbowygSelector: null,
        init: function(params){
            $.trumbowyg.svgPath = '/public/assets/css/libs/trumbowyg/icons.svg';
            this.trumbowygSelector = $('.js-trumbowyg-editor').trumbowyg({
                lang: 'cs',
                imageWidthModalEdit: true,
                removeformatPasted: true,
                btnsDef: {
                    customFormatting: {
                        dropdown: ['p', 'h1', 'h2', 'h3'],
                        ico: 'p'
                    }
               },
                btns: [
                    ['undo', 'redo'], // Only supported in Blink browsers
                    ['customFormatting'],
                    ['strong', 'em'],
                    ['link'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['removeformat'],

                ]
            }).on('tbwfocus',function(){
                if($(this).trumbowyg('html') == ''){
                    setTimeout(function(){
                        document.execCommand('formatBlock', false, '<p>');
                    },100);
                }
            })
            .on('tbwchange',function(){

                //notSavedAlertCheck = false;

                if($(this).trumbowyg('html') == ''){
                    document.execCommand('formatBlock', false, '<p>');
                }
            });
        },
        clear: function()
        {
            this.trumbowygSelector.each(function(index,item){
                $(item).trumbowyg('empty');
            });
        }
    }
}());

TRUMBOWYG.init();