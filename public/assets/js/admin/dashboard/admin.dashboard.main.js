'use strict';

var DASHBOARD = DASHBOARD || (function () {
    var self;
    return {

        // Remote modal
        remote_modal: $('#modal, #modalOverModal'),   
        btn_modal_submit: $('#modalSubmit'),
        btn_modal_over_modal_submit: $('#modalOverModalSubmit'),

        // Cards
        card_reader: PERSONIFICATORS.getSessionReader(),
        cardPoolingInterval: 3500,

        // Rooms
        room_expand: $('.room-expand'),
        room_tab_open: true,
        roomUpdateInterval: 3000,  

        // Add items to QUE
        addList_table: $('#addListTable'),      

        initializedQue: false,

        init: async function(){
            self = this;
            
            // set card reader if not in local storage
            if(self.card_reader == null) self.card_reader = await PERSONIFICATORS.chooseSessionReader();

            this.user_role = await GYM._role();
            NProgress.configure({ parent: '.tool-bar .widget', minimum: 0.1, showSpinner: false });

            this.fireEvents();
        },
        fireEvents: function(){

            // Cards pooling
            PERSONIFICATORS.startPolling(self.card_reader, self.openClientModalByCard, self.cardPoolingInterval);
            // Rooms pooling
            self.pullRoomData();  

            // Remote Modal
            $('body').on('click', '[data-toggle="modal"]', function(e){
                var modal_id = $(this).data("target");
                var $this = this;
                // loading spinner
                $(modal_id+' .modal-body').append(GYM.loading_spinner);
                $(modal_id+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2({ dropdownParent: $(this).parent() });
                    if($($this).is('#btnOpenAddQueModal')) QUE.initAddItemsModal();
                });
            }); 

            self.remote_modal.on('show.bs.modal', function(e){
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                if($(e.target).is('#modal')){ 
                    if($(e.relatedTarget).data('modal-submit')=='') self.btn_modal_submit.hide();
                    else self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit')).show();
                    PERSONIFICATORS.stopPolling(self.card_reader);
                }
                else self.btn_modal_over_modal_submit.text($(e.relatedTarget).data('modal-submit')).attr('id',$(e.relatedTarget).data('modal-submit-id'));
            });

            self.remote_modal.on('hidden.bs.modal', function(e){
                if($(e.target).is('#modal')){
                    PERSONIFICATORS.startPolling(self.card_reader, self.openClientModalByCard, self.cardPoolingInterval);
                    self.pullRoomData();  
                }
                $(e.target).find('.modal-body').html('');
            });            

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            }); 
            
            self.remote_modal.on('click', '#v-pills-cart-tab', function(){
                if(!self.initializedQue) QUE.init();
                self.initializedQue=true;
            });

            self.remote_modal.on('click', '#v-pills-moving-history-tab', function(){
                GYM._post('/admin/dashboard/get_client_moving_history_ajax', {card_id: $('#card_id').text()}).done(function(res){
                    $('#v-pills-moving-history').find('.card-body').html(res.data);
                });
            });


            self.remote_modal.on('hidden.bs.modal', function (event) {
                if ($('.modal:visible').length) { // modal over modal
                    $('body').addClass('modal-open');
                }
            });             

            // Rooms
            self.room_expand.click(function(){
                var p = $(this).parent(),
                    icon = p.find('i');

                if($(icon).hasClass('icon-keyboard_arrow_down')) $(icon).removeClass('icon-keyboard_arrow_down').addClass('icon-keyboard_arrow_up');
                else $(icon).removeClass('icon-keyboard_arrow_up').addClass('icon-keyboard_arrow_down');
            }); 

        },
        pullRoomData: function(){
            var data_pull = setInterval(function(){
                if(!($("#modal").data('bs.modal') || {})._isShown){ // If modal is not open
                    GYM._post('/admin/clients/get_room_occupation', {}).done(function(res){
                        if(!res.error) self.renderRooms(res);
                    });
                }else clearInterval(data_pull);
            }, self.roomUpdateInterval);
        },
        renderRooms: function(data){
            $.each(GYM._json(data).data, function(i, room){
                var id = room.id,
                    card = $('#room_' + id),
                    parent = card.parent(),
                    expander = parent.find('.room-expand'),
                    subtitle = parent.find('.card-subtitle');

                if(card.length > 0){
                    // element exists
                    var table = card.find('table tbody');
                    table.html('');

                    var total = 0;
                    if(room.occupation !== null){
                        $.each(room.occupation, function(x, checkin){
                            table.append(checkin);
                            total++;
                        });

                        if(total == 1) subtitle.text('1 zákazník');
                        else if (total > 1 && total < 5) subtitle.text(total + ' zákazníci');
                        else if (total > 4) subtitle.text(total + ' zákazníků');
                        if(!card.hasClass("show")) expander.click();
                    }else{
                        if(card.hasClass("show")) expander.click();
                        subtitle.text('Místnost je prázdná');
                    }
                }

            });
        }, 
        openClientModalByCard: function(cardId){
            let title = 'Detail zákazníka',
                submit = '',
                remote = `/admin/dashboard/client-modal/${cardId}?type=card`;
            
            $('body').append(`<a id="btnFakeBtn" class="d-none" href="javascript:;" data-toggle="modal" data-remote="${remote}" data-target="#modal" data-modal-title="${title}" data-modal-submit="${submit}"></a>`);
            $('#btnFakeBtn').click().remove();
        },
    }
}());

DASHBOARD.init();

var QUE = QUE || (function () {
    var self;
    return {
        // Buttons
        btnAddServiceItem: '#btnAddServiceItem',
        btnAddDepotItem: '#btnAddDepotItem',
        btnAddItems2Que: '#btnAddItems2Que',
        btnGo2Checkout: '#go2Checkout',

        clientQue_table: '#clientQueTable',
        addList_table: '#addListTable',

        init: async function(params){
            self = this;

            this.fireEvents();
        },
        fireEvents: function(){

            // Add items to addList
            DASHBOARD.remote_modal.on('click', self.btnAddServiceItem, function(){
                self.addItemToAddList($("#service_item option:selected"));
            });
            DASHBOARD.remote_modal.on('click', self.btnAddDepotItem, function(){
                self.addItemToAddList($("#depot_item option:selected"),true);
            }); 
            
            DASHBOARD.remote_modal.on('change', `${self.addList_table} .input-count`, function(){
                var count = parseInt($(this).val()),
                    item_price = parseInt($(this).closest('td').data('price')),
                    item_stock = parseInt($(this).closest('td').data('stock')),
                    price_col = $(this).closest('td').next();
                if(item_stock > 0 && count > item_stock){
                    N.show('error', `Tato položka je pouze ${item_stock}x na skladě.`);
                    $(this).val(item_stock);
                    count = item_stock;
                } else if(count > 999){ // validation (max 999 items)
                    $(this).val(999);
                    count = 999;
                } else if (count < 1){ // validation (min 1 item)
                    $(this).val(1);
                    count = 1;                    
                }
                price_col.text(item_price * count);
            });            

            // Submit items 2 que
            DASHBOARD.remote_modal.on('click', self.btnAddItems2Que, function(){
                self.addItems2Que(); 
            });

            $('#v-pills-cart-tab').on('click', '[data-toggle="modal"]', function(e){
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                    $('.select2').select2({ dropdownParent: $(this).parent() });
                });
            }); 

            $(self.btnGo2Checkout).click(function(){
                $('#modal').modal('hide'); // hide modal before redirect to payment
            });
            
        },
        getInputCount: function(value=1){
            return `<input min="1" max="999" class='form-control input-count' type='number' value='${value}' />`;
        },
        getInputDiscount: function(value=0){
            return `<input step="5" min="0" max="100" class='form-control input-discount' type='number' value='${value}' />`;
        },        
        addItemToAddList: async function(item,depot=false){
            let type = depot ? 'depot' : 'service';

            let itemId = item.attr("value"),
                clientId = $('#client_id').val(),
                cardId = $('#card_id').text(),            
                addList_item = depot ? $(self.addList_table).find(`[data-id='${itemId}'][data-depotid='1']`) : $(self.addList_table).find(`[data-id='${itemId}'][data-service='1']`),
                icon_remove = `<i class="icon-close text-danger" onclick="QUE.removeItemFromAddList(this);" style="cursor:pointer;"></i>`;
            
            if(addList_item.length){ // already in summary table
                let discount = parseInt(addList_item.closest('td').next().find('.input-discount').val()), // percentage
                    item_price = parseFloat(addList_item.data('price')),
                    addList_item_count = parseInt(addList_item.find('.input-count').val()) + 1,
                    discount_value = item_price * (discount / 100),
                    price = addList_item_count * (item_price - discount_value);

                addList_item.find('.input-count').val(addList_item_count);
                addList_item.closest('td').next().next().text(GYM._separateThousands(self.priceFormat(price)));                
            } else { // not in summary, get data and append
                if(depot){
                    await GYM._post('/admin/depot/get_item_info_simple_ajax', {client_id:clientId,card_id:cardId,item_id:itemId}).done(function (res) {
                        $.each(res.data.stocks, function(i, depot){
                            if(depot.depot_id == self.depot_select.val()){
                                if(res.benefit){
                                    //self.append2Note(`${item.text()} - Benefit (sleva ${res.benefit.discount} %)`);
                                    let discount_value = res.data.sale_price_vat * (res.benefit.discount / 100),
                                        item_price = res.data.sale_price_vat - discount_value;
                                    $(self.addList_table).append(`<tr><td>${item.text()}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-benefit='${res.benefit.id}' data-vat='${res.data.vat_value}' data-price='${self.priceFormat(res.data.sale_price_vat)}' data-stock='${parseInt(depot.stock)}' data-depotid='${depot.depot_id}' data-id='${item.attr("value")}'>${self.getInputCount()}</td><td>${self.getInputDiscount(res.benefit.discount)}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item_price))}</td><td>${icon_remove}</td></tr>`);
                                } else $(self.addList_table).append(`<tr><td>${item.text()}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-vat='${res.data.vat_value}' data-price='${self.priceFormat(res.data.sale_price_vat)}' data-stock='${parseInt(depot.stock)}' data-depotid='${depot.depot_id}' data-id='${item.attr("value")}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(res.data.sale_price_vat))}</td><td>${icon_remove}</td></tr>`);
                            }
                        });
                    });
                } else {
                    await GYM._post('/admin/pricelist/get_checkout_item_info_ajax', {client_id:clientId,card_id:cardId,item_id:itemId}).done(function (res) {
                        let p = res.data;
                        if(res.benefit){
                            //self.append2Note(`${item.text()} - Benefit (sleva ${res.benefit.discount} %)`);
                            let discount_value = p.vat_price * (res.benefit.discount / 100),
                                item_price = p.vat_price - discount_value;
                                $(self.addList_table).append(`<tr><td>${item.text()}</td><td class='text-right' data-id='${itemId}' data-benefit='${res.benefit.id}' data-service-type='${p.service_type}' data-service-subtype='${p.service_subtype}' data-price='${self.priceFormat(p.vat_price)}' data-vat='${p.vat}' data-service='1'>${self.getInputCount()}</td><td>${self.getInputDiscount(res.benefit.discount)}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item_price))}</td><td>${icon_remove}</td></tr>`);
                        } else $(self.addList_table).append(`<tr><td>${item.text()}</td><td class='text-right' data-id='${itemId}' data-service-type='${p.service_type}' data-service-subtype='${p.service_subtype}' data-price='${self.priceFormat(p.vat_price)}' data-vat='${p.vat}' data-service='1'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(p.vat_price))}</td><td>${icon_remove}</td></tr>`);
                    });                    
                }   
            }             
       
        },          
        addItems2Que: function(){
            var formData = new FormData();
            if ($(self.addList_table).find('td').length===0){
                N.show('error', 'Vyberte prosím alespoň jednu položku!');
                return false;   
            }

            formData.set('card_id',$('#card_id').text());
            formData.set('card_credit',parseFloat($('#card_credit').text()));
            formData.set('membership_id',$('#membership_id').text());

            // items
            $(self.addList_table).find('td:first-child').next('td').each(function () {
                let itemName = $(this).prev().contents().get(0).nodeValue, // name without child elements (eg. Depot name)
                    benefitId = $(this).data('benefit') ? $(this).data('benefit') : false,
                    vat = parseFloat($(this).data('vat')),
                    value = parseFloat($(this).data('price')) / (1 + vat),
                    vat_value = value * vat,
                    amount = parseInt($(this).find('.input-count').val()),
                    discount = parseInt($(this).next().find('.input-discount').val());      

                if($(this).data('depot')){ // DEPOT ITEMS
                    let depotID = $(this).data('depotid'),
                        depotItemID = $(this).data('id');
                    formData.append(`items[depot][${depotID}][${depotItemID}][name]`, itemName);
                    formData.append(`items[depot][${depotID}][${depotItemID}][benefit]`, benefitId);
                    formData.append(`items[depot][${depotID}][${depotItemID}][vat]`, vat);
                    formData.append(`items[depot][${depotID}][${depotItemID}][vat_value]`, vat_value);
                    formData.append(`items[depot][${depotID}][${depotItemID}][amount]`, amount);
                    formData.append(`items[depot][${depotID}][${depotItemID}][discount]`, discount);
                } else if ($(this).data('service')){ // SERVICE ITEMS
                    let serviceID = $(this).data('id');
                    formData.append(`items[service][${serviceID}][name]`, itemName);                        
                    formData.append(`items[service][${serviceID}][benefit]`, benefitId);
                    formData.append(`items[service][${serviceID}][vat]`,vat);
                    formData.append(`items[service][${serviceID}][vat_value]`,vat_value);
                    formData.append(`items[service][${serviceID}][amount]`, amount);     
                    formData.append(`items[service][${serviceID}][discount]`, discount);                   
                }
            });

            GYM._upload('/admin/dashboard/add_items_to_que_ajax', formData).done(function(res){
                if(!res.error){
                    N.show('success', 'Položky byly úspěšně přidány!');
                    $('#queItems').replaceWith(res.data);
                    $('#modalOverModal').modal('hide');
                } else N.show('error', 'Nepodařilo se přidat položky, zkuste to znovu');
                NProgress.done();
            });
        },
        removeItemFromAddList: function(el){
            $(el).closest('tr').remove();
        },
        initAddItemsModal: function(){
            
            // Services
            self.service_item_select = $('#service_item');

            self.service_item_select.val("").trigger("change");

            // Depot
            self.depot_select = $("#depot_id"),
            self.depot_item_select = $("#depot_item");

            self.depot_select.val("").trigger("change");
            self.depot_item_select.attr("disabled", true).trigger("change");

            self.depot_select.change(function(e){
                if(!$(this).val()) return false; // get items only if value is not empty
                self.depot_item_select.html('').trigger("change");
                var depot_id = $(this).val();
                GYM._post('/admin/depot/get_items_from_depot', {'depot_id': depot_id}).done(function(res){
                    if(!res.error){
                        if(res.length > 0){
                            self.depot_item_select.attr("disabled", false).trigger("change");
                            $.each(res, function(i, item){

                                var opt = new Option(item.name, item.id, false, false);
                                if(parseInt(item.stock) > 0){
                                    self.depot_item_select.append(opt).trigger("change");
                                }else{
                                    // TODO for some reason this shit stopped working ?? :D 
                                    self.depot_item_select.append(opt).trigger("change");
                                    self.depot_item_select.find('option[value="'+item.id+'"]').attr("disabled", true);
                                    self.depot_item_select.select2();
                                }
                            });
                            self.depot_item_select.val('').trigger("change");
                        }else{
                            self.depot_item_select.attr("disabled", true).trigger("change");
                            N.show('error', 'Na tomto skladě nejsou k dispozici žádné položky!');
                        }
                    }else{
                        N.show('error', 'Nedaří se spojit se systémem, opakujte prosím výběr.');
                    }
                });
            });
        }, 
        removeQueItem: function(el,card_id,que_id,depot_id,item_id,amount){
            let data = {'card_id':card_id, 'que_id':que_id, 'depot_id':depot_id, 'item_id':item_id, 'amount':amount, 'membership_id':$('#membership_id').text()};
            if(confirm('Opravdu chcete odstranit tuto položku z transakční fronty?')){
                GYM._post('/admin/dashboard/remove_item_from_que_ajax', data).done(function(res){
                    if(!res.error){
                        $('#queItems').replaceWith(res.data);
                        N.show('success', 'Položka byla úspěšně odstraněna');
                    } else N.show('error', 'Nepodařilo se odstranit položku z transakční fronty, zkuste to znovu');
                });
            }
        },
        priceFormat: function(x){ // 1 -> 1.00
            return parseFloat(Math.round(x * 100) / 100).toFixed(2);
        },          
    }
}());