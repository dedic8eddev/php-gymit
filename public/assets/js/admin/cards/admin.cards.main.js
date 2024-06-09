'use strict';

var CARDS = CARDS || (function () {
    var self;
    return {
        pair_modal: $('#pairNewCardModal'),
        pair_modal_submit: $('#pairNewCardSubmit'),
        pair_modal_form: $('#pairNewCardForm'),

        reader_select: $('select[name="reader_id"]'),
        selected_reader: PERSONIFICATORS.getSessionReader() || $('select[name="reader_id"]').val(),
        
        cards_table: '#cardsTable',
        cards_table_data: $('#cardsTable').data("ajax"),

        cardField: $('#readerInput'),
        init: async function(){
            self = this;

            this.user_role = await GYM._role();
            NProgress.configure({ parent: '.tool-bar .widget', minimum: 0.1, showSpinner: false });

            this.cards_table = new Tabulator(this.cards_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné karty",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting:true,
                ajaxFiltering:true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: 'KLIENT', field: 'client_name', headerFilter:true, formatter: this.returnName},
                    {title: 'ID KARTY', field: 'card_id', headerFilter: true},
                    {title: '', align: 'right', headerSort:false, formatter: this.returnTableButtons}
                ]
            });
            this.cards_table.setLocale("cs-cs");
            this.cards_table.setData(this.cards_table_data);

            self.reader_select.val(self.selected_reader);

            this.fireEvents();
        },
        returnName: function(cell){
            var d = cell.getRow().getData();
            return d.first_name + ' ' + d.last_name;
        },
        returnTableButtons: function(cell){
            var d = cell.getRow().getData();
            var delete_btn = '<a href="javascript:;" class="btn-fab btn-fab-sm shadow btn-danger remove-card" data-id="'+d.user_id+'"><i class="icon-delete"></i></a></a>';
            //var edit_btn = '<a href="/admin/cards/settings/'+d.id+'/" class="btn-fab btn-fab-sm shadow btn-primary"><i class="icon-mode_edit"></i></a></a>';
            
            return delete_btn;
        },
        fillCardField: function(value){
            self.cardField.val(value);
        },
        fireEvents: function(){
            self.pair_modal.on('hidden.bs.modal', function(){
                self.pair_modal_form[0].reset();
                self.cards_table.redraw(true);
            
                var readerId = self.reader_select.val();
                if(readerId){
                    PERSONIFICATORS.stopPolling(readerId);
                }
                $('#readerInput').val('');
            });

            self.pair_modal.on('shown.bs.modal', function(){
                console.log("OPEN", self.selected_reader);
                if(self.selected_reader){
                    PERSONIFICATORS.startPolling(self.selected_reader, CARDS.fillCardField);
                }
            });

            self.reader_select.change(function(){
                PERSONIFICATORS.stopPolling(self.selected_reader);
                PERSONIFICATORS.setSessionReader($(this).val());
                PERSONIFICATORS.startPolling($(this).val());

                self.selected_reader = $(this).val();
            });

            $("body").on("click", ".remove-card", function(){
                var user_id = $(this).data("id");
                
                GYM._post("/admin/cards/remove_pair_ajax", {'user_id':user_id}).done(function(res){
                    if( ! res.error){
                        N.show('success', 'Karta byla úspěšně přidána!', false, true); // reload
                        self.pair_modal.modal("hide");

                        self.cards_table.redraw(true);
                        self.cards_table.setData(self.cards_table_data);
                    }else{
                        N.show('error', 'Nepovedlo se přidat kartu, zkuste to znovu nebo později.');
                    }
                });
            });

            self.pair_modal_submit.click(function(e){
                e.preventDefault();

                var client = self.pair_modal.find('select[name="client_id"]').val(),
                    card = self.pair_modal.find('input[name="card_id"]').val(),
                    url = $(this).data('ajax');

                if(client.length > 0 && card.length > 0){
                    GYM._post(url, {'client_id':client, 'card_id':card}).done(function(res){
                        if( ! res.error){
                            N.show('success', 'Karta byla úspěšně přidána!'); // reload
                            self.pair_modal.modal("hide");

                            self.cards_table.redraw(true);
                            self.cards_table.setData(self.cards_table_data);
                        }else{
                            N.show('error', 'Nepovedlo se přidat kartu, zkuste to znovu nebo později.');
                        }
                    });
                }else{
                    N.show('error', 'Musí být vyplněna obě pole!');
                }
            });
        }
    }
}());

CARDS.init();