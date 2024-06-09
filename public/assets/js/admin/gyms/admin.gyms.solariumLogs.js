'use strict';

var SLOGS = SLOGS || (function () {
    var self;
    return {
        usage_table: '#usageTable',
        usage_table_url: $('#usageTable').data("ajax"),
        
        maintenance_table: '#maintenanceTable',
        maintenance_table_url: $('#maintenanceTable').data("ajax"),

        change_picker: {'1':'Ano','0':'Ne'},
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

            this.usage_table = new Tabulator(this.usage_table, {
                layout: 'fitColumns',
                headerFilterPlaceholder: 'Hledat..',
                placeholder:"Nebyly nalezeny žádné logy",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true},
                    {title: 'TRVÁNÍ', field: 'duration', headerFilter:true},
                    {title: 'ID TRANSAKCE', field: 'transaction_id', headerFilter:true},
                    {title: 'DATUM', field: 'date_created', formatter:"datetime", headerFilter: true, editor:self.dateEditor, editable: false, formatterParams:this.dateFilterParamas},
                ]
            });
            this.usage_table.setLocale("cs-cs");
            this.usage_table.setData(this.usage_table_url);

            this.maintenance_table = new Tabulator(this.maintenance_table, {
                layout: 'fitColumns',
                headerFilterPlaceholder: 'Hledat..',
                placeholder:"Nebyly nalezeny žádné logy",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'NÁZEV', field: 'name', headerFilter:true},
                    {title: 'VYMĚNA TRUBIC', field: 'change_pipes', headerFilter:true, headerFilter:'select', formatter:this.returnChangePipes, headerFilterParams:this.change_picker},
                    {title: 'ZADAL', field: 'full_name', headerFilter:true},
                    {title: 'DATUM', field: 'date_created', formatter:"datetime", headerFilter: true, editor:self.dateEditor, editable: false, formatterParams:this.dateFilterParamas},
                    {title: 'POZNÁMKA', field: 'note', headerFilter:true},
                ]
            });
            this.maintenance_table.setLocale("cs-cs");         

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
        returnChangePipes: function(c){
            let d = c._cell.row.data,
                icon = d.change_pipes==1 ? 'text-success icon-check' : 'text-danger icon-close';

            return `<i class="icon ${icon}"></i>`;
        },
        fireEvents: function(){

            // clear filter
            $(".js-clear-filter").click(function(){
                let table = eval('SLOGS.' + $(this).data('table'));
                table.clearFilter(true);
                table.clearSort();
            });              
            
            $("#v-pills-usage-tab").click(function(){ self.usage_table.setData(self.usage_table_url); });
            $("#v-pills-maintenance-tab").click(function(){ self.maintenance_table.setData(self.maintenance_table_url); });
        }
    }
}());

SLOGS.init();