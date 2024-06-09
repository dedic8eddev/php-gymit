'use strict';

/**
 * Global System Javascript
 */

var MAIN = MAIN || (function () {
    var self;
    return {
        _post: function(url, data){
            return $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                success: function (res) {}
            });
        },
        /**
         * Send a GET ajax request
         */
        _get: function(url){
            return $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function (res) {}
            });
        },
        /**
         * Function that retrieves a cookie by a name
         * @param {string} name     the string name of the cookie
         */
        _cookie: function(name){
            var cookiestring = RegExp(""+name+"[^;]+").exec(document.cookie);
            return decodeURIComponent(!!cookiestring ? cookiestring.toString().replace(/^[^=]+./,"") : "");
        },
        /**
         * Function that retrieves users role
         */
        _user: async () => {
            const req = await fetch('/home/get_user');
            const res = await req.json();

            return res.user;
        },
        init: function(){
            self = this;
        }
    }
}());

MAIN.init();