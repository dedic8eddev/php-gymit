'use strict';

var DEPOT = DEPOT || (function () {
    var self;
    return {
        depot_item_edit_form: $("#js_depot_item_edit_form"),
        depot_item_edit_form_btn: $("#js_depot_item_edit_form_btn"),

        gdn_table: "#depot_item_gdns",
        depot_gdn_table_url: $("#depot_item_gdns").data("ajax"),

        grn_table: "#depot_item_grns",
        depot_grn_table_url: $("#depot_item_grns").data("ajax"),
        init: async function(){
            self = this;

            this.user_role = await GYM._role();
            NProgress.configure({ parent: '.tool-bar .widget', minimum: 0.1, showSpinner: false });

            this.gdn_table = new Tabulator(this.gdn_table, {
                layout: 'fitColumns',
                placeholder:"Žádné výdejky nejsou k dispozici.",
                headerFilterPlaceholder:'Filtrovat data...',
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 20,
                columns: [
                    {title: 'Číslo výdejky', field: 'id', headerFilter:true,editable: false},
                    {title: 'Položka', field: 'name', editable: false},
                    {title: 'Množství', field: 'quantity', editable: false},
                    {title: 'Důvod', field: 'reason', editable: false},
                    {title: 'Vytvořeno', field: 'date_created', headerFilter:true,editor:this.dateEditor,editable: false,formatter:"datetime",formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD",
                        outputFormat:"DD.MM.YYYY",
                        invalidPlaceholder:"(invalid date)"
                        }
                    },
                    {title: '', align: 'right', formatter: this.returnTableButtons,editable: false, width: 100}
                ]
            });
            this.gdn_table.setSort([{column:"date_created",dir:"desc"}]);
            this.gdn_table.setData(this.depot_gdn_table_url);

            this.grn_table = new Tabulator(this.grn_table, {
                layout: 'fitColumns',
                placeholder:"Žádné příjemky nejsou k dispozici.",
                headerFilterPlaceholder:'Filtrovat data...',
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 20,
                columns: [
                    {title: 'Číslo příjemky', field: 'id', headerFilter:true,editable: false},
                    {title: 'Typ', field: 'income_type', headerFilter:true,editor:'select',editable: false,headerFilterParams:{"1":"Nové zboží","2":"Příjem zboží"},formatter:"lookup",formatterParams:
                        {
                        "1": 'Nové zboží',
                        "2": 'Příjem zboží'
                        }
                    },
                    {title: 'Vytvořeno', field: 'date_created', headerFilter:true,editor:this.dateEditor,editable: false,formatter:"datetime",formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD",
                        outputFormat:"DD.MM.YYYY",
                        invalidPlaceholder:"(invalid date)"
                        }
                    },
                    {title: '', align: 'right', formatter: this.returnTableButtonsGrn,editable: false, width: 100}
                ]
            });
            this.grn_table.setSort([{column:"date_created",dir:"desc"}]);
            this.grn_table.setData(this.depot_grn_table_url);

            this.fireEvents();
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var pdf_button = '<a href="/admin/depot/gdn-pdf/'+row_data.id+'" class="btn btn-xs btn-warning" data-id="'+row_data.id+'" target="_blank">PDF</a>&nbsp;';

            return pdf_button;
        },
        returnTableButtonsGrn: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var pdf_button = '<a href="/admin/depot/grn-pdf/'+row_data.id+'" class="btn btn-xs btn-warning" data-id="'+row_data.id+'" target="_blank">PDF</a>&nbsp;';

            return pdf_button;
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            //create and style editor
            var editor = document.createElement("input");

            flatpickr(editor, {
                dateFormat: "d.m.Y"
            });

            //create and style input
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";

            //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
            onRendered(function(){
                editor.focus();
                editor.style.css = "100%";
            });

            //when the value has been set, trigger the cell to update
            function successFunc(){
                success(moment(editor.value, "DD.MM.YYYY").format("YYYY-MM-DD"));
            }

            editor.addEventListener("change", successFunc);

            //return the editor element
            return editor;
        },
        fireEvents: function(){

            $("#v-pills-gdn-tab").click(function(){
                setTimeout(function(){
                    self.gdn_table.setData(self.depot_gdn_table_url);
                    self.gdn_table.redraw(true);
                },150);
            });
            $("#v-pills-grn-tab").click(function(){
                setTimeout(function(){
                    self.grn_table.setData(self.depot_grn_table_url);
                    self.grn_table.redraw(true);
                },150);
            });

            self.depot_item_edit_form_btn.click(function(e){
                e.preventDefault();
                var url = $(this).data("ajax");

                var inputs = self.depot_item_edit_form.find('input:required, select:required');

                $.each(inputs, function(i, input){
                    if($(input).val()){
                        $(input).removeClass("invalid");
                    }else{
                        $(input).addClass("invalid");
                    }
                });

                if(self.depot_item_edit_form.find(".invalid").length <= 0){

                    var formData = new FormData(),
                        form_inputs = self.depot_item_edit_form.find('input, select, textarea');

                    $.each(form_inputs, function(i, input){
                        formData.append($(input).attr('name'), $(input).val());
                    });

                    GYM._upload(url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Položka byla úspěšně upravena, stránka bude obnovena.', false, true);
                        }else{
                            N.show('error', 'Nepodařilo se položku upravit, zkontrolujte údaje nebo to zkuste znovu!');
                        }
                    });
                }else{
                    N.show('error', 'Povinné údaje chybí nebo některá pole obsahují chyby. Zkontrolujte červeně zvýrazněná pole.');
                }
            });

        }
    }
}());

DEPOT.init();