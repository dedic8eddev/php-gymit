'use strict';

var CLIENTS = CLIENTS || (function () {
    var self;
    return {
        role: null,

        clients_table: '#clientsTable',
        inactive_table: '#inactiveTable',
        clients_table_url: $('#clientsTable').data("ajax"),
        client_form: $('#addClientForm'),
        client_submit: $('.add-client-submit'),
        clients_groups: {'20':'Člen','21':'Jednorázový zákazník'},

        bool_picker: {'1':'Ano', '0':'Ne'},
        membership_picker: {'1':'Ano', '0':'Ne'},
        active_picker: {'1':'Aktivní', '0':'Neaktivní'},

        reader_select: $('select[name="reader_id"]'),
        selected_reader: PERSONIFICATORS.getSessionReader() || $('select[name="reader_id"]').val(),
        cardField: $('#readerInput'),

        init: async function(params){
            self = this;

            this.role = await GYM._role();

            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });   

            this.clients_table = new Tabulator(this.clients_table, {
                layout: 'fitColumns',
                placeholder:'Nebyli nalezeni žádní klienti',
                headerFilterPlaceholder:'Hledat..',
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'UŽIVATEL', field: 'full_name', headerFilter:true, formatter: this.returnName},
                    {title: 'E-MAIL', field: 'email', headerFilter:true},
                    {title: 'TYP', field: 'group_id', headerFilter:"select", headerFilterParams:{values:this.clients_groups}, formatter:'lookup', formatterParams:this.clients_groups},
                    {title: 'ČLENSTVÍ', field: 'membership', headerSort: false, headerFilter: true, headerFilterPlaceholder:"Hledat podle členství", editor: this.tableSelect2, editable: false},
                    {title: 'MULTISPORT', field: 'multisport_bool', headerFilter:"select", headerFilterParams:{values:this.bool_picker}, formatter:this.returnMultisportBool, width:140},
                    {title: 'TELEFON', field: 'phone', headerFilter:true, width:140},
                    {title: 'DATUM NAROZENÍ', field: 'birth_date', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams, width:180},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.clients_table.setLocale("cs-cs");
            this.clients_table.setData(this.clients_table_url,{role:[20,21],active:1});

            this.inactive_table = new Tabulator(this.inactive_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní neaktivní zákazníci",
                headerFilterPlaceholder:"Hledat..",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'UŽIVATEL', field: 'full_name', headerFilter:true, formatter: this.returnName},
                    {title: 'E-MAIL', field: 'email', headerFilter:true},
                    {title: 'TELEFON', field: 'phone', headerFilter:true},
                    {title: 'DATUM NAROZENÍ', field: 'birth_date', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.inactive_table.setLocale("cs-cs");   

            self.reader_select.val(self.selected_reader);

            this.initTableSelects();

            this.fireEvents();
        },
        dateFormatterParams: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat, // variable from main.js
            invalidPlaceholder:"-&nbsp;-&nbsp;-"
        }, 
        tableSelect2: function(c, onRender, success, cancel, params){
            var editor = document.createElement("select"),
                field = c.getField();

            editor.id = 'membershipSelector';
            editor.classList.add('table-s2');
            editor.setAttribute('data-type', 'memberships');
            editor.setAttribute('data-lang', 'členství');

            $('body').on('change', '#'+editor.id, function(){ success($(this).val()) });
            return editor;
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
        fillCardField: function(value){
            self.cardField.val(value);
        },     
        statusFilter: function(headerValue, rowValue, rowData, filterParams){
            var compare = 0;
            if(headerValue == "Aktivní") compare = 1;

            return rowData.active == compare;
        },
        returnStatus: function(cell){
            var row_data = cell._cell.row.data;
            if(row_data.active != 1){
                return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            }else{
                return '<span class="icon icon-circle s-12 mr-2 text-success"></span> Aktivní';
            }
        },
        returnMultisportBool: function(c){
            let d = c._cell.row.data;
            return d.multisport_id ? 'ANO' : 'NE';
        },
        returnNum: function(){
            return 0;
        },
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="/admin/clients/edit/'+row_data.id+'">'+row_data.full_name+'</a>';
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = GYM._isAllowed('edit') ? '<a href="/admin/clients/edit/'+row_data.id+'" data-id="'+row_data.id+'"><i class="icon-pencil"></i></a>&nbsp;' :'';
            var delete_button = '<a href="javascript:;" class="btn btn-xs btn-danger client-remove" data-id="'+row_data.id+'" data-ajax="/admin/clients/remove_client_ajax">Odstranit</a>';            
            return edit_button;
        },
        validEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        initTableSelects: function () {
            var s = $('.table-s2');
            console.log(s)
            $.each(s, function(i, select){
                console.log(select)
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
                        url: '/admin/clients/search_'+$(select).data('type')+'_ajax',
                        dataType: "json",
                        type: "GET",
                        quietMillis: 50,
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: ($(select).data('type') == "memberships") ? item.name : item.full_name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });
            });
        },
        fireEvents: function(){

            $(".nav-link").click(function(){
                if ( $(this).hasClass("switch-to-inactive") || $(this).hasClass("switch-to-clients") ){
                    var readerId = self.reader_select.val();
                    if(readerId){
                        PERSONIFICATORS.stopPolling(readerId);
                    }
                    $('#readerInput').val('');
                }
            });

            $("#v-pills-buyers-tab").click(function(){
                if(self.selected_reader){
                    PERSONIFICATORS.startPolling(self.selected_reader, CLIENTS.fillCardField);
                }
            });

            $('.lessRequired').change(function(){
                $('.lessRequired').not(this).each(function(){
                    $(this).prop('checked',false);
                });
                if($(this).prop('checked')){
                    $(self.client_form).find('.normal input, .normal select').removeClass('invalid').prop('required',false);;
                    $(self.client_form).find('.normal label span').html('');
                    $(self.client_form).find('.disposable input, .disposable select').prop('required',true);;
                    $(self.client_form).find('.disposable label span').html('*');
                } else {
                    $(self.client_form).find('.disposable input, .disposable select').removeClass('blue lighten-5 invalid').prop('required',false);;
                    $(self.client_form).find('.disposable label span').html('');
                    $(self.client_form).find('.normal input, .normal select').prop('required',true);; 
                    $(self.client_form).find('.normal label span').html('*');               }
            });

            self.reader_select.change(function(){
                PERSONIFICATORS.stopPolling(self.selected_reader);
                PERSONIFICATORS.setSessionReader($(this).val());
                PERSONIFICATORS.startPolling($(this).val());

                self.selected_reader = $(this).val();
            });

            GYM._media(); // init media
            $('.js-media-input-target-id').change(function(){
                var img = $(this).attr('data-img');
                $('.image-preview').css('background-image', 'url(' + img + ')').addClass('uploaded');
            });

            // flatpickr
            $("#birth_date").flatpickr({
                minDate: '1900-01-01',
                altInput: true,
                altFormat: flatpickrDateFormat,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    // is client younger than 15
                    if(moment().diff(dateStr, 'years') < 15) $('#representative').closest('.form-row').removeClass('d-none');
                    else $('#representative').closest('.form-row').addClass('d-none');
                },
            });               

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('CLIENTS.' + $(this).data('table'));
                table.clearFilter(true);
                table.clearSort();
            });   

            $('.switch-to-clients').click(function(){ self.clients_table.setData(self.clients_table_url,{role:[20,21],active:1}); });
            $('.switch-to-inactive').click(function(){ self.inactive_table.setData(self.clients_table_url,{role:[20,21],active:0}); });

            $('#clientsTable').on('click', '.client-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id');

                var agreed = confirm('Opravdu chcete vymazat tohoto zákazníka?');
                if(agreed){
                    GYM._post(url, {'client_id':id}).done(function(res){
                        if(!res.error){
                            self.clients_table.setData(self.clients_table_url,{role:[20,21],active:1});
                            N.show('success', 'Zákazník úspěšně smazán!');
                        }else{
                            N.show('error', 'Nepovedlo se smazat zákazníka, zkuste to znovu.');
                        }

                        NProgress.done();
                    });
                }
            });

            this.client_submit.click(function(e){
                e.preventDefault();

                if(!$('#agreement').is(':checked')) $('#agreement').parent().find("label").addClass('invalid');
                else $('#agreement').parent().find("label").removeClass('invalid');

                if($('#representative').is(':visible') && !$('#representative').is(':checked')) $('#representative').parent().find("label").addClass('invalid');
                else $('#representative').parent().find("label").removeClass('invalid');                

                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form: $(self.client_form)[0],
                    succes_text: 'Uživatelský účet byl úspěšně vytvořen!',
                    error_text: 'Nepodařilo se vytvořit účet, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.client_form[0].reset();
                        $(".switch-to-clients").click();
                    }
                });                
            });
        }
    }
}());

CLIENTS.init();