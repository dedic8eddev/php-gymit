'use strict';

var DEPOT = DEPOT || (function () {
    var self;
    return {
        depot_table: '#depotTable',
        depot_table_url: $('#depotTable').data("ajax"),

        invoice_table: '#depotInvoiceTable',
        invoice_table_data: $('#depotInvoiceTable').data("ajax"),
        sub_tables: [],

        depot_add_item_form: $("#js_depot_add_new_item_form"),
        depot_add_item_form_btn: $("#js_depot_add_new_item_form_btn"),

        depot_receipt_goods_form: $("#js_depot_receipt_goods_form"),
        depot_receipt_goods_form_btn: $("#js_depot_receipt_goods_form_btn"),
        depot_receipt_goods_modal: $("#js_depot_receipt_goods_modal"),

        depot_delivered_goods_form: $("#js_depot_delivered_goods_form"),
        depot_delivered_goods_form_btn: $("#js_depot_delivered_goods_form_btn"),
        depot_delivered_goods_modal: $("#js_depot_delivered_goods_modal"),

        product_modal: $('#productModal'),
        product_modal_submit: $('#saveProductDetail'),
        product_modal_title: $('#productModalTitle'),
        product_stock_table: '#productStockTable',
        product_log_table: '#productLogTable',
        product_edit_form: $('#productDetailForm'),
        move_product_modal: $('#moveProductModal'),
        take_product_modal: $('#removeProductModal'),
        move_product_submit: $('#moveprod'),
        take_product_submit: $('#removeprod'),
        log_modal: $('#logtModal'),

        invoiceFormTable: '#invoiceFormTable',
        invoiceProductRowTemplate: $('#newProductRowTemplate'),
        invoiceNewProductsContainer: $('.new-products-container'),
        invoiceAddProductRow: $('#addNewProductRow'),
        invoiceAddProducts: $('#addProdFromInvoice'),
        invoiceProductModal: $('#invoiceProductModal'),
        submitInvoice: $('#submitInvoice'),
        invoiceForm: $('#addItemsForm'),
        depots: [],

        inventory_day: $("#inventory_day"),
        inventory_depot: $("#inventory_depot_id"),
        inventory_table: "#inventoryTable",
        inventory_table_url: $("#inventoryTable").data("ajax"),
        inventory_print: $("#printInventory"),

        stats_from: $("#stats_from"),
        stats_to: $("#stats_to"),
        stats_table: "#statsTable",
        stats_table_url: $("#statsTable").data("ajax"),
        stat_item: $("#stat_items"),
        stat_export: $("#xlsStatistic"),

        new_item_form: $("#addItemForm"),
        new_item_submit: $("#js_depot_add_new_item_form_btn"),
        itemCategories: {1: 'Výživa', 2: 'Nápoje', 3:'Dětský koutek', 4:'Dárkový poukaz', 5:'Solárium', 6:'Ostatní'}, // TODO grab from server
        init: async function(){
            self = this;

            this.depots = this.returnDepotList();

            this.user_role = await GYM._role();
            NProgress.configure({ parent: '.tool-bar .widget', minimum: 0.1, showSpinner: false });

            this.depot_table = new Tabulator(this.depot_table, {
                layout: 'fitColumns',
                placeholder:"Žádné skladové položky nejsou k dispozici.",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'Název', field: 'name', headerFilter:true,editable: false},
                    {title: 'Kategorie', field: 'category', headerFilter:true, editable: false, editor:'select',headerFilterParams: self.itemCategories,formatter:"lookup",formatterParams: self.itemCategories},
                    {title: 'Množství', field: 'stock', headerFilter:true, editable: false, formatter: this.returnFormattedStock},
                    {title: 'Rezervace', field: 'reserved', headerFilter:true, editable: false, formatter: this.returnFormattedReservation},
                    {title: 'Aktivní', field: 'active', headerFilter:true,editable: false,editor:'select',headerFilterParams:{"0":"Ne","1":"Ano"},formatter:"lookup",formatterParams:
                        {
                        "0": 'Ne',
                        "1": 'Ano'
                        }
                    },
                    {title: 'Poslední úprava', field: 'last_update', headerFilter:true,editor:this.dateEditor,editable: false,formatter:"datetime",formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD HH:mm:ss",
                        outputFormat:"DD.MM.YYYY HH:mm:ss",
                        invalidPlaceholder:"(invalid date)"
                        }
                    },
                    {title: '', align: 'right', formatter: this.returnTableButtons, width: 110}
                ],
                rowClick: function (e, row){
                    console.log($(e.target))
                    if(!$(e.target).hasClass('icon-delete')){
                        var el = row.getElement();
                        $(el).find('.open-product-detail').click();
                    }
                }
            });

            this.depot_table.setLocale("cs-cs");
            this.depot_table.setData(this.depot_table_url);
            this.depot_table.setSort([{column:"last_update",dir:"desc"}]);

            this.product_stock_table = new Tabulator(this.product_stock_table, {
                layout: 'fitColumns',
                placeholder:"Žádné skladové hodnoty nejsou k dispozici.",
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 100,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'Sklad', field: 'name', headerFilterPlaceholder: 'Filtrovat sklad', 
                    editor:"select", editable: false, headerFilter: true, headerFilterParams: this.returnDepotList},
                    {title: 'Množství', field: 'stock', formatter: this.returnFormattedStock},
                    {title: 'Rezervace', field: 'reservation', formatter: this.returnFormattedReservation},
                    {title: '', align: 'right', formatter: this.returnStocksButtons}
                ],
                renderStarted:function(){
                    $('#productModal').find(".tabulator-footer").hide();
                }
            });
            this.product_stock_table.setLocale("cs-cs");
            self.product_stock_table.setData([]);
            this.product_stock_table.setSort([{column:"stock",dir:"asc"}]);

            this.product_log_table = new Tabulator(this.product_log_table, {
                layout: 'fitColumns',
                placeholder:"Žádné záznamy.",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'Sklad', field: 'depotId', formatter: this.reurnLogDepotName, headerFilterPlaceholder: 'Filtrovat sklad', 
                    editor:"select", editable: false, headerFilter: true, headerFilterParams: this.returnDepotList},
                    {title: 'Množství', field: 'amount', formatter: this.returnLogFormattedStock},
                    {title: 'Datum', field: 'loggedOn', formatter: this.formatDate, headerFilter: true, headerFilterPlaceholder:"Hledat podle dne",editor:this.dateEditor,editable: false, formatterParams:
                        {
                        inputFormat:"YYYY-MM-DD",
                        outputFormat:"YYYY-MM-DD",
                        invalidPlaceholder:"(nesprávné datum)"
                        }
                    },
                    {title: 'Druh', field: 'direction', formatter: this.returnLogDirection, editor:"select", editable: false, headerFilter: true, headerFilterPlaceholder: 'Všechny druhy', headerFilterParams: {
                        '': '',
                        'to': 'Naskladnění',
                        'from': 'Vyskladnění',
                        'new': 'Nový příjem',
                        'sale': 'Prodej',
                        'reservation': 'Rezervace',
                        'release': 'Uvolnění'
                    }},
                    {title: 'Cena', field: 'buy_price', formatter: this.returnBuyPriceLog},
                    {title: 'Poznámka', field: 'note'},
                ],
                rowClick: function(e, row){
                    var d = row.getData();

                    self.log_modal.find('.log-id').val(d._id);
                    self.log_modal.find('textarea').val(d.note);
                    self.product_modal.find('.fader').fadeIn(function(){
                        self.log_modal.modal("show");
                    });

                    self.log_modal.modal('show');
                }
            });
            this.product_log_table.setLocale("cs-cs");
            this.product_log_table.setSort([{column:"stock",dir:"asc"}]);

            this.invoiceFormTable = new Tabulator(this.invoiceFormTable, {
                layout: 'fitColumns',
                placeholder:"Přidejte nějaké položky..",
                pagination: 'local',
                responsiveLayout: "collapse",
                paginationSize: 20,
                resizableColumns: true,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: '', align: 'right', formatter: this.returnInvoiceTableButtons, frozen: true, width: 65},
                    {title: 'Název', field: 'name', frozen: true, editable: function (c){
                        var d = c.getRow().getData();
                        return (typeof d.new_product !== "undefined");
                    }, editor: "input", formatter: function(c){
                        var d = c.getRow().getData(),
                            el = c.getRow().getElement();

                        if(typeof d.new_product !== "undefined"){
                            $(el).addClass("newprod");
                        }

                        return c.getValue();
                    }}
                ]
            });
            this.invoiceFormTable.setLocale("cs-cs");
            this.invoiceFormTable.setData([]);
            $.each(self.depots, function(id, depot_name){
                if(id != '') self.invoiceFormTable.addColumn({title: depot_name, field: 'depotid_'+id, formatter: self.returnInvoiceFormattedStock, editable: true, editor: 'number'}, false, 'name');
            });
            self.invoiceFormTable.addColumn({title: 'Nákupní cena za jednotku', field: 'buyPrice', editable: true, editor: 'number', formatter: this.returnBuyPriceInvoice, cellEdited:function(cell){
                    var d = cell.getRow().getData(),
                        v = parseFloat(cell.getValue()),
                        ov = typeof cell.getOldValue() !== "undefined" ? parseFloat(cell.getOldValue()) : 0;

                    var vat_val = d.vat_value;

                    var total = parseFloat($('.invoice-total').text());
                        total -= ov;
                        total += v;

                    var total_vat = parseFloat($('.invoice-total-vat').text());
                        total_vat -= (ov + (ov * parseFloat(vat_val)));
                        total_vat += (v + (v * parseFloat(vat_val)));

                    $('.invoice-total').text(total);
                    $('.invoice-total-vat').text(total_vat);
                }, rowDeleted: function(row){
                    var d = row.getData();

                    console.log("HIIHII");
                    console.log(d);
                    /*
                    v = parseFloat(cell.getValue()),
                    ov = typeof cell.getOldValue() !== "undefined" ? parseFloat(cell.getOldValue()) : 0;

                    var vat_val = d.vat_value;

                    var total = parseFloat($('.invoice-total').text());
                        total -= ov;
                        total += v;

                    var total_vat = parseFloat($('.invoice-total-vat').text());
                        total_vat -= (ov + (ov * parseFloat(vat_val)));
                        total_vat += (v + (v * parseFloat(vat_val)));

                    $('.invoice-total').text(total);
                    $('.invoice-total-vat').text(total_vat);
                    */
                }}, false);
            //self.invoiceFormTable.addColumn({title: '', align: 'right', formatter: this.returnInvoiceTableButtons}, false);
            self.invoiceFormTable.redraw(true);

            this.invoice_table = new Tabulator(this.invoice_table, {
                layout: 'fitColumns',
                placeholder:"Neexistují žádné skladové faktury..",
                resizableColumns: false,
                pagination: 'remote',
                paginationSize: 20,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'Název', field: 'invoice_name'},
                    {title: '#', field: 'invoice_number'},
                    {title: 'Poznámka', field: 'note'},
                    {title: 'Naskladnil', field: 'created_by_name'},
                    {title: 'Kdy', field: 'created_on', formatter: this.formatDate},
                    {formatter: self.tableHideIcon, align:"center", title:"Položky", headerSort:false, cellClick:function(e, row, formatterParams){
                        const id = row.getData().id;
                       $(".subTable" + id + "").toggle();  
                       
                       $(".subTable" + id + "").css("width", "100%");
                       $.each(self.sub_tables, function(i, table){
                           table.redraw(true); 
                       });

                    }}
                ],
                rowFormatter: function(row){
                    //create and style holder elements
                   var holderEl = document.createElement("div");
                   var tableEl = document.createElement("div");
            
                   const id = row.getData().id;

                   holderEl.style.boxSizing = "border-box";
                   holderEl.style.padding = "10px 30px 10px 10px";
                   holderEl.style.borderTop = "1px solid #333";
                   holderEl.style.borderBotom = "1px solid #333";
                   holderEl.style.background = "#ddd";
                   holderEl.classList = "sub-table-holder";
                   holderEl.style.display = "none";
                   holderEl.setAttribute('class', "subTable" + id + "");

                   tableEl.style.border = "1px solid #333";
                   tableEl.setAttribute('class', "subTable" + id + "");
                   tableEl.style.display = "none";
            
                   holderEl.appendChild(tableEl);
                   row.getElement().appendChild(holderEl);

                   var row_data = JSON.parse(row.getData().items);
                   var subTable = new Tabulator(tableEl, {
                       layout:"fitColumns",
                       langs: GYM.tabulator_czech,
                       layoutColumnsOnNewData:true,
                       data: row_data,
                       columns:[
                        {title:"Produkt", field:"name"}
                       ]
                   })

                   self.sub_tables.push(subTable);

                    $.each(self.depots, function(id, depot_name){
                        if(id != '') subTable.addColumn({title: depot_name, field: 'depotid_'+id, formatter: self.returnInvoiceFormattedStock}, false, 'name');
                    });
                    subTable.addColumn({title: 'Nákupní cena za jednotku', field: 'buyPrice', formatter: this.returnBuyPriceInvoice}, false);
                    subTable.redraw(true);
                }
            });
            this.invoice_table.setLocale("cs-cs");
            this.invoice_table.setData(self.invoice_table_data);

            // INVENTORY
            self.inventory_day.flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false,
                defaultDate: new Date()
            });

            this.inventory_table = new Tabulator(this.inventory_table, {
                layout: 'fitColumns',
                placeholder:"Pro zvolené rozsahy neexistují data..",
                resizableColumns: false,
                pagination: 'local',
                paginationSize: 50,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                printVisibleRows:false,
                printAsHtml:true,
                printHeader:"<h1>Inventura</h1>",
                columns: [
                    {title: 'Název položky', field: 'name', headerFilter: true, headerFilterPlaceholder: 'Filtrovat dle názvu'},
                    {title: 'Sklad', field: 'depot_name'},
                    {title: 'Rezervace', field: 'reserved', headerFilter:true, editable: false, formatter: this.returnFormattedReservation},
                    {title: 'Množství', field: 'stock', formatter: self.returnFormattedStock}
                ],
                ajaxParams:{day: self.inventory_day.val(), depot_id: self.inventory_depot.val()}
            });
            this.inventory_table.setLocale("cs-cs");
            this.inventory_table.setData(this.inventory_table_url);

            // STATS
            self.stats_from.flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false,
                defaultDate: new Date()
            });
            self.stats_to.flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false,
                defaultDate: new Date()
            });

            this.stats_table = new Tabulator(this.stats_table, {
                layout: 'fitColumns',
                placeholder:"Pro zvolené rozsahy neexistují data..",
                resizableColumns: false,
                pagination: 'remote',
                paginationSize: 50,
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                printVisibleRows:false,
                printAsHtml:true,
                printHeader:"<h1>Inventura</h1>",
                columns: [
                    {title: 'Název položky', field: 'item_name'},
                    {title: 'Množství', field: 'amount', formatter: self.returnStatAmount},
                    {title: 'Datum/čas', field: 'loggedOn', formatter: self.formatDate},
                    {title: 'Cena', field: 'salePrice', formatter: self.returnTotalPriceStats}
                ],
                ajaxParams:{from: self.stats_from.val(), to: self.stats_to.val(), item_id: self.stat_item.val()}
            });
            this.stats_table.setLocale("cs-cs");
            this.stats_table.setData(this.stats_table_url);

            this.fireEvents();
        },
        tableHideIcon: function (c){
            return "<i class='fa fa-eye-slash'></i>";
        },
        returnStatAmount: function(c){
            var d = c.getRow().getData();
            return d.amount + d.unit;
        },
        returnTotalPriceStats: function(c){
            var d = c.getRow().getData();
            var formatter = new Intl.NumberFormat('cs-CZ', {
                style: 'currency',
                currency: 'CZK',
                minimumFractionDigits: 2
              });
            return formatter.format(d.amount * d.salePrice);
        },
        returnInvoiceTableButtons: function (cell) {
            var ri = cell.getRow().getIndex();
            return '<a class="btn btn-xs btn-danger remove-invoice-row" onClick="DEPOT.removeInvoiceTableRow('+ri+')"><i class="icon-trash-o"></i></a>';
        },
        removeInvoiceTableRow: function (row_index){
            var row = self.invoiceFormTable.getRowFromPosition(String(row_index));
                console.log(row);
            self.invoiceFormTable.deleteRow(row_index);
            // TODO: reset totals
        },
        returnBuyPriceLog: function(cell){
            var d = cell.getRow().getData();
            if(d.direction === 'new' && typeof d.buyPrice != 'undefined'){
                var formatter = new Intl.NumberFormat('cs-CZ', {
                    style: 'currency',
                    currency: 'CZK',
                    minimumFractionDigits: 2
                  });
                return formatter.format(d.buyPrice * d.amount);
            }else if(d.direction === 'sale' && typeof d.salePrice != 'undefined'){
                var formatter = new Intl.NumberFormat('cs-CZ', {
                    style: 'currency',
                    currency: 'CZK',
                    minimumFractionDigits: 2
                  });
                return formatter.format(d.salePrice * d.amount);
            }else{
                return '--';
            }
        },
        returnBuyPriceInvoice: function(cell){
            var d = cell.getRow().getData();
            if(typeof d.buyPrice != 'undefined'){
                var formatter = new Intl.NumberFormat('cs-CZ', {
                    style: 'currency',
                    currency: 'CZK',
                    minimumFractionDigits: 2
                  });
                return formatter.format(d.buyPrice);
            }else{
                return '0 Kč';
            }
        },
        returnDepotList: function (cell){
            var data = {'':''};
            GYM._post('/admin/depot/get_all_depots_ajax', {}).done(function(res){
                $.each(res, function(i, depot){
                    data[depot.id] = depot.name;
                });
            });

            return data;
        },
        dateEditor: function(cell, onRendered, success, cancel, editorParams){
            //create and style editor
            var editor = document.createElement("input");

            flatpickr(editor, {
                dateFormat: "Y-m-d"
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
                success(editor.value);
            }

            editor.addEventListener("change", successFunc);

            //return the editor element
            return editor;
        },
        returnLogDirection: function(cell){
            var d = cell.getValue();
            var types = {'to' : 'Naskladnění', 'from' : 'Vyskladnění', 'new' : 'Nový příjem', 'sale':'Prodej', 'reservation':'Rezervace', 'release':'Uvolnění'};
            var classes = {'to': 'text-success', 'from': 'text-danger', 'new': 'text-primary', 'sale':'text-warning', 'reservation':'text-dark', 'release':'text-dark'};

            return '<span class="'+classes[d]+'">'+types[d]+'</span>';
        },
        formatDate: function(cell){
            var d = cell.getValue();
            return moment(d).format('D.M.YYYY HH:mm');
        },
        reurnLogDepotName: function (cell){
            var d = cell.getRow().getData();
            return d.depot_name;
        },
        returnInvoiceFormattedStock: function( cell ){
            var d = cell.getRow().getData();
            var field = cell.getColumn().getField();
            console.log(field);

            if(typeof d[field] != 'undefined'){
                return d[field] + ' ' + d.unit;
            }else{
                return '0 ' + d.unit;
            }
        },
        returnLogFormattedStock: function (cell) {
            var d = cell.getRow().getData();
            return d.amount + ' ' + d.unit;
        },
        returnFormattedStock: function(cell){
            var d = cell.getRow().getData();
            return d.stock + ' ' + d.unit;
        },
        returnFormattedReservation: function(c){
            var d = c.getRow().getData();
            return d.reserved + ' ' + d.unit;
        },
        returnTotalPrice: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var formatter = new Intl.NumberFormat('cs-CZ', {
                style: 'currency',
                currency: 'CZK',
                minimumFractionDigits: 2
              });
            cell._cell.row.data.total_price = (row_data.price * row_data.stock);
            return formatter.format(row_data.price * row_data.stock);
        },
        returnTableButtons: function(cell, params, onRendered){
            var row_data = cell._cell.row.data;
            var detail_button = '<a href="javascript:;" class="float-right open-product-detail" data-id="'+row_data.id+'" data-toggle="modal" data-target="#productModal"><i class="icon-eye mr-3"></i></a>&nbsp;';
            var delete_button = GYM._isAllowed('delete') ? '<a href="javascript:;" class="float-right delete-product" data-id="'+row_data.id+'"><i class="icon-delete mr-3 text-danger"></i></a>&nbsp;' :'';

            return detail_button + delete_button;
        },
        returnStocksButtons: function (cell) {
            var d = cell.getRow().getData();
            var add_button = GYM._isAllowed('create') ? '<a href="javascript:;" class="float-right move-product btn btn-primary btn-xs mr-1" data-id="'+d.item_id+'" data-depotid="'+d.depot_id+'">NOVÝ</a>' :'';
            var take_button = GYM._isAllowed('edit') ? '<a href="javascript:;" class="float-right take-product btn btn-danger btn-xs" data-id="'+d.item_id+'" data-depotid="'+d.depot_id+'">VYSKLADNIT</a>' :'';

            return take_button + add_button;
        },
        headerFilterUnitPrice: function(headerValue, rowValue, rowData, filterParams){
            return (rowValue.includes(headerValue.replace(',','.'))) || (headerValue.replace(',','.') == rowValue)
        },
        headerFilterTotalPrice: function(headerValue, rowValue, rowData, filterParams){
            rowValue = rowValue.toString();
            return (rowValue.includes(headerValue.replace(',','.'))) || (headerValue.replace(',','.') == rowValue)
        },
        getProductModalData: function(item_id){
            GYM._post('/admin/depot/get_item_info_ajax', {'item_id': item_id}).done(function(res){
                // Depots stock table in modal
                self.product_stock_table.setData(res.data.stocks);
                self.product_stock_table.setSort([{column:"stock",dir:"asc"}]);
                setTimeout(function(){ self.product_stock_table.redraw(true); }, 100);

                // Logs table in modal
                self.product_log_table.setData('/admin/depot/get_item_logs_ajax/' + res.data.id);

                // Detail form
                $.each(res.data, function(name, val){
                    if(name !== 'description' && name !== 'note' && name !== 'active' && name !== 'unit' && name !== 'vat_value'){
                        self.product_edit_form.find('input[name="'+name+'"]').val(val);
                    }else if(name == 'description' || name == 'note'){
                        self.product_edit_form.find('textarea[name="'+name+'"]').val(val);
                    }else if(name == 'active' || name == 'unit' || name == 'vat_value'){
                        self.product_edit_form.find('select[name="'+name+'"]').val(val).trigger('change');
                    }
                });

                self.product_modal_title.text(res.data.name);
            });
        },
        fireEvents: function(){

            $('body').on('click', '.delete-product', function(){
                var item_id = $(this).data("id");
                var agreed = confirm('Opravdu chcete vymazat skladovou položku? Přijdete o všechny její záznamy.');

                if(agreed){
                    GYM._post('/admin/depot/remove_item_ajax', {item_id: item_id}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Položka byla úspěšně vymazána!');
                            self.depot_table.setData(self.depot_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }
            });

            self.stat_export.click(function(){
                self.stats_table.download("xlsx", "Statistika_"+self.stat_item.find('option[selected="selected"]').text()+"_"+self.stats_from.val()+"_"+self.stats_to.val()+".xlsx", {sheetName:"Statistika"});
            });

            self.stat_item.change(function(){
                var val = $(this).val();
                self.stats_table.setData(self.stats_table_url, {from: self.stats_from.val(), to: self.stats_to.val(), item_id: val});
            });
            self.stats_from.change(function(){
                var val = $(this).val();
                self.stats_table.setData(self.stats_table_url, {from: val, to: self.stats_to.val(), item_id: self.stat_item.val()});
            });
            self.stats_to.change(function(){
                var val = $(this).val();
                self.stats_table.setData(self.stats_table_url, {from: self.stats_from.val(), to: val, item_id: self.stat_item.val()});
            });

            self.inventory_depot.change(function(){
                var val = $(this).val();
                self.inventory_table.setData(self.inventory_table_url, {day: self.inventory_day.val(), depot_id: val});
            });
            self.inventory_day.change(function(){
                var val = $(this).val();
                self.inventory_table.setData(self.inventory_table_url, {day: val, depot_id: self.inventory_depot.val()});
            });

            self.inventory_print.click(function(){
                var table = self.inventory_table.getHtml();
                var $printerDiv = $('<div class="printContainer"></div>');
                
                $printerDiv.append("<h1 style='margin-bottom: 20px;'>Inventura / "+moment(self.inventory_day.val()).format("DD. MM. YYYY")+"");
                $printerDiv.html(table); 

                $('body').append($printerDiv).addClass("printingContent");
            
                window.print();
                $printerDiv.remove();
                $('body').removeClass("printingContent");
            });

            $('#buttonlogtab').click(function(){ setTimeout(function(){ self.product_log_table.redraw(true); }, 450); });
            $('#buttonstocktab').click(function(){ setTimeout(function(){ self.product_stock_table.redraw(true); }, 450); });

            $('.switch-to-invoice').click(function(){ setTimeout(function(){ 
                    self.invoiceFormTable.redraw(true);
                    self.invoice_table.redraw(true); 
                }, 650);
             });

             $(".switch-to-statistics").click(function(){
                setTimeout(function(){ 
                    self.stats_table.redraw(true); 
                }, 450);
             })

            $('.switch-to-inventory').click(function(){ setTimeout(function(){ self.inventory_table.redraw(true); }, 450); });
            $('.switch-to-statistic').click(function(){ setTimeout(function(){ self.stats_table.redraw(true); }, 450); });

            self.submitInvoice.click(function(){
                var invoice_number = self.invoiceForm.find('input[name="invoice_number"]').val(),
                    invoice_name = self.invoiceForm.find('input[name="invoice_name"]').val(),
                    note = self.invoiceForm.find('textarea[name="note"]').val();

                if(invoice_number && invoice_number != ""){
                    self.invoiceForm.find('input[name="invoice_number"]').removeClass("invalid");

                    var items = self.invoiceFormTable.getData();
                    if(items.length > 0){

                        var invalid_items = 0;
                        $.each(items, function(i, item){
                            if(!item.buyPrice){
                                invalid_items++;
                            }else{
                                if(invalid_items > 0) invalid_items--;
                            }
                        });

                        if(invalid_items <= 0){
                            GYM._post('/admin/depot/submit_invoice_ajax', {'invoice':invoice_number, 'invoice_name':invoice_name, 'note':note, 'items':items}).done(function (res) {
                                if(!res.error){
                                    N.show("success", "Položky byly úspěšně naskladněny!");

                                    // clean up
                                    self.invoiceForm.find('input[name="invoice_number"]').val("");
                                    self.invoiceForm.find('input[name="invoice_name"]').val("");
                                    self.invoiceForm.find('textarea[name="note"]').val("");
                                    self.invoiceFormTable.setData([]);
                                    self.depot_table.setData(self.depot_table_url); // update main table
                                    self.depot_table.redraw(true);
                                }else{
                                    N.show("error", GYM.general_ajax_error);
                                }
                            });
                        }else{
                            N.show("error", "U jedné nebo více položek chybí nákupní cena za jednotku!");
                        }

                    }else{
                        N.show("error", "Formulář neobsahuje žádné skladové položky!");
                    }

                }else{
                    self.invoiceForm.find('input[name="invoice_number"]').addClass("invalid");
                    N.show("error", GYM.general_form_error);
                }
            });

            self.invoiceProductModal.on('hidden.bs.modal', function(){
                self.invoiceNewProductsContainer.html('');
                $('#existing_products').val(null).trigger('change');
            });

            self.invoiceAddProducts.click(function(e){
                e.preventDefault();
                
                var existing_rows = $('#existing_products').val();
                var new_items = [];
                var depot_items = [];

                var new_rows = self.invoiceNewProductsContainer.find('.product-row');

                if(new_rows.length > 0){
                    // There are new items to be submitted
                    $.each(new_rows, function(i, row){

                        var inputs = $(row).find('input, select');
                        GYM._validateInputs(inputs);

                        if($(row).find('.invalid').length <= 0){

                            var new_item = {
                                'new_product': '1',
                                'name': $(row).find('input[name="name"]').val(),
                                'unit': $(row).find('select[name="unit"]').val(),
                                'sale_price': $(row).find('input[name="sale_price"]').val(),
                                'vat_value': $(row).find('select[name="vat_value"]').val(),
                                'sale_price_vat': $(row).find('input[name="sale_price_vat"]').val()
                            };

                            $.each(self.depots, function(id, depot_name){
                                if(id != '') new_item['depotid_'+id] = 0;
                            });

                            new_items.push(new_item);
                        }else{
                            N.show('error', GYM.general_form_error);
                            return false;
                        }
                    });
                }

                if(existing_rows.length > 0){
                    $.each(existing_rows, function(i, item){
                        GYM._post('/admin/depot/get_item_info_ajax', {'item_id': item}).done(function(res){
                            if(!res.error){
                                var depot_item = {
                                    'id': item,
                                    'name': res.data.name,
                                    'unit': res.data.unit,
                                    'sale_price': res.data.sale_price,
                                    'vat_value': res.data.vat_value,
                                    'sale_price_vat': res.data.sale_price_vat,
                                };

                                $.each(self.depots, function(id, depot_name){
                                    if(id != '') depot_item['depotid_'+id] = 0;
                                });

                                if(self.invoiceProductModal.find('.invalid').length <= 0) self.invoiceFormTable.addData(depot_item);
                            }
                        });
                    });
                }

               //console.log(new_items, depot_items);
                if(self.invoiceProductModal.find('.invalid').length <= 0){
                    self.invoiceFormTable.addData(new_items);
                    self.invoiceProductModal.modal("hide");
                }
            });

            self.invoiceAddProductRow.click(function(){
                var template = self.invoiceProductRowTemplate.clone();
                self.invoiceNewProductsContainer.append(template.html());
            });

            $('body').on('change', '.sale-price-new', function(){
                var parent = $(this).parents().eq(2);

                var vat = parent.find('.vat-value-new').val();
                if(vat){
                    parent.find('.sale-price-vat-new').val( (parseInt($(this).val()) + (parseInt($(this).val()) * parseFloat(vat))) );
                }else{
                    parent.find('.sale-price-vat-new').val( (parseInt($(this).val()) + (parseInt($(this).val()) * 0.21)) );
                }
            });
            $('body').on('change', '.sale-price-vat-new', function(){
                var parent = $(this).parents().eq(2);

                var vat = parent.find('.vat-value-new').val();
                if(vat){
                    parent.find('.sale-price-new').val( (parseInt($(this).val()) - (parseInt($(this).val()) * parseFloat(vat))) );
                }else{
                    parent.find('.sale-price-new').val( (parseInt($(this).val()) - (parseInt($(this).val()) * 0.21)) );
                }
            });
            $('body').on('change', '#vat-value-new', function(){
                var parent = $(this).parents().eq(2);
                var nonvat = parent.find('.sale-price-new').val(),
                    vat = parent.find('.sale-price-vat-new').val(),
                    v = $(this).val();

                if(vat){
                    parent.find('.sale-price-new').val( (parseInt(vat) - (parseInt(vat) * parseFloat(v))) );
                }else if(!vat && nonvat){
                    parent.find('.sale-price-vat-new').val( (parseInt(nonvat) + (parseInt(nonvat) * parseFloat(v))) );
                }
            });

            $('body').on('click', '.delete-row > .btn', function(){
                var parent = $(this).parents().eq(1);
                parent.remove();
            });

            self.product_modal_submit.click(function(e){
                e.preventDefault();
                
                var inputs = self.product_edit_form.find('input, select, textarea');
                var item_id = self.product_edit_form.find('.item-id').val();
                GYM._validateInputs(inputs);

                if( self.product_edit_form.find('.invalid').length <= 0 ){
                    var values = {};
                    $.each(inputs, function(i, input){ if(typeof $(input).attr('name') != 'undefined') values[$(input).attr('name')] = $(input).val(); });

                    GYM._post('/admin/depot/edit_item_ajax/' + item_id, values).done(function(res){
                        if(!res.error){
                            N.show('success', 'Operace proběhla úspěšně!');
                            self.product_modal.modal('hide');
                            self.depot_table.setData(self.depot_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            self.move_product_submit.click(function(e){
                e.preventDefault();

                var inputs = self.move_product_modal.find('input:visible, select:visible, textarea:visible');
                var item_id = self.move_product_modal.find('.item-id').val();
                GYM._validateInputs(inputs);

                if( self.move_product_modal.find('.invalid').length <= 0 ){
                    var values = {};
                        values.item_id = item_id;
                    $.each(inputs, function(i, input){ values[$(input).attr('name')] = $(input).val(); });

                    GYM._post('/admin/depot/move_product_ajax', values).done(function(res){
                        if(!res.error){
                            N.show('success', 'Operace proběhla úspěšně!');
                            self.move_product_modal.modal('hide');
                            self.getProductModalData(item_id);
                            self.depot_table.setData(self.depot_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            self.take_product_submit.click(function(e){
                e.preventDefault();

                var inputs = self.take_product_modal.find('input:visible, select:visible, textarea:visible');
                var item_id = self.take_product_modal.find('.item-id').val();
                var depot_id = self.take_product_modal.find('.depot-id').val();
                GYM._validateInputs(inputs);

                if( self.take_product_modal.find('.invalid').length <= 0 ){
                    var values = {};
                        values.item_id = item_id;
                        values.depot_id = depot_id;
                    $.each(inputs, function(i, input){ values[$(input).attr('name')] = $(input).val(); });

                    GYM._post('/admin/depot/take_product_ajax', values).done(function(res){
                        if(!res.error){
                            N.show('success', 'Operace proběhla úspěšně!');
                            self.take_product_modal.modal('hide');
                            self.getProductModalData(item_id);
                            self.depot_table.setData(self.depot_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            $('#productModal').on('click', '.move-product', function(){
                var item_id = $(this).data("id");
                var depot_id = $(this).data("depotid");
                self.product_modal.find('.fader').fadeIn(function(){
                    self.move_product_modal.modal("show");
                    self.move_product_modal.find('.item-id').val(item_id);
                    self.move_product_modal.find('.depot-id').val(depot_id);
                    self.move_product_modal.find('select[name="to_depot_id"]').val(depot_id).trigger('change');
                });
            });
            $('#productModal').on('click', '.take-product', function(){
                var item_id = $(this).data("id");
                var depot_id = $(this).data("depotid");
                self.product_modal.find('.fader').fadeIn(function(){
                    self.take_product_modal.modal("show");
                    self.take_product_modal.find('.item-id').val(item_id);
                    self.take_product_modal.find('.depot-id').val(depot_id);
                    self.take_product_modal.find('select[name="to_depot_id"]').val(depot_id).trigger('change');
                });
            });

            self.move_product_modal.on('hidden.bs.modal', function(){
                self.product_modal.find('.fader').fadeOut();
                self.move_product_modal.find('form')[0].reset();
            });
            self.take_product_modal.on('hidden.bs.modal', function(){
                self.product_modal.find('.fader').fadeOut();
                self.take_product_modal.find('form')[0].reset();
            });
            self.log_modal.on('hidden.bs.modal', function(){
                self.product_modal.find('.fader').fadeOut();
                self.log_modal.find('textarea')[0].val('');
            });

            // Depot id selection in movement modal change
            self.move_product_modal.on('change', '#from_depot_id', function(){
                var s = $(this);
                var depot_id = $(this).val();
                var item_id = self.move_product_modal.find('.item-id').val();
                GYM._post('/admin/depot/get_depot_item_stocks', {'depot_id': depot_id, 'item_id': item_id}).done(function(res){
                    if(!res.error){
                        // set max value (to not overstock)
                        s.removeClass('invalid');
                        s.parent().find('.select2.select2-container').removeClass('invalid');
                        self.move_product_modal.find('input[name="quantity"]').attr('max', (res.stock - res.reserved));
                    }else{
                        s.addClass('invalid');
                        s.parent().find('.select2.select2-container').addClass('invalid');
                        N.show('error', 'Na vybraném skladě nejsou žádné skladové zásoby této položky, vyberte jiný sklad pro přesun.');
                    }
                });
            });

            // PROD MODAL
            self.product_modal.on('show.bs.modal', function ( evt ) {
                var btn = $(evt.relatedTarget),
                    item_id = btn.data('id');

                self.product_edit_form.find('.item-id').val(item_id);
                self.getProductModalData(item_id);
            });

            self.new_item_submit.click(function(e){
                e.preventDefault();
                var form = self.new_item_form,
                    url = $(this).data("ajax"),
                    inputs = self.new_item_form.find("input, select, textarea");

                GYM._validateInputs(inputs);

                if(self.new_item_form.find(".invalid").length <= 0){
                    var formData = new FormData(),
                        form_inputs = self.new_item_form.find('input, select, textarea');

                    $.each(form_inputs, function(i, input){
                        if(typeof $(input).attr('name') != 'undefined') formData.append($(input).attr('name'), $(input).val());
                    });

                    GYM._upload(url, formData).done(function(res){
                        if(!res.error){
                            N.show('success', 'Nová skladová položka byla vytvořena.');

                            // refresh data
                            self.depot_table.setData(self.depot_table_url);
                            self.depot_table.redraw(true);

                            // Clean form
                            form[0].reset();
                        }else{
                            N.show('error', res.error);
                        }
                    });
                }
            });

            $(".js-depot-home-clear-filter").click(function(){
                var tableId = $(this).closest('.tab-pane').find('.table').attr('id');
                var dTable = null;

                if(tableId == 'depotTable'){
                    dTable = eval('DEPOT.depot_table');
                }

                dTable.clearHeaderFilter();
                dTable.clearSort();
                dTable.setSort([{column:"last_update",dir:"desc"}]);
                dTable.redraw(true);
            });

            $('.switch-to-depot-all').click(function(){
                setTimeout(function(){
                    self.depot_table.redraw(true);
                }, 150);
            });

            $('#sale_price').change(function(){
                var vat = $('#vat_value').val();
                if(vat){
                    $('#sale_price_vat').val( (parseInt($(this).val()) + (parseInt($(this).val()) * parseFloat(vat))) );
                }else{
                    $('#sale_price_vat').val( (parseInt($(this).val()) + (parseInt($(this).val()) * 0.21)) );
                }
            });
            $('#sale_price_vat').change(function(){
                var vat = $('#vat_value').val();
                if(vat){
                    $('#sale_price').val( (parseInt($(this).val()) - (parseInt($(this).val()) * parseFloat(vat))) );
                }else{
                    $('#sale_price').val( (parseInt($(this).val()) - (parseInt($(this).val()) * 0.21)) );
                }
            });
            $('#vat_value').change(function(){
                var nonvat = $('#sale_price').val(),
                    vat = $('#sale_price_vat').val(),
                    v = $(this).val();

                if(vat){
                    $('#sale_price').val( (parseInt(vat) - (parseInt(vat) * parseFloat(v))) );
                }else if(!vat && nonvat){
                    $('#sale_price_vat').val( (parseInt(nonvat) + (parseInt(nonvat) * parseFloat(v))) );
                }
            });

            $('#sale_price_edit').change(function(){
                var vat = $('#vat_value_edit').val();
                if(vat){
                    $('#sale_price_vat_edit').val( (parseInt($(this).val()) * (1 + parseFloat(vat))) );
                }else{
                    $('#sale_price_vat_edit').val( (parseInt($(this).val()) * (1 + 0.21)) );
                }
            });
            $('#sale_price_vat_edit').change(function(){
                var vat = $('#vat_value_edit').val();
                if(vat){
                    $('#sale_price_edit').val( (parseInt($(this).val()) / (1 + parseFloat(vat))) );
                }else{
                    $('#sale_price_edit').val( (parseInt($(this).val()) / (1 + 0.21)) );
                }
            });
            $('#vat_value_edit').change(function(){
                var nonvat = $('#sale_price_edit').val(),
                    vat = $('#sale_price_vat_edit').val(),
                    v = $(this).val();

                if(vat){
                    $('#sale_price_edit').val( (parseInt(vat) / (1 + parseFloat(v))) );
                }else if(!vat && nonvat){
                    $('#sale_price_vat_edit').val( (parseInt(nonvat) * (1 + parseFloat(v))) );
                }
            });

        }
    }
}());

DEPOT.init();