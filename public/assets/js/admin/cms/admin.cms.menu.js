'use strict';

var MENU = MENU || (function () {
    var self;
    return {
        menu_form: '#menuForm',
        btn_submit_menu_form: $('#submitGeneralInfo'),  

        role: false,
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.fireEvents();
        },
        fireEvents: function(){
     

            $('#cmsMenuPage').on('submit', self.menu_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Položky menu byly úspěšně uloženy!',
                    error_text: 'Nepodařilo se uložit položky menu, zkontrolujte údaje nebo to zkuste znovu!',
                });
            });                

        },
    }
}());

MENU.init();