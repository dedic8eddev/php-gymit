'use strict';

var EETAPP = EETAPP || (function () {
    var self;
    return {
        checkouts_table: '#checkoutsTable',
        checkouts_table_url: $('#checkoutsTable').data("ajax"),
        checkout_form: '#checkoutForm',
        checkout_state_form: '#checkoutStateForm',

        log_table: '#checkoutsLogTable',
        log_table_url: $('#checkoutsLogTable').data("ajax"),
        
        remote_modal: $('#modal'),   
        btn_modal_submit: $('#modalSubmit'),
        
        state_picker: {'1':'Otevřel/a', '0':'Uzavřel/a'},
        
        role: false,
        init: async function(params){
            self = this;

            this.role = await GYM._role();
            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });

            this.checkouts_table = new Tabulator(this.checkouts_table, {
                layout: 'fitColumns',
                headerFilterPlaceholder: "Hledat..",
                placeholder:"Nebyly nalezeny žádné pracovní pozice",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true,formatter: this.returnName},
                    {title: 'STAV', field: 'state', headerFilter:true,formatter: this.returnState},
                    {title: 'EVIDOVANÝ ZŮSTATEK', field: 'amount', align:'right', headerFilter:true,formatter: this.returnAmount},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons, width: 100}
                ]
            });
            this.checkouts_table.setLocale("cs-cs");
            this.checkouts_table.setData(this.checkouts_table_url);

            this.log_table = new Tabulator(this.log_table, {
                layout: 'fitColumns',
                headerFilterPlaceholder: "Hledat..",
                placeholder:"Nebyly nalezeny žádné pracovní pozice",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'POKLADNA', field: 'name', headerFilter:true},
                    {title: 'UŽIVATEL', field: 'user', headerFilter:true},
                    {title: 'DATUM', field: 'date_created', headerFilter:true, editable:false, editor:this.dateEditor, formatter:"datetime",formatterParams:this.dateFormatterParams },
                    {title: 'STAV', field: 'state', headerFilter:"select", headerFilterParams: this.state_picker,formatter: this.returnLogState},
                    {title: 'EVIDOVANÝ ZŮSTATEK', field: 'amount', align:'right', headerFilter:true,formatter: this.returnAmount},
                ]
            });
            this.log_table.setLocale("cs-cs");            

            this.fireEvents();
        },
        dateFormatterParams: {
            inputFormat:"YYYY-MM-DD HH:mm:ss",
            outputFormat:tabulatorDateFormat+' '+tabulatorTimeFormat, // variable from main.js
            invalidPlaceholder:"(invalid date)"
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
            return '<a class="edit-modal-btn" href="javascript:;" data-toggle="modal" data-remote="/admin/eetapp/edit-checkout/'+row_data.id+'" data-target="#modal" data-modal-title="Editace pokladny" data-modal-submit="Uložit">'+row_data.name+'</a>&nbsp;';
        },
        returnState: function(cell){
            if(cell._cell.row.data.state != 1) return '<span class="icon icon-lock3 s-18 mr-1 text-danger"></span> Uzavřená';
            else return '<span class="icon icon-lock-open2 s-18 mr-2 text-success"></span> Otevřená';
        }, 
        returnLogState: function(cell){
            if(cell._cell.row.data.state != 1) return '<span class="icon icon-lock3 s-18 mr-1 text-danger"></span> Uzavřel/a';
            else return '<span class="icon icon-lock-open2 s-18 mr-2 text-success"></span> Otevřel/a';
        },         
        returnAmount: function(cell){
            return GYM._separateThousands(cell._cell.row.data.amount)+" Kč";
        },                
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var edit_button = '<a href="javascript:;" class="edit-modal-btn" data-toggle="modal" data-remote="/admin/eetapp/edit-checkout/'+row_data.id+'" data-target="#modal" data-modal-title="Editace pokladny" data-modal-submit="Uložit"><i class="icon-pencil"></i></a>&nbsp;';
            var open_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/eetapp/set-checkout-state/'+row_data.id+'" data-target="#modal" title="Otevřít pokladnu" data-modal-title="Otevřít pokladnu" data-modal-submit="Otevřít pokladnu"><i class="icon-lock-open2 s-18 text-success"></i></a>&nbsp;';
            var close_button = '<a href="javascript:;" data-toggle="modal" data-remote="/admin/eetapp/set-checkout-state/'+row_data.id+'" data-target="#modal" title="Zavřít pokladnu" data-modal-title="Zavřít pokladnu" data-modal-submit="Zavřít pokladnu"><i class="icon-lock3 s-18 text-danger"></i></a>&nbsp;';
            if(row_data.state==1) return close_button+edit_button;
            else return open_button+edit_button;
        },         
        fireEvents: function(){
            
            $('body').on('click', '[data-toggle="modal"]', function(){
                if($(this).hasClass('edit-modal-btn')) $($(this).data("target")+' .modal-dialog').addClass('modal-lg');
                else $($(this).data("target")+' .modal-dialog').removeClass('modal-lg');
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                });
            });              

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            });             

            $('#v-pills-table-tab').click(function(){ self.checkouts_table.setData(self.checkouts_table_url); });
            $('#v-pills-log-tab').click(function(){ self.log_table.setData(self.log_table_url); });

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('EETAPP.' + $(this).data('table'));
                table.clearSort();
                table.clearFilter(true);
            });   
            
            self.remote_modal.on('submit', self.checkout_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Pokladna byla úspěšně uložena!',
                    error_text: 'Nepodařilo se uložit pokladnu, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.checkouts_table.setData(self.checkouts_table_url);
                    }
                });
            }); 

            self.remote_modal.on('submit', self.checkout_state_form, function(e){
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Stav pokladny byl úspěšně uložen!',
                    error_text: 'Nepodařilo se uložit stav pokladny, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.remote_modal.modal('hide');
                        self.checkouts_table.setData(self.checkouts_table_url);
                    }
                });
            });            
        },
    }
}());

EETAPP.init();