'use strict';

var CLIENT_DETAIL = CLIENT_DETAIL || (function () {
    var self;
    return {
        save_submit_btn: $('.save-user-submit'),
        user_id: $('#user_id').val(),
        user_form: $('#saveClientForm'),

        transactions_history_table: '#transactionsHistoryTable',
        transactions_history_table_url: $('#transactionsHistoryTable').data("ajax"),
        membership_benefits_usage_table: '#membershipBenefitsUsageTable',
        membership_benefits_usage_table_url: $('#membershipBenefitsUsageTable').data("ajax"),
        purchased_items_table: '#purchasedItemsTable',
        purchased_items_table_url: $('#purchasedItemsTable').data("ajax"),                   

        sub_payment_table:'#subscriptionPaymentTable',

        remove_btn: $('.remove-user'),
        activate_btn: $('.activate-user'),

        forbid_access_popover_options: {
            title: '<span>Zadejte důvod</span><a href="javascript:;" class="close" onclick="$(\'.btn-forbid-access\').popover(\'hide\');">&times;</a>',
            placement: 'top',
            html: true,
            content: function() { return $('#forbid-access-popover').html(); }
        },

        role: null,
        init: async function(params){
            self = this;
            this.role = await GYM._role();

            this.initTransactionHistoryTable();
            this.initMembershipBenefitsUsageTable();
            this.initPurchasedItemsTable();
            this.initSubPaymentsTable();

            this.fireEvents();
        },
        fireEvents: function(){
            GYM._media(); // init media

            self.initTableSelects();

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('CLIENT_DETAIL.' + $(this).data('table'));
                table.clearSort();
                table.clearFilter(true);
                table.setFilter([{field:"clientId", type:"=", value:$('#user_id').val()}]);
                self.initTableSelects();                
            });               

            $('#v-pills-client-transactions-tab').click(function(){ self.transactions_history_table.setData(self.transactions_history_table_url); });
            $('#v-pills-benefits-tab').click(function(){ self.membership_benefits_usage_table.setData(self.membership_benefits_usage_table_url); });
            $('#v-pills-purchased-items-tab').click(function(){ self.purchased_items_table.setData(self.purchased_items_table_url); });

            $('#v-pills-sub-payments-tab').click(function(){
                GYM._post('/admin/payments/get_client_subscription_info_ajax', {client_id: $('#client_id').text()}).done(function(res){
                    if(res.data !== null){
                        self.sub_payment_table.setData(res.data.transactions);
                        self.sub_payment_table.redraw(true);   
                    } else $('#subscriptionPaymentTable').replaceWith('<h5 class="text-center my-3">Klient nemá žádné očekávané platby</h5>');
                });
            });    
            
            $('.lessRequired').change(function(){
                $('.lessRequired').not(this).each(function(){
                    $(this).prop('checked',false);
                });
                if($(this).prop('checked')){
                    $(self.user_form).find('.normal input, .normal select').removeClass('invalid').prop('required',false);;
                    $(self.user_form).find('.normal label span').html('');
                    $(self.user_form).find('.disposable input, .disposable select').prop('required',true);;
                    $(self.user_form).find('.disposable label span').html('*');
                } else {
                    $(self.user_form).find('.disposable input, .disposable select').removeClass('blue lighten-5 invalid').prop('required',false);;
                    $(self.user_form).find('.disposable label span').html('');
                    $(self.user_form).find('.normal input, .normal select').prop('required',true);; 
                    $(self.user_form).find('.normal label span').html('*');               }
            });
            $('#disposable_user').change();

            // flatpickr
            $("#birth_date").flatpickr({
                minDate: '1900-01-01',
                altInput: true,
                altFormat: flatpickrDateFormat,
                dateFormat: "Y-m-d",
            }); 
            
            // forbid access popover
            $('.btn-forbid-access').popover(CLIENT_DETAIL.forbid_access_popover_options);  
            // Close popover on click outside (BS4)          
            $('html').on("mouseup", function (e) {
                if ($(e.target)[0].className.indexOf("popover") == -1 && $(e.target).parents('.popover.show').length === 0) {
                    $(".popover").each(function () { $(this).popover("hide"); });
                }
            });
            
            $(document).on("click", '.btn-submit-forbid-access', function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form: $(this).closest('form')[0],
                    succes_text: 'Klientovi byl zamezen přístup!',
                    error_text: 'Nepovedlo se uložit změny, zkuste to prosím znovu.',
                    success_function: function(){
                        $('.btn-forbid-access').popover('hide').popover('disable').removeClass('btn-danger btn-forbid-access').addClass('btn-success btn-allow-access').text('Povolit vstup');
                        $('.forbidden-access').removeClass('d-none');
                    }
                });
            });

            $("#clientEditPage").on("click", '.btn-allow-access', function(e){
                if(confirm('Opravdu chcete znovu povolit vstup tomuto klientovi?')){
                    GYM._post('/admin/clients/allow_access_ajax', {'user_id':$('#user_id').val()}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Klientovi byl povolen přístup!');
                            $('.btn-allow-access').popover('enable').removeClass('btn-success btn-allow-access').addClass('btn-danger btn-forbid-access').text('Zakázat vstup');
                            $('.forbidden-access').addClass('d-none');
                        }else N.show('error', 'Nepovedlo se uložit změny, zkuste to prosím znovu.');
                    });
                }                
            });

            $('.js-media-input-target-id').change(function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            }); 

            $("#clientEditPage").on("click", ".remove-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete deaktivovat tohoto uživatele?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl deaktivován!');
                            $('.remove-user').data('ajax','/admin/users/activate_user_ajax').text("Aktivovat uživatele").removeClass("btn-danger").addClass("btn-success").removeClass("remove-user").addClass("activate-user");
                        }else N.show('error', 'Nepovedlo se deaktivovat uživatele, zkuste to prosím znovu.');
                    });
                }
            });

            $("#clientEditPage").on("click", ".activate-user", function(e){
                e.preventDefault();
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete znovu aktivovat tohoto uživatele?');
                if(agreed){
                    GYM._post(url, {'user_id':id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Uživatel byl aktivován!');
                            $('.activate-user').data('ajax','/admin/users/remove_user_ajax').text("Deaktivovat uživatele").addClass("btn-danger").removeClass("btn-success").addClass("remove-user").removeClass("activate-user");
                        }else N.show('error', 'Nepovedlo se aktivovat uživatele, zkuste to prosím znovu.');
                    });
                }
            });

            self.save_submit_btn.click(function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form: $(self.user_form)[0],
                    succes_text: 'Klient byl úspěšně upraven!',
                    error_text: 'Nepovedlo se uložit změny, zkuste to prosím znovu.',
                });
            });        

        }, 
        initTransactionHistoryTable: function(){
            this.transactions_history_table = new Tabulator(this.transactions_history_table, {
                initialFilter:[{field:"clientId", type:"=", value:$('#user_id').val()}],
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné transakce",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: '#', field: 'transactionNumber', headerFilter: true },
                    {title: 'PROVEDL', field: 'employee_name', headerFilter: true, editor: self.select2Editor, editable: false, headerSort:false },
                    {title: 'DATUM', field: 'paidOn', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat podle dne", editor: self.dateEditor, editable: false, formatterParams:{
                        inputFormat:"YYYY-MM-DD HH:mm:ss",
                        outputFormat:"DD.MM.YYYY HH:mm:ss",
                        invalidPlaceholder:""
                }},
                    {title: 'TYP PLATBY', field: 'transType', headerFilter:true },                    
                    {title: 'CENA', field: 'value', headerFilter:true, formatter: self.formatHistoryTableValue },
                ]
            });
            this.transactions_history_table.setLocale("cs-cs");
        },
        initMembershipBenefitsUsageTable: function(){
            this.membership_benefits_usage_table = new Tabulator(this.membership_benefits_usage_table, {
                initialFilter:[{field:"client_id", type:"=", value:$('#user_id').val()}],
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné využité výhody",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'ČLENSTVÍ', field: 'm_name', headerFilter: true, editable: false, headerSort:true },
                    {title: 'POLOŽKA', field: 'item_name', headerFilter: true, editable: false, headerSort:true },
                    {title: 'CENA', field: 'item_price', align:'right', headerFilter: true, editable: false, headerSort:true, formatter: function(c){ return c.getValue() + ' Kč'; } },
                    {title: 'SLEVA', field: 'discount', align:'right', headerFilter: true, editable: false, headerSort:true, formatter: function(c){ return c.getValue() + ' %'; } },
                    {title: 'DATUM', field: 'date_created', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat podle dne", editor: self.dateEditor, editable: false, formatterParams:{
                        inputFormat:"YYYY-MM-DD HH:mm:ss",
                        outputFormat:"DD.MM.YYYY HH:mm:ss",
                        invalidPlaceholder:""
                    }},
                ]
            });
            this.membership_benefits_usage_table.setLocale("cs-cs");
        },
        initPurchasedItemsTable: function(){
            this.purchased_items_table = new Tabulator(this.purchased_items_table, {
                initialFilter:[{field:"clientId", type:"=", value:$('#user_id').val()}],
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné transakce",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'DATUM', field: 'paidOn', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat podle dne", editor: self.dateEditor, editable: false, formatterParams:{
                            inputFormat:"YYYY-MM-DD HH:mm:ss",
                            outputFormat:"DD.MM.YYYY HH:mm:ss",
                            invalidPlaceholder:""
                    }},
                    {title: 'POLOŽKA', field: 'itemName', headerFilter: true, editor: self.select2Editor, editable: false, headerSort:false },                   
                    {title: 'CENA', field: 'vatPrice', headerFilter:true },
                ]
            });
            this.purchased_items_table.setLocale("cs-cs");
        },        
        initSubPaymentsTable: function(){
            this.sub_payment_table = new Tabulator(this.sub_payment_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezené žádné platby za členství",
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'OD', field: 'start', formatter: function(c){ return moment(c.getValue()).format('DD.MM.YYYY'); }},
                    {title: 'DO', field: 'end', formatter: function(c){ return moment(c.getValue()).format('DD.MM.YYYY'); }},
                    {title: 'CENA', field: 'value', formatter: function(c){
                            var formatter = new Intl.NumberFormat('cs-CZ', {
                                style: 'currency',
                                currency: 'CZK',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                            let d = c._cell.row.data;
                            return formatter.format(d.value + d.vat_value);
                        }
                    },
                    {title: 'ZAPLACENO', field: 'paid', formatter: function(c){
                            var el = c.getElement();
                            if(c.getValue()){
                                $(el).css('background', '#dbffdb');
                                if(typeof c.getRow().getData().deposit != 'undefined') return 'Ano (záloha)';
                                else return 'Ano';
                            }else{
                                $(el).css('background', '#ffdbdb');
                                return 'Ne';
                            }
                        }
                    },
                ]
            });
            this.sub_payment_table.setLocale("cs-cs");
        },
        formatHistoryTableValue: function(c){
            var d = c.getRow().getData(),
                el = c.getElement();
            
                var formatter = new Intl.NumberFormat('cs-CZ', {
                    style: 'currency',
                    currency: 'CZK',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                
            if(d.refund == "1"){
                $(el).addClass("invalid");
                return "-" + formatter.format(c.getValue());
            }else{
                return formatter.format(c.getValue());
            }
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            var editor = document.createElement("input");
            flatpickr(editor, { dateFormat: flatpickrDateFormat });
            onRendered(function(){ editor.focus(); editor.style.css = "100%"; });
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";
            editor.addEventListener("change", successFunc);
            return editor;
            function successFunc(){ success(moment(editor.value, tabulatorDateFormat).format("YYYY-MM-DD")); }
        }, 
        select2Editor: function(c, onRender, success, cancel, params){
            var editor = document.createElement("select"),
                field = c.getField();

            editor.id = field == 'employee_name' ? 'employeeSelector' : 'pricelistSelector';
            editor.classList.add('table-s2');
            if(field == 'employee_name'){
                editor.setAttribute('data-url', '/admin/clients/search-employees-ajax');
                editor.setAttribute('data-lang', 'zaměstnance');
            } else {
                editor.setAttribute('data-url', '/admin/pricelist/search-pricelist-items-ajax');
                editor.setAttribute('data-lang', 'položku');
            }

            $('body').on('change', '.table-s2', function(){ 
                success($(this).val()); 
            });
            return editor;
        },
        initTableSelects: function () {
            var s = $('.table-s2');
            $.each(s, function(i, select){
                $(select).select2({
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: function() {
                            return 'Napište alespoň 2 znaky..';
                        }
                    },
                    placeholder: 'Vyhledejte '+$(select).data('lang')+'..',
                    allowClear: true,
                    delay: 250,
                    ajax: {
                        url: $(select).data('url'),
                        dataType: "json",
                        type: "GET",
                        quietMillis: 50,
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.full_name,
                                        id: item.id,
                                    }
                                })
                            };
                        },
                    }                
                });
            });
        },                                                                         
    }
}());

CLIENT_DETAIL.init();