'use strict';

var SITE_SETTINGS = SITE_SETTINGS || (function () {
    var self;
    return {
        init: function(){
            self = this;
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });
            this.fireEvents();
        },
        fireEvents: function(){

            $('.save-site-settings').click(function(){
                var current_site = $("#current_site").val();

                GYM._post("/admin/site_settings/save", {current_site: current_site}).done(function(res){
                    if(!res.error) N.show("success", "Nastavení změněno!");
                    else N.show("error", "Nepovedlo se uložit nastavení!");
                });
            });     

        }
    }
}());

SITE_SETTINGS.init();