'use strict';

var REPORTING = REPORTING || (function () {
    var self;
    return {
        init: function(){
            self = this;
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });
            this.fireEvents();
        },
        fireEvents: function(){

            $('input[type="date"]').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false
            });

            $('.modal').on('show.bs.modal', function (e) {
                let reportType = $(e.relatedTarget).data('report');
                $(this).find('.generateReport').attr('onClick', `REPORTING.generateReport(this,'${reportType}');`);
            });

            $('.modal').on('hidden.bs.modal', function (e) {
                $.each($(this).find('input, select'), function (i, input){
                    $(input).val('');
                })
            });            

        },
        generateReport: function(el,type){
            let from = $(el).closest('form').find('[name="from"]').val(),
                to = $(el).closest('form').find('[name="to"]').val();
            if(from && to) {
                if(from <= to){
                    window.open(`/admin/reporting/generate_${type}?from=${from}&to=${to}`, '_blank');
                } else N.show('error', 'Vybraný rozsah není správný, zkontrolujte správnost zadání!');
            } else N.show('error', 'Vyberte rozsah pro který generovat report!');                
        }
    }
}());

REPORTING.init();