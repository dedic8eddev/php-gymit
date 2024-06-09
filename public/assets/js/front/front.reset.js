'use strict';

var RESET = RESET || (function () {
    var self;
    return {
        reset_submit: $('#submitReset'),
        reset_form: $('#resetForm'),
        reset_finish_form: $('#resetFinishForm'),
        reset_finish_submit: $('#submitResetChange'),
        init: function(params){
            self = this;
            this.fireEvents();
        },
        validEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        fireEvents: function(){
            this.reset_submit.click(function(e){
                e.preventDefault();
                var url = $(this).data("ajax");
                var email = self.reset_form.find('input[name="email"]');

                if(! self.reset_submit.hasClass("disabled")){
                    if(!$(email).val() || !self.validEmail($(email).val())){
                        N.show('error', 'Chybí e-mailová adresa nebo je v nesprávném formátu!');
                        $(email).addClass("invalid");
                    }else{
                        $(email).removeClass("invalid");
    
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {"email":$(email).val()},
                            dataType: "json",
                            beforeSend: function(){
                                self.reset_submit.removeClass("btn-success");
                                self.reset_submit.addClass("disabled");
                                self.reset_submit.addClass("btn-dark");
                                self.reset_submit.attr("disabled", true);
                            },
                            success: function (res) {
                                if(!res.error){
                                    N.show('success', 'Na Váš e-mail byl odeslán odkaz pro resetování hesla, vyčkejte na jeho příchod, případně zkontrolujte složku spam.');
                                }else{
                                    N.show('error', res.error);
                                }
                            }
                        });
                    }
                }

            });

            this.reset_finish_submit.click(function(e){
                e.preventDefault();
                var url = $(this).data("ajax");
                var password = self.reset_finish_form.find('input[name="password"]');
                var token = self.reset_finish_form.find('input[name="token"]');

                if(! self.reset_finish_submit.hasClass("disabled")){
                    if(!$(password).val() || $(password).val().length < 6){
                        if($(password).val().length < 6){
                            N.show('error', 'Heslo je příliš krátké (alespoň 6 znaků)!');
                        }else{
                            N.show('error', 'Musíte vyplnit heslo!');
                        }
                        $(password).addClass("invalid");
                    }else{
                        $(password).removeClass("invalid");
    
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {"password": $(password).val(), "token": $(token).val()},
                            dataType: "json",
                            beforeSend: function(){
                                self.reset_finish_submit.removeClass("btn-success");
                                self.reset_finish_submit.addClass("disabled");
                                self.reset_finish_submit.addClass("btn-dark");
                                self.reset_finish_submit.attr("disabled", true);
                            },
                            success: function (res) {
                                if(!res.error){
                                    window.location.replace('/login');
                                }else{
                                    N.show('error', 'Nepodařilo se změnit Vaše heslo, zkuste to prosím znovu nebo nás kontaktujte.');
                                }
                            }
                        });
                    }
                }
            });
        }
    }
}());

RESET.init();