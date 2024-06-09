'use strict';

var USER_DETAIL = USER_DETAIL || (function () {
    var self;
    return {
        save_submit_btn: $('.save-user-submit'),
        user_form: $('#saveUserForm'),
        remove_btn: $('.remove-user'),
        activate_btn: $('.activate-user'),
        role: null,
        init: async function(params){
            self = this;
            this.role = await GYM._role();
            this.fireEvents();
        },
        fireEvents: function(){

            $("body").on("click", ".remove-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete deaktivovat tohoto uživatele?');
                if(agreed){

                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl deaktivován!');
                            
                            $('.remove-user').text("Aktivovat uživatele");
                            $('.remove-user').removeClass("btn-danger");
                            $('.remove-user').addClass("btn-success");
                            $('.remove-user').removeClass("remove-user");
                            $('.remove-user').addClass("activate-user");

                        }else{
                            N.show('error', 'Nepovedlo se deaktivovat uživatele, zkuste to prosím znovu.');
                        }
                    });
                    
                }
            });
            $("body").on("click", ".activate-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete znovu aktivovat tohoto uživatele?');
                if(agreed){

                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl aktivován!');
                            
                            $('.activate-user').text("Deaktivovat uživatele");
                            $('.activate-user').addClass("btn-danger");
                            $('.activate-user').removeClass("btn-success");
                            $('.activate-user').addClass("remove-user");
                            $('.activate-user').removeClass("activate-user");

                        }else{
                            N.show('error', 'Nepovedlo se aktivovat uživatele, zkuste to prosím znovu.');
                        }
                    });

                }
            });

            self.save_submit_btn.click(function(e){
                e.preventDefault();
                var inputs = self.user_form.find('input:required, select:required'),
                    id = $(this).data("id"),
                    ajax_url = $(this).data("ajax");

                $.each(inputs, function(i, input){
                    if($(input).val()){
                        $(input).removeClass("invalid");
                    }else{
                        $(input).addClass("invalid");
                    }
                });

                if(self.user_form.find(".invalid").length <= 0){
                    var formData = new FormData(),
                        form_inputs = self.user_form.find('input, select');

                    $.each(form_inputs, function(i, input){
                        formData.append($(input).attr('name'), $(input).val());
                    });

                    formData.append('user_id', id);

                    GYM._upload(ajax_url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatelský účet byl úspěšně upraven!');
                        }else{
                            N.show('error', 'Nepovedlo se uložit změny, zkuste to prosím znovu.');
                        }
                    });

                }else{
                    N.show('error', 'Povinné údaje chybí nebo některá pole obsahují chyby. Zkontrolujte červeně zvýrazněná pole.');
                }

            });

        }
    }
}());

USER_DETAIL.init();