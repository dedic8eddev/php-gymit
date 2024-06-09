'use strict';

var PROFILE = PROFILE || (function () {
    var self;
    return {
        strength: {
            0: "20%",
            1: "35%",
            2: "50%",
            3: "80%",
            4: "100%"
        },
        password_input: document.getElementById('password'),
        password_meter: $('.progress-bar'),
        profile_form: $('#profileForm'),
        password_form: $('#passwordForm'),
        billing_form: $('#billingForm'),
        init: function(){
            self = this;
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   
            this.fireEvents();
        },
        fireEvents: function(){   
            this.password_input.addEventListener('input', function() {
              var val = self.password_input.value;
              var result = zxcvbn(val);

              // % score
              var score = self.strength[result.score];
              if(val !== ""){
                self.password_meter.css("width", score);
              }else{
                self.password_meter.css("width", "0%");
              }
            });

            this.profile_form.find('.btn').click(function(e){
                e.preventDefault();

                var form_data = new FormData();
                var ajax_url = $(this).data("url");

                var inputs = self.profile_form.find('input');
                $.each(inputs, function(i, input){
                    if($(input).prop("required") && ( $(input).val() === "" || !$(input).val() )){
                        $(input).addClass('invalid');
                    }else{
                        $(input).removeClass('invalid');
                    }
                });

                if(self.profile_form.find('.invalid').length <= 0){

                    $.each(inputs, function(i, input){
                        form_data.append($(input).attr('name'), $(input).val());
                    });

                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: form_data,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function(){
                            NProgress.start();
                        },
                        success: function (res) {
                            if(!res.error){
                                N.show('success', 'Změny byly úspěšně uloženy!');
                            }else{
                                N.show('error', 'Nepovedlo se uložit Váš profil, zkuste to prosím znovu.');
                            }

                            NProgress.done();
                        }
                    });
                }else{
                    N.show('error', 'Povinné údaje chybí nebo některá pole obsahují chyby. Zkontrolujte červeně zvýrazněná pole.');
                }
            });

            this.password_form.find('.btn').click(function(e){
                e.preventDefault();

                var form_data = new FormData();
                var ajax_url = $(this).data("url");

                var inputs = self.password_form.find('input');
                if($(inputs[0]).val() === "" || $(inputs[1]).val() === ""){
                    N.show('error', 'Jedno nebo obě pole pro heslo jsou prázdné!');
                }else{
                    if($(inputs[0]).val() != $(inputs[1]).val()){
                        N.show('error', 'Hesla se neshodují!');
                        $(inputs[0]).addClass('invalid');
                        $(inputs[1]).addClass('invalid');
                    }else{
                        $(inputs[0]).removeClass('invalid');
                        $(inputs[1]).removeClass('invalid');
                    }
    
                    if(self.password_form.find('.invalid').length <= 0){
    
                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {password: $(inputs[0]).val()},
                            dataType: "json",
                            beforeSend: function(){
                                NProgress.start();
                            },
                            success: function (res) {
                                if(!res.error){
                                    N.show('success', 'Heslo úspěšně změněno!');

                                    // reset
                                    $(inputs[0]).val('');
                                    $(inputs[1]).val('');
                                    self.password_meter.css("width", "0%");
                                }else{
                                    N.show('error', 'Nepovedlo se změnit heslo, zkuste to prosím znovu.');
                                }

                                NProgress.done();
                            }
                        });
                    }else{
                        N.show('error', 'Povinné údaje chybí nebo některá pole obsahují chyby. Zkontrolujte červeně zvýrazněná pole.');
                    }
                }
            });
        }
    }
}());

PROFILE.init();