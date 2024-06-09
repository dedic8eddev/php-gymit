'use strict';

var PRICE_LIST = PRICE_LIST || (function () {
    var self;
    return {
        price_list_table: '#pricelistTable',
        price_list_table_url: $('#pricelistTable').data("ajax"),
        price_form: '#priceForm', 

        membership_table: '#membershipTable',
        membership_table_url: $('#membershipTable').data("ajax"),
        membership_form: '#membershipForm',

        membership_overview_table: '#membershipOverviewTable',
        membership_overview_table_url: $('#membershipOverviewTable').data("ajax"),

        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),        
        
        active_picker: {'1':'Aktivní','2':'Neaktivní'},
        services_picker: {'1':'Cvičební zóny', '2':'Osobní trenér', '3':'Skupinové lekce', '4':'Wellness', '5':'Solarium', '6':'Vouchery', '7':'Půjčovna', '10':'Ostatní', '11': 'Parkování'},
        period_picker: {'d':'Den', 'm':'Měsíc', 'y':"Rok"},   
        dateFilterParamas: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat,
            invalidPlaceholder:""
        },             

        role: false,
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.initPriceListTable();
            this.initMembershipTable();
            this.initMembershipOverviewTable();
            this.initTableSelects();

            this.fireEvents();
        },
        returnPrice: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            if(row_data.price>0) return row_data.price+" Kč";
            else return "Zdarma";
        },
        returnVATPrice: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            if(row_data.vat_price>0) return row_data.vat_price+" Kč";
            else return "Zdarma";
        },          
        returnVAT: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return row_data.vat*100 + ' %';
        },
        returnServiceType: function(cell, params, onRendered){  
            var row_data = cell._cell.row.data;
            return self.services_picker[row_data.service_type];
        },
        returnPriceName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/pricelist/edit_price/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit">'+row_data.name+'</a>&nbsp;';
        },
        returnContractNumber: function(c){
            var d = c._cell.row.data;
            return `<a href="/admin/contract/get_contract_pdf?contractNumber=${d.contractNumber}&userId=${d.clientId}" target="_blank">${d.contractNumber}</a>`;
        },
        returnMembershipName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/pricelist/edit_membership/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit">'+row_data.name+'</a>&nbsp;';
        },    
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            if($(cell._cell.table.element).attr('id') == 'pricelistTable'){
                var delete_button = '';
                var edit_button = GYM._isAllowed('edit') ? '<a href="javascript:;" data-toggle="modal" data-remote="/admin/pricelist/edit_price/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;' :'';
                if(row_data.locked!=1 && GYM._isAllowed('delete')) delete_button = '<a href="javascript:;" class="price-remove text-danger ml-2" data-id="'+row_data.id+'" data-ajax="/admin/pricelist/remove_price_ajax"><i class="icon-close"></i></a>';                
            } else {
                var edit_button = GYM._isAllowed('edit') ? '<a href="javascript:;" data-toggle="modal" data-remote="/admin/pricelist/edit_membership/'+row_data.id+'" data-target="#modal" data-modal-title="Editace položky" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;' :'';
                var delete_button = '';                
            }
            return edit_button+delete_button;
        },
        returnVisible: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return row_data.visible == 1 ? '<i class="fa fa-eye" title="Zobrazuje se"></i>&nbsp;' : '';
        },
        tableSelect2: function(c, onRender, success, cancel, params){
            var editor = document.createElement("select"),
                field = c.getField();

            editor.id = field == 'client_name' ? 'clientSelector' : 'employeeSelector';
            editor.classList.add('table-s2');

            var term = "";
            if(field == "client_name"){
                term = "clients";
            }else if(field == "membership_name"){
                term = "memberships";
            }

            editor.setAttribute('data-type', term);

            var lang = "";
            if(field == "client_name"){
                lang = "klienta";
            }else if(field == "membership_name"){
                lang = "členství";
            }

            editor.setAttribute('data-lang', lang);

            $('body').on('change', '#'+editor.id, function(){ success($(this).val()) });
            return editor;
        },
        fireEvents: function(){
            TRUMBOWYG.init();

            $('#v-pills-prices-tab').click(function(){ self.price_list_table.setData(self.price_list_table_url); })
            $('#v-pills-membership-tab').click(function(){ self.membership_table.setData(self.membership_table_url); })
            $('#v-pills-membership-overview-tab').click(function(){ self.membership_overview_table.setData(self.membership_overview_table_url); })

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('PRICE_LIST.' + $(this).data('table'));
                table.clearFilter(true);
                table.clearSort();
                self.initTableSelects();
            });      
            

            $('body').on('click', '[data-toggle="modal"]', function(){
                var remote_url = $(this).data("remote");
                $($(this).data("target")+' .modal-body').load(remote_url, function(){
                    $('.select2').select2();
                    if(self.remote_modal.find('.js-trumbowyg-editor').length) TRUMBOWYG.init();
                    self.setServiceType($('#service_type').val());
                    if(self.remote_modal.find(self.membership_form).length) self.servicesPricesTabulator(/[^/]*$/.exec(remote_url)[0]).init();
                    // icon dropdown 
                    $('.dropdown-menu li').on('click', function(){
                        $(this).parent().prev('.dropdown-toggle').html($(this).html());   
                        var itemId = $(this).data('id');
                        $(this).parents('.form-row').find('.selectedDropDownItem').val(itemId);
                        $(this).parents('.form-row').find('.itemText').prop('disabled',itemId == '');
                    });  
                });
            });  

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });           

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            }); 

            // service types
            self.remote_modal.on('change', '#service_type', function(){
                self.setServiceType($(this).val());
            });            
            
            // VAT settings
            self.remote_modal.on('change', '#price_edit', function(){
                var vat = $('#vat_value_edit').val();
                if(vat) $('#vat_price_edit').val( (parseInt($(this).val())) * (1 + parseFloat(vat)) );
                else $('#vat_price_edit').val( (parseInt($(this).val())) * (1 + 0.21) );
            });
            self.remote_modal.on('change', '#vat_price_edit', function(){           
                var vat = $('#vat_value_edit').val();
                if(vat) $('#price_edit').val( (parseInt($(this).val()) / (1 + parseFloat(vat))) );
                else $('#price_edit').val( (parseInt($(this).val()) / (1 + 0.21)) );
            });
            self.remote_modal.on('change', '#vat_value_edit', function(){   
                var price = $('#price_edit').val(),
                    vat_price = $('#vat_price_edit').val(),
                    vat = $(this).val();
                if(vat_price) $('#price_edit').val( (parseInt(vat_price) / (1 + parseFloat(vat))) );
                else if(!vat_price && price) $('#vat_price_edit').val( (parseInt(price) * (1 + parseFloat(vat))) );
            });            

            $('#pricelistTable').on('click', '.price-remove, .gym_service-remove', function(){
                var url = $(this).data('ajax'),
                    id = $(this).data('id'),
                    agreed = confirm('Opravdu chcete vymazat tuto položku?');
                if(agreed){
                    GYM._post(url, {'price_id':id}).done(function(res){
                        if(!res.error){
                            self.price_list_table.setData(self.price_list_table_url);
                            N.show('success', 'Položka úspěšně smazána!');
                        }else N.show('error', 'Nepodařilo se smazat položku, zkuste to znovu.');
                        NProgress.done();
                    });
                }
            });        

            $('#priceListPage').on('submit', self.price_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Položka byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit položku, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.price_list_table.setData(self.price_list_table_url);
                    }
                });
            });

            $('#priceListPage').on('submit', self.membership_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Položka byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit položku, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.membership_table.setData(self.membership_table_url);
                    }
                });
            });            
        },
        setServiceType: function(type){
            if(jQuery.inArray(parseInt(type), [3,5]) !== -1){
                $('#duration').prop('required',true).attr("disabled", false).closest('.js-duration-col').removeClass('d-none');
            } else if(type){
                $('#duration').prop('required',false).attr("disabled", true).closest('.js-duration-col').addClass('d-none');            
            }
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
        initPriceListTable: function(){
            this.price_list_table = new Tabulator(this.price_list_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeni žádní klienti",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'TYP SLUŽBY', field: 'service_type', headerFilter:'select', headerFilterParams: PRICE_LIST.services_picker,headerFilterPlaceholder:"Hledat podle služby",formatter: this.returnServiceType},
                    {title: 'POLOŽKA', field: 'name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu",formatter: this.returnPriceName},
                    {title: 'CENA BEZ DPH', field: 'price', align: 'right', headerFilter:true, headerFilterPlaceholder:"Hledat..", formatter: this.returnPrice},
                    {title: 'DAŇ', field: 'vat', align: 'right', headerFilter:true, headerFilterPlaceholder:"Hledat..", formatter: this.returnVAT},
                    {title: 'CENA S DPH', field: 'vat_price', align: 'right', headerFilter:true, headerFilterPlaceholder:"Hledat podle ceny",formatter: this.returnVATPrice},
                    {title: '', align: 'center', headerSort:false, formatter: this.returnVisible, width: 10 },
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 90}
                ]
            });
            this.price_list_table.setLocale("cs-cs");
            this.price_list_table.setData(this.price_list_table_url);            
        },
        initMembershipTable: function(){
            this.membership_table = new Tabulator(this.membership_table, {
                layout: 'fitColumns',
                placeholder:"Nebylo nalezeno žádné členství",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'TYP ČLENSTVÍ', field: 'type_name', headerFilter:true, headerFilterPlaceholder:"Hledat podle typu", width:200},
                    {title: 'NÁZEV', field: 'type_name', headerFilter:true, headerFilterPlaceholder:"Hledat podle názvu",formatter: this.returnMembershipName},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.membership_table.setLocale("cs-cs");
        },
        initMembershipOverviewTable: function(){
            this.membership_overview_table = new Tabulator(this.membership_overview_table, {
                layout: 'fitColumns',
                placeholder:"Nebyli nalezeno žádné členství",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'ČÍSLO SMLOUVY', field: 'contractNumber', align: 'right', headerFilter:true, headerFilterPlaceholder:"Hledat podle čísla smlouvy", formatter:self.returnContractNumber},
                    {title: 'KLIENT', field: 'client_name', headerSort:false, headerFilter: true, headerFilterPlaceholder:"Hledat podle klienta", editor: self.tableSelect2, editable: false},
                    {title: 'DRUH ČLENSTVÍ', field: 'membership_name', headerSort:false, headerFilter: true, headerFilterPlaceholder:"Hledat podle členství", editor: self.tableSelect2, editable: false},
                    {title: 'TYP ČLENSTVÍ', field: 'membership_type', headerSort:false, headerFilter: false},
                    {title: 'OD', field: 'createdOn', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat od data", editor: self.dateEditor, editable: false, formatterParams:self.dateFilterParamas},
                    {title: 'DO', field: 'to', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat do data", editor: self.dateEditor, editable: false, formatterParams:self.dateFilterParamas }
                ]
            });
            this.membership_overview_table.setLocale("cs-cs");
            this.membership_overview_table.setData(this.membership_overview_table_url);
        },
        servicesPricesTabulator: function(mem_id){
            return {
                servicesPrices_table: '#servicesPrices_table',
                servicesPrices_table_url: $('#servicesPrices_table').data("ajax"),
                servicesPrices_table_type: $('#servicesPrices_table').data("type"),
                init: async function(params){
                    NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });  
                    this.servicesPrices_table = new Tabulator(this.servicesPrices_table, {
                        layout: 'fitColumns',
                        placeholder:"Žádna data nenalezena",
                        resizableColumns: false,
                        pagination: 'remote',
                        paginationSize: 20,
                        paginationSizeSelector:[10, 20, 30, 50, 100],
                        layoutColumnsOnNewData:true,
                        langs: GYM.tabulator_czech,
                        columns: [
                            {title: 'TYP SLUŽBY', field: 'service_type', headerFilter:'select', headerFilterParams: PRICE_LIST.services_picker,headerFilterPlaceholder:"Hledat podle služby",formatter: PRICE_LIST.returnServiceType},
                            {title: 'Název', field: 'name', headerSort:true, headerFilter:true, editable:false, widthGrow:2 },
                            {title: 'Cena s DPH', field: 'vat_price', align: 'right', headerSort:true, headerFilter:true, editor:"input", editable:true },
                            {title: 'Akce', field: 'actions', align: 'right', headerSort:false, editable:false, formatter: this.returnTableButtons},
                        ]
                    });
                    this.servicesPrices_table.setLocale("cs-cs");
                    this.fireEvents();
                },
                returnTableButtons: function(cell, params, onRendered){
                    var row_data = cell._cell.row.data;
                    return GYM._isAllowed('edit') ? '<a href="javascript:;" class="btn btn-xs btn-primary js-save-btn" data-url="/admin/pricelist/save-membership-service-price-ajax" data-id="'+row_data.id+'" data-price-id="'+row_data.price_id+'" data-mem-id="'+mem_id+'" data-vat="'+row_data.vat+'">Uložit</a>&nbsp;': '';
                },
                fireEvents: function(){                    
                    var self = this;

                    $('#v-pills-servicesPrices-tab').click(function(){ 
                        self.servicesPrices_table.setData(self.servicesPrices_table_url); 
                        PRICE_LIST.btn_modal_submit.hide();
                    });

                    $('#v-pills-general-tab').click(function(){
                        PRICE_LIST.btn_modal_submit.show();
                    });
        
                    $("#js_add_tag").on("click", function(e) { 
                        self.servicesPrices_table.addData([{name:"Nový",actions:""}], true);
                        $("#servicesPrices_table").find('.tabulator-row:first-child .tabulator-cell:first-child').click();
                    });                                        
        
                    $("#servicesPrices_table").on('click','.js-save-btn',function(e){
                        e.preventDefault();
                        var data = {};
                        var url = $(this).data('url');
                        data['item_id'] = $(this).data('id'),
                        data['vat_price'] = $(this).closest('.tabulator-row').find('.tabulator-cell:nth-child(3)').text(),
                        data['vat'] = $(this).data('vat'),
                        data['price'] = parseFloat(data['vat_price']) / (1 + parseFloat(data['vat'])),
                        data['price_id'] = $(this).data('price-id'),

                        data['membership_id'] = $(this).data('mem-id');

                        GYM._post(url, data).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka uložena!');
                                self.servicesPrices_table.setData(self.servicesPrices_table_url);
                            }else N.show('error', 'Nepovedlo se uložit položku, zkuste to prosím znovu.');
                        });                                         
                    }); 
                    $("#servicesPrices_table").on('click','.js-delete-btn',function(e){
                        e.preventDefault();

                        var url = $(this).data('url'),
                            item_id = $(this).data('id'),
                            item_name = $(this).closest('.tabulator-row').find('.tabulator-cell:first-child').text();

                        GYM._post(url, {item_id:item_id}).done(function(res){
                            if(!res.error){
                                N.show('success', 'Položka smazána!');
                                self.servicesPrices_table.setData(self.servicesPrices_table_url);
                            }else N.show('error', 'Nepovedlo se smazat položku, zkuste to prosím znovu.');
                        });                                             
                    });                        
                }            
            }
        },          
    }
}());

PRICE_LIST.init();