'use strict';

var VOUCHERS = VOUCHERS || (function () {
    var self;
    return {
        vouchers_table: '#vouchersTable',
        vouchers_table_url: $('#vouchersTable').data("ajax"),

        btn_add_vouchers: $('#addVouchers'),
        add_voucher_items_table: $('#addVoucherItemsTable'),
        
        remote_modal: $('#modal'),
        btn_modal_submit: $('#modalSubmit'),        
        
        active_picker: {'1':'Aktivní','2':'Neaktivní'},
        identification_picker: {'invoice':'Faktura','webpay':'Webová platební brána','payments':'Pokladna'},    
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

            this.vouchers_table = new Tabulator(this.vouchers_table, {
                layout: 'fitColumns',
                headerFilterPlaceholder:"Hledat..",
                placeholder:"Nebyly nalezeny žádné vouchery",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'KÓD', field: 'code', headerFilter:true, width:120},
                    {title: 'TYP PLATBY', field: 'identification_type', headerFilter:'select', headerFilterParams:{values:this.identification_picker}, formatter:'lookup', formatterParams:this.identification_picker },
                    {title: 'ID PLATBY', field: 'identification_id', headerFilter:true },
                    {title: 'OBDAROVANÝ', field: 'gifted_user_name', headerFilter:true, formatter:this.returnGiftedUser },
                    {title: 'STATUS', field: 'state', headerFilter:"select", headerFilterFunc:this.statusFilter, headerFilterParams:this.active_picker, headerFilterPlaceholder: 'Aktivní/neaktivní', formatter: this.returnStatus, width:120},
                    {title: 'POZNÁMKA', field: 'note', headerFilter:true, },
                ],
                rowClick:function(e, row){
                    // if text is not selected (in case of copying)
                    if(!getSelection().toString()){
                        let a = $(`<a href="javascript:;" data-toggle="modal" data-remote="/admin/vouchers/detail/${row._row.data.code}" data-target="#modal" data-modal-title="Detail voucheru" data-modal-submit="Uložit">`);
                        $('body').append(a);
                        a.click().remove();  
                    }
                },
            });
            this.vouchers_table.setLocale("cs-cs");
            this.vouchers_table.setData(this.vouchers_table_url);

            this.fireEvents();
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
        returnName: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            return row_data.name;
            return '<a href="javascript:;" data-toggle="modal" data-remote="/admin/vouchers/edit-voucher/'+row_data.id+'" data-target="#modal" data-modal-title="Editace voucheru" data-modal-submit="Uložit">'+row_data.name+'</a>&nbsp;';
        },
        returnGiftedUser: function(c, params, onRendered){
            var d = c._cell.row.data;
            return d.gifted_user_name ? `${d.gifted_user_name} (${d.gifted_user_email})` : '';
        },        
        returnStatus: function(cell){
            if(cell._cell.row.data.date_disabled) return '<span class="icon icon-circle s-12 mr-2"></span> Neaktivní';
            else return '<span class="icon icon-circle s-12 mr-2 text-primary"></span> Aktivní';
        },        
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/vouchers/edit-voucher/'+row_data.id+'" data-target="#modal" data-modal-title="Editace voucheru" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;';
            var disable_button = '<a href="javascript:;" class="js-disable-voucher text-danger ml-2" data-code="'+row_data.code+'" data-ajax="/admin/vouchers/disable-voucher-ajax"><i class="icon-close"></i></a>';            
            if(row_data.date_disabled) return;
            return disable_button;
        },         
        fireEvents: function(){

            // invoice_items
            if(GYM._getUrlParameter('invoice')) $('#invoiceItemsModal').modal('show');
            self.btn_add_vouchers.click(function(){
                let data={};
                data['identification_type']=self.add_voucher_items_table.data('identification-type');
                data['identification_id']=self.add_voucher_items_table.data('identification-id');
                data['items']=[];
                self.add_voucher_items_table.find('tbody .voucher-item').each(function(i,input){
                    data['items'].push({
                        'type': $(input).data('item-type'),
                        'id': $(input).data('item-id'),
                        'amount': $(input).val()
                    });
                });

                GYM._post('/admin/vouchers/create_vouchers_ajax', data).done(function (res){
                    if(!res.error){
                        N.show('success', 'Vouchery byly úspěšně přidány');
                        $('#invoiceItemsModal').modal('hide');
                        self.vouchers_table.setData(self.vouchers_table_url);
                    }else{
                        N.show('error', GYM.general_ajax_error);
                    }
                });
            });

            $('#showCreatedInvoiceVouchers').click(function(){
                let invoiceNum = $(this).data('invoice-number');
                self.vouchers_table.setFilter([
                    {field:'identification_type', type:'=', value:'invoice'},
                    {field:'identification_id', type:'=', value:invoiceNum},
                ]);
                $('#invoiceItemsModal').modal('hide');
            });

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('VOUCHERS.' + $(this).data('table'));
                table.clearFilter(true);
                table.clearSort();
            });      

            $('body').on('click', '[data-toggle="modal"]', function(){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                });
            });              

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
                self.btn_modal_submit.hide();
            });

            self.remote_modal.on('shown.bs.modal', function(e){
            });

            self.remote_modal.on('hide.bs.modal', function(e){
            });               
            
            self.remote_modal.on('hidden.bs.modal', function (event) {
                if ($('.modal:visible').length) { // modal over modal
                    $('body').addClass('modal-open');
                }
            });              

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            }); 
            
            self.remote_modal.on('click', '.js-disable-voucher', function(){
                var url = $(this).data('ajax'),
                voucher_code = $(this).data('code'),
                agreed = confirm('Opravdu chcete zneaktivnit tento voucher?');
                if(agreed){
                    GYM._post(url, {'voucher_code':voucher_code}).done(function(res){
                        if(!res.error){
                            self.vouchers_table.setData(self.vouchers_table_url);
                            N.show('success', 'Voucher úspěšně deaktivován!');
                            self.remote_modal.modal('hide');
                        }else N.show('error', 'Nepodařilo se zneaktivnit voucher, zkuste to znovu.');
                        NProgress.done();
                    });
                }
            });

            $('#vouchersPage').on('submit', self.voucher_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Položka byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit položku, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.vouchers_table.setData(self.vouchers_table_url);
                    }
                });
            });
        },          
    }
}());

VOUCHERS.init();