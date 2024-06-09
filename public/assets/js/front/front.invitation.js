'use strict';

var INVITATION = INVITATION || (function () {
    var self;
    return {
        reg_submit: $('#submitRegistration'),
        user_form: $('#invitationForm'),
        init: function(params){
            self = this;
            NProgress.configure({ parent: '#invitationForm', minimum: 0.1, showSpinner: false });   
            this.fireEvents();
        },
        fireEvents: function(){
            this.reg_submit.click(function(e){
                e.preventDefault();
                var url = $(this).data("ajax");
                var password = self.user_form.find('input[name="password"]');

                if(! self.reg_submit.hasClass("disabled")){
                    var inputs = self.user_form.find('input:required, select:required');

                    $.each(inputs, function(i, input){
                        if($(input).val()){
                            $(input).removeClass("invalid");
                        }else{
                            if($(input).attr("type") != "checkbox") $(input).addClass("invalid");
                        }
                    });
    
                    if(!$('#agreement').is(':checked')){
                        $('#agreement').parent().find("label").addClass('invalid');
                    }else{
                        $('#agreement').parent().find("label").removeClass('invalid');
                    }

                    if($(password).val() == "" || $(password).val().length < 6){
                        if($(password).val().length < 6){
                            N.show('error', 'Heslo je příliš krátké (alespoň 6 znaků)!');
                        }else{
                            N.show('error', 'Musíte vyplnit heslo!');
                        }
                        $(password).addClass("invalid");
                    }else{
                        $(password).removeClass("invalid");
                    }
    
                    if(self.user_form.find(".invalid").length <= 0){
    
                        var formData = new FormData(),
                            form_inputs = self.user_form.find('input, select');
    
                        $.each(form_inputs, function(i, input){
                            formData.append($(input).attr('name'), $(input).val());
                        });
    
                        $.ajax({
                            url: url,
                            data: formData,
                            processData: false,
                            contentType: false,
                            type: "POST",
                            dataType: "json",
                            beforeSend: function(){
                                NProgress.start();
                                self.reg_submit.removeClass("btn-success");
                                self.reg_submit.addClass("disabled");
                                self.reg_submit.addClass("btn-dark");
                                self.reg_submit.attr("disabled", true);
                            },
                            success: function (res) {
                                if(!res.error){
                                    window.location.replace('/login');
                                }else{
                                    N.show('error', 'Nepodařilo se dokončit registraci, zkontrolujte správnost údajů a zkuste to znovu. Případně nás kontaktujte.');
                                }
    
                                NProgress.done();
                            }
                        });
    
                    }
                }
            });
        }
    }
}());

INVITATION.init();