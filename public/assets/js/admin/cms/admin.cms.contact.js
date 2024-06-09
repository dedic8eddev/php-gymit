'use strict';

var CONTACT = CONTACT || (function () {
    var self;
    return {
        general_info_form: '#generalInfoForm',
        btn_submit_general_info_form: $('#submitGeneralInfo'),

        opening_hours_form: '#openingHoursForm',
        btn_submit_opening_hours_form: $('#submitOpeningHours'),             
        
        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),        
        
        dateFilterParamas: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat,
            invalidPlaceholder:"(invalid date)"
        },             

        role: false,
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.fireEvents();
        },
        fireEvents: function(){

            $('body').on('click', '[data-toggle="modal"]', function(){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2();
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                });
            });  

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });

            self.btn_submit_general_info_form.click(function(){
                $(self.general_info_form).submit();
            });

            self.btn_submit_opening_hours_form.click(function(){
                $(self.opening_hours_form).submit();
            });          

            // flatpickr
            $(".js-time-input").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: flatpickrTimeFormat,
                time_24hr: flatpickr24hr
            });                         

            $('#cmsContactPage').on('submit', self.general_info_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Základní informace byly úspěšně uloženy!',
                    error_text: 'Nepodařilo se uložit základní informace, zkontrolujte údaje nebo to zkuste znovu!',
                });
            });        

            $('#cmsContactPage').on('submit', self.opening_hours_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Otevírací hodiny byly úspěšně uloženy!',
                    error_text: 'Nepodařilo se uložit otevírací hodiny, zkontrolujte údaje nebo to zkuste znovu!',
                });
            });                

        },
    }
}());

CONTACT.init();