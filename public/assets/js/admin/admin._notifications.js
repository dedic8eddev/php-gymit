'use strict';

/**
 * Global Notification Fc
 * Call with N.show();
 * 
 * Accepts  type => (string)'success', 'error', 'info', 'warning',.. (bootstrap classes)
 *          text => (string)'lorem ipsum'
 *          sticky => (boolean) true (if supplied will remove timeout for the alert)
 *          reload => (boolean) true (will reload current page if true)
 * 
 * Settings can be changed below.
 * 
 * Author: Jan Dočekal (jan.docekal@bold-interactive.com)
 */

var N = N || (function () {
    return {
        settings: {
            layout: 'bottomRight',
            timeout: 4000,
            progressBar: true,
            killer: true,
            theme: 'bootstrap-v3',
            animation: {
                open: 'animated fadeInUp',
                close: 'animated fadeOutDown'
            }
        },
        show: function(type, text, sticky, reload){
            sticky = sticky || false;
            reload = reload || false;

            var s = this.settings;
                s.text = text;
                s.type = type;

            if(reload){
                s.callbacks = {
                    afterClose: function(){
                        window.location.reload();
                    }
                };
            }

            if(sticky){ s.timeout = false; }else{ s.timeout = 4000; }
            new Noty(s).show();
        }
    }
}());