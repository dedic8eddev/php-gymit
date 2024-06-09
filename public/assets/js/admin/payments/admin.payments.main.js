'use strict';

var PAYMENTS = PAYMENTS || (function () {
    var self;
    return {
        // Choose Checkout
        chooseCheckoutModal: $('#chooseCheckoutModal'),
        checkout_state_form: '#checkoutStateForm',

        // Cardreader
        card_reader: PERSONIFICATORS.getSessionReader(),
        cardPoolingInterval: 2000,
        card_pooling: false,

        // Remote modal
        remote_modal: $('#modal'),   
        btn_modal_submit: $('#modalSubmit'),

        // Buttons
        btnAddServiceItem: $('#btnAddServiceItem'),
        btnAddDepotItem: $('#btnAddDepotItem'),
        btnAddCredit: $('#btnTopUpCredit'),
        btnRefundCredit: $('#btnRefundCredit'),

        // Summary table
        add_form: $('#addPaymentForm'),
        summary_table: $('#summaryTable'),
        purchase_type_table: $('#purchaseTypeTable'),
        client_select: $('#clientId'),
        creditAmount: $('#c_credit'),

        // Multisport
        multisportServiceTypes: [1,3,4],

        // Payments tabulator
        payments_table: '#paymentsTable',
        payments_table_url: $('#paymentsTable').data("ajax"),

        // Depot tabulator
        depot_select: $("#depot_id"),
        depot_item_select: $("#depot_item"),

        // Subscription
        sub_client_select: $('#sub_client_id'),
        sub_payment_table: '#subscriptionPaymentTable',

        // Invoice tab
        invoice_table: '#invoicesTable',
        invoice_table_url: $('#invoicesTable').data("ajax"),

        add_invoice: $('.add-invoice'),
        invoice_modal: $('#invoiceModal'),
        invoice_client: $('#invoiceClient'),
        add_invoice_item: $('.addInvoiceItem'),
        remove_invoice_item: $('.remove-invoice-item'),
        invoice_submit: $('#addInvoiceSubmit'),
        addInvoiceForm: $('#addInvoiceForm'),

        close_transaction: $('.close-transaction'),
        close_day: $('.close-day'),
        print_receipts: $('.print-receipts'),

        edit_form: $('#editTransactionForm'),
        editTransactionModal: $('#editTransactionModal'),
        summary_table_edit: $('#summaryTableEdit'),
        purchase_type_table_edit: $('#purchaseTypeTableEdit'),
        refund_form: $('#refundTransactionForm'),
        refundTransactionModal: $('#refundTransactionModal'),
        summary_table_refund: $('#summaryTableRefund'),
        purchase_type_table_refund: $('#purchaseTypeTableRefund'),

        subRefundModal: $('#refundSubPaymentModal'),
        confirmSubRefund: $('#confirmSubRefund'),

        role: null,
        init: async function(params){
            self = this;

            // set card reader if not in local storage
            if(self.card_reader == null) self.card_reader = await PERSONIFICATORS.chooseSessionReader();            
            // set checkout and terminal if not in local storage
            this.checkout = this.getSessionCheckout() || await this.chooseSessionDevices().checkout;
            this.terminal = this.getSessionTerminal() || await this.chooseSessionDevices().terminal;

            // Init payment terminal
            TERMINALS.URL = this.terminal.terminal_ip;
            TERMINALS.terminalId = this.terminal.terminal_id;
            TERMINALS.terminalName = this.terminal.terminal_name;
            TERMINALS.startSocket();

            this.role = await GYM._role();

            NProgress.configure({ parent: '#app', minimum: 0.1, showSpinner: false });     

            self.initPaymentsTable();
            self.initSubPaymentsTable();
            self.initInvoicesTables();

            this.fireEvents();            
            this.initCheckout(); // must be here after fireEvents becaouse of remote modal functions

        },
        fireEvents: function(){
            self.initTableSelects();

            $("#show-devices-picker").click(function(){
                self.chooseSessionDevices();
            });

            // open payment terminal menu
            $('.terminal-menu').click(function(e){
                $(this).parent().click();
                TERMINALS.requestMenu();
            });

            // Open invoice modal
            self.add_invoice.click(function(){
                self.invoice_modal.modal('show');
            });
            self.invoice_client.change(function(){
                var client_id = $(this).val();
                GYM._post('/admin/clients/get_client_ajax', {'client_id': client_id}).done(function(res){
                    if(!res.error){
                        var d = res.data;

                        self.invoice_modal.find('input[name="client_card_id"]').val(d.card.card_id);
                        self.invoice_modal.find('input[name="client_name"]').val(d.first_name + ' ' + d.last_name);
                        self.invoice_modal.find('input[name="client_street"]').val(d.street);
                        self.invoice_modal.find('input[name="client_city"]').val(d.city);
                        self.invoice_modal.find('input[name="client_zip"]').val(d.zip);
                        self.invoice_modal.find('input[name="client_country"]').val(d.country);
                        self.invoice_modal.find('input[name="client_company_id"]').val(d.company_id);
                        self.invoice_modal.find('input[name="client_vat_id"]').val(d.vat_id);
                        
                        $('.client-data-container').slideDown();
                    }else{
                        N.show('error', 'Nedaří se spojit se systémem, opakujte prosím výběr.');
                    }
                });
            });
            
            self.add_invoice_item.click(function(){
                let type = $(this).data('type').charAt(0).toUpperCase() + $(this).data('type').slice(1); // first letter Upper
                var itm = $(`#invoiceItem${type}Template`).html();
                $('.invoice-items').append(itm).find("select:last-child").select2();
            });
            self.addInvoiceForm.on('change', '.invoice-items .item-row select[data-type]', async function(){
                let item_id = $(this).val(),
                    client_id = self.invoice_client.val(),
                    card_id = self.invoice_modal.find('input[name="client_card_id"]').val(),
                    item_price;
                switch($(this).data('type')) {
                    case 'service':
                        await GYM._post('/admin/pricelist/get_checkout_item_info_ajax', {client_id:client_id,card_id:card_id,item_id:item_id}).done(function (res) {
                            //item_price = res.data.vat_price;
                            item_price = res.data.price;
                        });
                        break;
                    case 'membership':
                        await GYM._post('/admin/pricelist/get_membership_price_info/', {item_id:item_id}).done(function (res) {
                            //item_price = res.data.price;
                            item_price = res.data.price / 1.21;
                        });
                        break;
                    case 'depot':
                        await GYM._post('/admin/depot/get_item_info_simple_ajax', {item_id:item_id}).done(function (res) {
                            //item_price = res.data.sale_price_vat;
                            item_price = res.data.sale_price;
                        });                        
                        break;
                }
                $(this).closest('.item-row').find('input[name="item_value"]').val(GYM._separateThousands(self.priceFormat(item_price)));
                $(this).closest('.item-row').find('input[name="item_amount"]').val(1);
            });

            $('body').on('click', '.remove-invoice-item', function(){
                var row = $(this).parents().eq(1);
                row.remove();
            });
            self.invoice_modal.find('input[type="date"]').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false
            });

            $("#add_sub_datepick").flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                dateFormat: "Y-m-d",
                enableTime: false,
                defaultDate: Date.now()
            });

            // get invoice PDF
            $("body").on("click", ".invoice-get-pdf", function(){
                var id = $(this).data("invoiceid");
                window.open("/admin/payments/get_invoice_pdf/" + id);
            });

            // pay an invoice now
            $("body").on("click", ".invoice-pay-now", function(){
                var invoice_id = $(this).data("invoiceid"),
                    amount = $(this).data("amount");
                
                $("#invoicePayModal").modal("show");
                $("#invoicePayModal").find(".btn").data("invoiceid", invoice_id);
                $("#invoicePayModal").find(".btn").data("amount", amount);
            });

            // Payment choosing for invoice
            $('body').on("click", ".pay-by-card", function(){
                var invoice_id = $(this).data("invoiceid"),
                    amount = $(this).data("amount");

                $("#invoicePayModal").modal("hide");

                // Call temrinal
                TERMINALS.requestPayment(amount)
                    .then(function(data){
                        // TODO: Variable sym
                        GYM._get('/admin/payments/pay-invoice/'+invoice_id+'/2').done(function(res){
                            if(!res.error){
                                N.show('success', 'Faktura byla zaplacena');
                                self.invoice_table.setData(self.invoice_table_url);
                            }else{
                                N.show('error', GYM.general_ajax_error);
                            }
                        });
                    })
                    .catch(function(err){
                        N.show('error', err);
                    });
            });
            $('body').on("click", ".pay-by-cash", function(){
                var invoice_id = $(this).data("invoiceid"),
                    amount = $(this).data("amount");

                // Skip terminal
                GYM._get('/admin/payments/pay-invoice/'+invoice_id+'/1').done(function(res){
                        if(!res.error){
                            N.show('success', 'Faktura byla zaplacena');
                            self.invoice_table.setData(self.invoice_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
            });

            $('body').on('click', '.cancel-invoice', function(){
                var invoice = $(this).data('invoiceid'),
                    agreed = confirm('Opravdu chcete stornovat tuto fakturu?');

                if(agreed){
                    GYM._get('/admin/payments/cancel-invoice/'+invoice).done(function(res){
                        if(!res.error){
                            N.show('success', 'Faktura byla stornována');
                            self.invoice_table.setData(self.invoice_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }
            });

            self.invoice_modal.on('hidden.bs.modal', function(){ // clear invoice form
                var inputs = self.addInvoiceForm.find('input, select');

                $.each(inputs, function(i, input){
                    $(input).val('');
                });

                self.invoice_client.val(null).trigger('change');
                $('.client-data-container').slideUp();
                $('.invoice-items').html('');
            });

            self.invoice_submit.click(function(){
                var inputs = self.addInvoiceForm.find('input, select'),
                    items = $('.invoice-items').find('.item-row');
                
                GYM._validateInputs(inputs);
                if(self.addInvoiceForm.find('.invalid').length <= 0){
                    if(items.length > 0){
                        
                        var req_data = {};
                            req_data['items'] = [];
                        $.each(inputs, function(i, input){
                            // add only invoice data, items are added in next loop
                            if(!$(input).closest('.row').hasClass('item-row')) req_data[$(input).attr("name")] = $(input).val();
                        });
                        $.each(items, function(i, item){
                            var item_name, item_type, item_id,
                                item_autocont_account = false;

                            if($(item).find('select[name="item_name"]').length > 0){
                                item_type = $(item).find('select[name="item_name"]').data('type');
                                item_id = $(item).find('select[name="item_name"]').val();
                                item_name = $(item).find('select[name="item_name"] > option:selected').text();
                            }else{
                                item_type = 'custom_item';
                                item_name = $(item).find('input[name="item_name"]').val();
                                item_autocont_account = $(item).find('select[name="account_number"]').val();
                            }

                            req_data['items'].push({
                                'item_type': item_type,
                                'item_id': item_id,
                                'item_name': item_name,
                                'item_discount': $(item).find('input[name="item_discount"]').val(),
                                'item_value': parseFloat($(item).find('input[name="item_value"]').val().replace(/\s/g, '')),
                                'item_amount': $(item).find('input[name="item_amount"]').val(),
                                'autocont_account': item_autocont_account
                            });
                        });

                        GYM._post('/admin/payments/submit-invoice-ajax', req_data).done(function (res){
                            if(!res.error){
                                N.show('success', 'Faktura byla úspěšně přidána!');
                                self.invoice_modal.modal('hide');
                                self.invoice_table.setData(self.invoice_table_url);
                            }else{
                                N.show('error', GYM.general_ajax_error);
                            }
                        });

                        
                    }else{
                        N.show('error', 'Faktura musí obsahovat alespoň jednu položku!');
                    }
                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            // Edit existing transaction payment method
            self.editTransactionModal.on('click', '.edit-payment-method', function(){
                var selected_method = $(this).data('selected');
                self.editTransactionModal.find('.fader').fadeIn(function(){
                    $('#editTransactionPaymentMethodModal').modal("show");

                    var select = $('#editTransactionPaymentMethodModal').find('#editPaymentMethodSelect');

                    select.val(selected_method);
                    select.trigger("change");
                });
            });
            $('#editTransactionPaymentMethodModal').on('hidden.bs.modal', function(){
                self.editTransactionModal.find('.fader').fadeOut();
            });
            $('#submitNewPaymentMethodEdit').click(function(){
                var select = $('#editTransactionPaymentMethodModal').find('#editPaymentMethodSelect'),
                    method = select.val(),
                    method_name = select.find('option[value="'+method+'"]').text(),
                    total = self.purchase_type_table_edit.find('input').val(),
                    btn = '<a href="javascript:;" class="btn btn-xs btn-primary edit-payment-method" data-selected="'+method+'"><i class="icon-settings2"></i></a>';

                    self.purchase_type_table_edit.find('tbody').html('');
                    self.purchase_type_table_edit.append(`<tr id="pt_${method}"><td>${method_name} ${btn}</td><td class='text-right'><input min="0" max="999999" step="0.01" class='form-control' type='number' data-id='${method}' step="0.50" value='${total}' onchange='PAYMENTS.checkPurchaseTypeValueChange(this);' /></td></tr>`);
                    $('#editTransactionPaymentMethodModal').modal("hide");
                    self.countPrice(true);
            });

            self.editTransactionModal.on('hidden.bs.modal', function(){
                self.summary_table_edit.find('tbody').html('');
                self.purchase_type_table_edit.find('tbody').html('');
            });

            self.refundTransactionModal.on('hidden.bs.modal', function(){
                self.summary_table_refund.find('tbody').html('');
                self.purchase_type_table_refund.find('tbody').html('');
            });

            // Close a trnasaction for editing

            $('#paymentsTable').on('mousedown', '.tabulator-row', function(e){ // do not highlight text when press shift and click
                if (window.getSelection && e.shiftKey) {window.getSelection().removeAllRanges();}
                else if (document.selection && e.shiftKey) {document.selection.empty();}
            });

            self.close_transaction.click(function(){
                if($(this).hasClass("disabled")) return false;

                var data = self.payments_table.getSelectedData(),
                    c = confirm('Opravdu chcete označit vybrané transakce jako uzavřené? Nebude možné je editovat.');

                if(c) {
                    GYM._post('/admin/payments/set_transactions_as_closed', {transactions: data}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Transakce byly úspěšně uzavřeny.');
                            PAYMENTS.payments_table.setData(PAYMENTS.payments_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }
            });

            self.close_day.click(function(){
                if($(this).hasClass("disabled")) return false;

                var day = $('#dayselect').val(),
                    c = confirm('Opravdu chcete označit vybraný den ('+day+') jako uzavřený? Nebude možné v tomto dni nadále editovat transakce.');

                if(c) {
                    GYM._post('/admin/payments/set_day_as_closed', {day: day}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Den by úspěšně uzavřen.');
                            PAYMENTS.payments_table.setData(PAYMENTS.payments_table_url);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });
                }
            });

            // Print receipts
            self.print_receipts.click(function(){
                if(confirm('Opravdu chcete vytisknout vybrané transakce?')) {
                    let ids = [];
                    $.each(self.payments_table.getSelectedData(), function(i,v){
                        ids.push(v.parentTransaction === undefined ? v._id : v.parentTransaction);
                    });
                    GYM._post('/admin/payments/print_receipt', {transactions: ids}).done(function(res){
                        if(!res.error) N.show('success', 'Daňové doklady byly úspěšně vytisknuty');
                        else N.show('error', GYM.general_ajax_error);
                    });
                }
            });

            $('body').on('change', '#dayselect', function(){
                if($(this).val() != "") self.close_day.removeClass("disabled");
                else self.close_day.addClass("disabled");
            });

            $(".js-payments-clear-filter").click(function(){
                self.payments_table.clearHeaderFilter();
                self.payments_table.clearSort();
                self.payments_table.setSort([{column:"paidOn",dir:"desc"}]);
                self.payments_table.redraw(true);

                self.close_day.addClass("disabled");
                self.initTableSelects();
            });

            $(".js-invoices-clear-filter").click(function(){
                self.invoice_table.clearFilter(true);
                self.invoice_table.clearSort();
                self.initTableSelects();
            });

            // Remote Modal
            $('body').on('click', '[data-toggle="modal"][data-remote]', function(){
                // Return credit
                if($(this).is('#btnRefundCredit') && !self.client_select.val()){
                    N.show('error', 'Načtěte prosím kartu klienta!');
                    return false;
                } else if ($(this).is('#btnRefundCredit') && !$('#creditValue').val()) {
                    N.show('error','Zadejte částku pro vrácení kreditu');
                    $('#creditValue').addClass('invalid').focus();
                    return false; 
                } else if ($(this).is('#btnRefundCredit') && ( $('#creditValue').val() > parseFloat(self.creditAmount.text().replace(/\s/g, '')) ) ) {
                    N.show('error','Klient nemá dostatek kreditu pro vrácení');
                    $('#creditValue').addClass('invalid').focus();
                    return false;                     
                } else $('#creditValue').removeClass('invalid');

                // Show/hide footer
                if($(this).is('#btn-create-card-modal') || $(this).is('#btnRefundCredit')){ // create card modal
                    $($(this).data("target")+' .modal-footer').addClass('d-none');
                } else $($(this).data("target")+' .modal-footer').removeClass('d-none');

                // load external content
                $($(this).data("target")+' .modal-body').load($(this).data("remote"), function(){
                });
            }); 

            self.remote_modal.on('show.bs.modal', function(e){
                /*if($(e.relatedTarget).is('#show-open-checkout-modal')){ // cannot close by click outside or keyboard
                    $(this).data('bs.modal')._config.backdrop = 'static';
                    $(this).data('bs.modal')._config.keyboard = false;
                } else {
                    $(this).data('bs.modal')._config.backdrop = true;
                    $(this).data('bs.modal')._config.keyboard = false;
                }*/
                $(e.target).find('.modal-title').text($(e.relatedTarget).data('modal-title'));
                self.btn_modal_submit.text($(e.relatedTarget).data('modal-submit'));
            });

            self.btn_modal_submit.click(function(){
                $(this).closest('.modal-content').find('form').submit();
            }); 
            
            self.remote_modal.on('hidden.bs.modal', function (e) {
                if(self.card_pooling) PERSONIFICATORS.stopPolling(self.card_reader);
                if ($('.modal:visible').length) { // modal over modal
                    $('body').addClass('modal-open');
                }
            }); 

            self.remote_modal.on('click', '#btn-pair-card, #btn-pair-card-immediately', function(){
                let successFunction;

                if($(this).is('#btn-pair-card-immediately')) successFunction = self.pairCard;
                else successFunction = self.pairCardImmediately;

                $('#createCardModalBtns').fadeOut(300);
                setTimeout(function(){
                    $('#createCardModalInstructions').fadeIn(500); 
                    PERSONIFICATORS.startPolling(self.card_reader, successFunction, self.cardPoolingInterval);
                    self.card_pooling=true;
                }, 400);
            });

            self.remote_modal.on('change', '#createCardModalBtns ul.list-group li input', function(){
                $(this).closest('ul').find('li input').not(this).each(function(){
                    $(this).prop('checked',false);
                });
            });

            self.remote_modal.on('click', '#btn-return-credit-bank', function(){
                $('#returnCreditModalBtns').fadeOut(300);
                setTimeout(function(){
                    $('#returnCreditBankModalInstructions').fadeIn(500); 
                }, 400);
            });   
            
            self.remote_modal.on('click', '.submit-return-credit', function(){
                let data = {};
                data['refundValue'] = $('#creditValue').val(),
                data['accountNumber'] = $('#bank-account-number').val(),
                data['clientId'] = self.client_select.val(),
                data['cardId'] = self.client_select.find('option:selected').attr('data-cardId');
                data['checkoutId'] = self.checkout.checkout_eet_id;
                
                GYM._post(`/admin/payments/refund-credit-ajax/`,data).done(function(res){
                    N.show('success','Transakce byla úspěšně vytvořena!');
                    self.remote_modal.modal('hide');
                    self.client_select.val(null).trigger("change"); // reset
                });
            });
            
            self.remote_modal.on('submit', self.checkout_state_form, function(e){
                let currentState = $(this).find('[name="state"]').val();
                e.preventDefault();
                GYM._submitForm({
                    url: $(this).data('ajax'),
                    form:$(this)[0],
                    succes_text: 'Stav pokladny byl úspěšně uložen!',
                    error_text: 'Nepodařilo se uložit stav pokladny, zkontrolujte údaje nebo to zkuste znovu!',
                    success_function: function(){
                        self.checkout.state = currentState == 1 ? 0 : 1;
                        $('#show-close-checkout-modal, #show-open-checkout-modal').toggleClass('d-none');
                        self.remote_modal.modal('hide');
                    }
                });
            }); 

            // Vouchers
            $('#voucherModal').on('show.bs.modal', function(){ // clear input voucher_code on modal opan
                $('#voucher_code').val('');
            });
            $('#applyVoucher').click(function(){
                let voucher_code = $('#voucher_code').val();
                if(voucher_code){
                    $('#voucher_code').removeClass('invalid');
                    GYM._post(`/admin/vouchers/get_voucher_ajax/${voucher_code.trim()}`,{'checkout':1}).done(function(res){
                        if(!res.error){
                            let data = res.data;

                            if(self.purchase_type_table.find(`td input[data-voucher-code='${data.code}']`).length){
                                N.show('error', 'Tento Voucher již v pokladně je');
                                return false;
                            }

                            let icon_remove = `<i class="icon-close text-danger float-right" onclick="PAYMENTS.removePurchaseTypeItem(this,5);"></i>`;

                            // Add credit item
                            self.summary_table.append(`<tr><td>Dobití kreditu</td><td class='text-right voucher' data-voucher-code='${data.code}' data-id='0' data-price='${self.priceFormat(data.vat_price)}'><input class='form-control input-count' type='number' value='1' disabled /></td><td>${self.getInputDiscount(0,true)}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(data.vat_price))}</td><td></td>`);
                            // Voucher purchase type
                            self.purchase_type_table.append(`<tr id="pt_5" class="voucher"><td>Voucher</td><td class='text-right'><input class='form-control' type='number' data-id='5' data-voucher-code='${data.code}' value='${parseInt(data.vat_price)}' disabled /></td><td>${icon_remove}</td></tr>`)
                            if(data.note) self.append2Note(0,data.note);
                            // membership presale merch present
                            if(data.present !== undefined){
                                self.summary_table.append(`<tr><td>${data.present.name}<br /><small>Sklad: ${data.present.depot_name}</small></td><td class='text-right' data-voucher-code='${data.code}' data-vat='${data.present.vat}' data-price='${self.priceFormat(data.present.vat_price)}' data-stock='${parseInt(data.present.depot_stock)}' data-depotid='${data.present.depot_id}' data-id='${data.present.id}'><input class='form-control input-count' type='number' value='1' disabled /></td><td>${self.getInputDiscount(100,true)}<td class='text-right'>0.00</td><td></td></tr>`);
                                self.append2Note(data.present.id,`${data.present.name} - dárek (předprodej členství)`);
                            }
                            
                            $('#voucherModal').modal('hide');
                            N.show('success', 'Voucher aplikován');
                        } else {
                            N.show('error', res.errMsg);
                        }
                    });

                } else {
                    $('#voucher_code').addClass('invalid').focus();
                    N.show('error', 'Vyplňte prosím kód voucheru');
                }
            });
            
            // subscriptions

            self.sub_client_select.change(function(){
                var client_id = $(this).val();
                if(typeof client_id == 'object' || client_id == "null" || client_id == "" || !client_id) return false; // skip if empty

                // reset
                $('#statetab').find('.user-sub-rows').remove();
                $('#buttonpayments').addClass('disabled');
                $('#buttondetail').addClass('disabled');
                $('#buttonstate').click();

                GYM._post('/admin/payments/get_client_subscription_info_ajax', {client_id: client_id}).done(function(res){
                    if(!res.error){

                        var data = res.data;
                        if(data && data.membership.id>0){
                            
                            // Remove sub modal controls
                            $('.sub-add-btn-container').remove();
                            $('.select-client-placeholder').hide();

                            var name = data.membership.name;
                            var contract_number = data.contractNumber;

                            $('#buttonstate').data('contractnum', contract_number);
                            $('#buttonstate').data('subtype', data.subType);
                            $('#buttonstate').data('subperiod', data.subPeriod);

                            var active_end = false;
                            $.each(data.transactions, function(i, t){
                                if(t.paid){
                                    active_end = moment(t.end).format('DD. MM. YYYY');
                                }else{
                                    return false;
                                }
                            });

                            if(data.transactions){
                                $('#statetab').append('<div class="row user-sub-rows header-row"><div class="col-md-3"><strong>Druh členství</strong></div><div class="col-md-3"><strong>Aktivní od</strong></div><div class="col-md-3"><strong>Aktivní do</strong></div><div class="col-md-3"><strong>Zaplaceno do</strong></div></div>'); // header
                                $('#statetab').append('<div class="row user-sub-rows"><div class="col-md-3"><strong>'+name+'</strong></div><div class="col-md-3">'+moment(data.createdOn).format('DD. MM. YYYY')+'</div><div class="col-md-3">'+moment(data.transactions[data.transactions.length - 1].end).format('DD. MM. YYYY')+'</div><div class="col-md-3">'+active_end+'</div></div>'); // data
                            }

                            $('#buttonpayments').removeClass('disabled');
                            $('#buttondetail').removeClass('disabled');

                            self.sub_payment_table.setData(data.transactions);
                            self.sub_payment_table.redraw(true);
                            $('#buttonpayments').data('subid', data.membership.id);

                        }else{
                            $('#statetab').find('.user-sub-rows').remove();
                            $('#buttonpayments').addClass('disabled');
                            $('#buttondetail').addClass('disabled');
                            $('#buttonstate').click();
                            $('#buttonstate').removeAttr('data-contractnum');

                            if($('#statetab').find('.add-subscription').length <= 0){
                                // add sub modal controls if not existent
                                $('.select-client-placeholder').text('Klient nemá žádné členství, můžete mu jej zřídit.');
                                $('.select-client-placeholder').show();
                                $('#statetab').append('<p class="text-center sub-add-btn-container"><a href="javascript:;" class="btn btn-primary add-subscription">Založit členství</a></p>');
                                $('#statetab').find('.add-subscription').data('clientid', client_id);
                            }
                        }

                    }else{
                        N.show('error', GYM.general_ajax_error);
                    }
                });
            });

            $('#buttonpayments').click(function(){ setTimeout(function(){self.sub_payment_table.redraw(true)}, 250); });

            $('#subModal').on('click', '.add-subscription', function(){
                var client_id = $(this).data("clientid");
                $('#subModal').find('.fader').fadeIn(function(){
                    $('#addSubModal').modal("show");
                    $('#addSubModal').find('#saveSubModal').data('clientid', client_id);
                });
            });

            $('#subModal').on('hidden.bs.modal', function(){
                self.sub_client_select.val(null).trigger('change');
                $('.sub-add-btn-container').remove();
                $('.select-client-placeholder').text('Vyberte klienta');
                $('.select-client-placeholder').show();

                $('#statetab').find('.user-sub-rows').remove();
                $('#buttonpayments').addClass('disabled');
                $('#buttondetail').addClass('disabled');
                $('#buttonstate').click();
            });
            $('#addSubModal').on('hidden.bs.modal', function(){
                $('#subModal').find('.fader').fadeOut();
                $('.sub-period-container').slideUp();
                $('#addSubForm')[0].reset();
            });

            $('#subModal').on('click', '.pay-subscription', function(){
                var client_id = self.sub_client_select.val(),
                    contract_number = $('#buttonstate').data('contractnum'),
                    trans_id = $(this).data('transid'),
                    start = $(this).data('start'),
                    end = $(this).data('end'),
                    value = $(this).data('value'),
                    vat = $(this).data('vat'),
                    vat_value = $(this).data('vat_value'),
                    sub_type = $('#buttonstate').data('subtype'),
                    sub_period = $('#buttonstate').data('subperiod'),
                    sub_id = $('#buttonpayments').data('subid');

                var items = [];

                items.push({
                    'sub_id': sub_id,
                    'item_name': 'Platba za členství (' + moment(start).format('DD.MM.') + '-' + moment(end).format('DD.MM.') + ')',
                    'value': value,
                    'vat': vat,
                    'vat_value': vat_value,
                    'transaction_id': trans_id,
                    'existing_payment': 1,
                    'sub_type': sub_type,
                    'sub_period': sub_period
                });

                self.resetRegister();
                self.addSubscriptionToSummary({items: items, client_id: client_id, contract_number: contract_number});
                $('#subModal').modal('hide');

                //self.client_select.val(client_id).trigger('change');
                $('#note').val('Platba za část členství'); // note
            });

            $('#subModal').on('click', '.refund-subscription', function(){
                var client_id = self.sub_client_select.val(),
                    contract_number = $('#buttonstate').data('contractnum'),
                    trans_id = $(this).data('transid'),
                    start = $(this).data('start'),
                    end = $(this).data('end'),
                    paid = $(this).data('paid'),
                    today = moment().format('YYYY-MM-DD');

                $('#subModal').find('.fader').fadeIn(function(){
                    self.subRefundModal.modal("show");
                    self.subRefundModal.find('.refund-sub-date').text(moment(start).format('DD. MM. YYYY') + ' - ' + moment(end).format('DD. MM. YYYY'));

                    self.confirmSubRefund.data('client_id', client_id);
                    self.confirmSubRefund.data('contract_number', contract_number);
                    self.confirmSubRefund.data('transaction_id', trans_id);
                    self.confirmSubRefund.data('paid', paid);

                    // Is a paid timeblock?
                    if(paid){
                        if(today > moment(start).format('YYYY-MM-DD')){
                            // Zpětně zaplaceno
                            // Storno očekávané platby
                            // Vykrácení nových plateb (marketing) - posunutí předplatného
                            self.subRefundModal.find('#paid_history').show();
                            self.confirmSubRefund.data('refund_cat', 'paid_history');
                        }else{
                            // Do budoucna zaplaceno
                            // Protitransakce částečná
                            // Přidat časový segment (kompenzace)
                            self.subRefundModal.find('#paid_future').show();
                            self.confirmSubRefund.data('refund_cat', 'paid_future');
                        }
                    }else{
                        // Storno nezaplaceného členství
                        if(today > moment(start).format('YYYY-MM-DD')){
                            // Zpětně nezaplaceno
                            // Storno očekávané platby
                            // Přidat časový segment (kompenzace)
                            self.subRefundModal.find('#unpaid_history').show();
                            self.confirmSubRefund.data('refund_cat', 'unpaid_history');
                        }else{
                            // Do budoucna nezaplaceno
                            // Jen zrušit členství (?)
                            self.subRefundModal.find('#unpaid_future').show();
                            self.confirmSubRefund.data('refund_cat', 'unpaid_future');
                        }
                    }
                });
            });
            self.subRefundModal.on('hidden.bs.modal', function(){
                $('#subModal').find('.fader').fadeOut();
                self.subRefundModal.find('.refundtab').hide();
            });

            // Confirm subscription month refund
            self.confirmSubRefund.click(function(e){
                e.preventDefault();

                var select = self.subRefundModal.find('select:visible'),
                    name = select.attr('name'),
                    value = select.val(),
                    note = self.subRefundModal.find("textarea").val(),
                    data = $(this).data();

                data[name] = value;
                data["note"] = note;

                GYM._post('/admin/payments/refund_sub', data).done(function(res){
                    if(!res.error){
                        N.show('success', 'Storno proběhlo úspěšně!');
                        self.subRefundModal.modal('hide');
                        self.subRefundModal.find("#note").val(""); // reset note

                        GYM._post('/admin/payments/get_client_subscription_info_ajax', {client_id: data.client_id}).done(function(res){
                            self.sub_payment_table.setData(res.data.transactions);
                            self.sub_payment_table.redraw(true);
                        });
                    }else{
                        N.show('error', GYM.general_ajax_error);
                    }
                });
            });

            $('#addSubModal').on('change', '#subTypeSelect', function(){
                var sub_type = $(this).val(),
                    sub_type_category = false;

                if(sub_type.includes('basic')){
                    sub_type_category = sub_type.split('_')[1];
                    if(sub_type_category != 'quarter'){
                        $('.sub-period-container').slideDown();
                    }else{
                        $('.sub-period-container').slideUp();
                    }
                }else if(sub_type.includes('platinum')){
                    if(sub_type.includes('_')) sub_type_category = sub_type.split('_')[1];
                    if(sub_type_category != 'quarter' || sub_type_category == false){
                        $('.sub-period-container').slideDown();
                    }else{
                        $('.sub-period-container').slideUp();
                    }
                    
                }else if(sub_type.includes('trial')){
                    $('.sub-period-container').slideUp();
                }
            });

            $('#saveSubModal').click(function(){
                var inputs = $('#addSubForm').find('input:visible, select:visible, textarea:visible');
                GYM._validateInputs(inputs);
                
                if($('#addSubForm').find('.invalid').length <= 0){
                    
                    var req_data = {};
                        req_data.client_id = $(this).data("clientid");
                    $.each(inputs, function(i, input){
                        req_data[$(input).attr("name")] = $(input).val();
                    });

                    req_data['start'] = $('#add_sub_datepick').val();

                    if(/^.*_quarter$/.test(req_data['sub_type'])){
                        req_data['sub_period']='quarter';
                    } 

                    GYM._post('/admin/payments/submit_subscription_ajax', req_data).done(function(res){
                        if(!res.error){
                            $('#addSubModal').modal("hide");
                            $('#subModal').modal('hide');

                            self.addSubscriptionToSummary(res.data);
                            self.append2Note(res.data.items[0].sub_id,'Platba nového členství'); // note
                            $('#printContractPreview').removeClass('hidden').attr('href', `/admin/contract/get-contract-pdf?userId=${self.sub_client_select.val()}&membershipId=${res.data.membership_id}`);
                        }else{
                            N.show('error', GYM.general_ajax_error);
                        }
                    });

                }else{
                    N.show('error', GYM.general_form_error);
                }
            });

            // depot stuff
            self.depot_select.val("").trigger("change");
            self.depot_item_select.attr("disabled", true).trigger("change");

            self.depot_select.change(function(e){
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

            self.client_select.change(function(e){
                var client_id = $(this).val();
                if(client_id){
                    GYM._post('/admin/clients/get_client_ajax', {'client_id': client_id}).done(function(res){
                        if(!res.error){
                            self.creditAmount.text(GYM._separateThousands(res.data.credit));
                            self.client_select.find('option:selected').attr('data-cardId',res.data.card.card_id);

                            // show/hide btn refund credit
                            if(res.data.credit>0) self.btnRefundCredit.fadeIn(300);
                            else self.btnRefundCredit.fadeOut(300);

                            if(!res.data.card.card_id && self.client_select.find('option:selected').attr('data-non-client')!=1){ // client has not card
                                $('#btn-create-card-modal').click();
                            }
                            self.fillvatInfoForm(res.data);
                        }else{
                            N.show('error', 'Nedaří se spojit se systémem, opakujte prosím výběr.');
                        }
                    });
                }
            });

            $("#v-pills-list-tab").click(function(){
                self.payments_table.setData(self.payments_table_url);  
            });            
            $("#v-pills-invoices-tab").click(function(){
                self.invoice_table.setData(self.invoice_table_url);  
            });            
            // purchase types

            $(".btn-pt-popover").popover({
                trigger: 'manual',
                placement: 'right',
                html: true,
                title: function() { return $(this).data('poptitle'); },
                content: function() { return $($(this).data('target')).html(); }
            }).on("mouseenter", function () {
                var _this = this;
                $(this).popover("show");
                $($(this).data('bs.popover').tip).css('max-width','200px').css('width','200px');
                $(".popover").on("mouseleave", function () {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) $(_this).popover("hide");
                }, 200);
            });


            $(".purchase-type .card-input-element").change(function(e){
                if($(this).hasClass('parentPT')){
                    return false;
                }
                var icon_remove = `<i class="icon-close text-danger float-right" onclick="PAYMENTS.removePurchaseTypeItem(this,${$(this).val()});"></i>`,
                    ptText = $(this).next().text(),
                    ptId = $(this).val();
                if($(this).is(':checked')){
                    if(ptId==4 && (!self.summary_table.find(`td[data-service-type='${self.multisportServiceTypes.join("'],td[data-service-type='")}']`).length)){ // only service can be paid by multisport
                        $(this).prop('checked',false);
                        N.show('error','Multisport kartou lze platit pouze služba');
                        return false;
                    }

                    var credit_in_cashout = self.summary_table.find(`td[data-id="0"]`).length,
                        voucher_in_cashout = self.summary_table.find(`td[data-service-type="6"]`).length,
                        checkout_total_rows = self.summary_table.find(`tbody tr`).length,
                        bank_total_rows = credit_in_cashout + voucher_in_cashout;

                    if(ptId==14 && (((!voucher_in_cashout) && (!credit_in_cashout)) || checkout_total_rows != bank_total_rows)){
                        $(this).prop('checked',false);
                        N.show('error','Bankovním převodem lze platit pouze kredit nebo vouchery!');
                        return false;
                    }

                    if(ptId==2){
                        $('#cardCashback').fadeIn(300);
                    }

                    self.purchase_type_table.append(`<tr id="pt_${ptId}"><td>${ptText}</td><td class='text-right'><input min="1" max="999999" step="0.0001" class='form-control' type='number' data-id='${ptId}' value='' onchange='PAYMENTS.checkPurchaseTypeValueChange(this);' /></td><td>${icon_remove}</td></tr>`);
                    self.countPrice();
                } else {
                    $('#pt_'+ptId).remove();
                    self.countPrice();

                    if(ptId==2){
                        $('#cardCashback').fadeOut(300);
                    }
                }
            });
            $(document).on('click', '.js-pt-subtype', function(){
                let icon_remove = `<i class="icon-close text-danger float-right" onclick="PAYMENTS.removePurchaseTypeItem(this,${$(this).val()});"></i>`,
                    ptId = $(this).data('id'),
                    ptText = $(this).data('title'),
                    parent = $(this).data('parent'),
                    popId = $(this).closest('.popover').attr('id');
                if(self.purchase_type_table.find(`#pt_${ptId}`).length){
                    $('#pt_'+ptId).remove();
                    if(!self.purchase_type_table.find(`[data-parent='${parent}']`).length){
                        $('.purchase-type').find(`[aria-describedby='${popId}']`).prev('input').prop('checked',false);;
                    }
                } else {
                    $('.purchase-type').find(`[aria-describedby='${popId}']`).prev('input').prop('checked',true);
                    self.purchase_type_table.append(`<tr id="pt_${ptId}" data-parent="${parent}"><td>${ptText}</td><td class='text-right'><input min="1" max="999999" step="0.0001" class='form-control' type='number' data-id='${ptId}' value='' onchange='PAYMENTS.checkPurchaseTypeValueChange(this);' /></td><td>${icon_remove}</td></tr>`);
                }
                
                $(".btn-pt-popover").popover('hide');
                self.countPrice();
            });

            // Purchase items

            self.btnAddCredit.click(function(){ self.addCreditToSummary($("#creditValue").val()); });
            self.btnAddDepotItem.click(function(){ self.addItemToSummary($("#depot_item option:selected"),'depot'); });
            self.btnAddServiceItem.click(async function(){ 
                let item = $("#service_item option:selected"),
                    service_type = item.data('service-type');
                if(service_type==5){ // solarium
                    self.btnAddServiceItem.popover('enable');
                    self.btnAddServiceItem.popover('show');
                } else {
                    self.btnAddServiceItem.popover('disable');
                    self.addItemToSummary(item,'service'); 
                }
            });
            // popover to fill additional data
            self.btnAddServiceItem.popover({
                trigger: 'click',
                placement: 'right',
                html: true,
                title: function() { return `<span>Vyplňte</span><a href="javascript:;" class="close" onclick="$('#${$(this).attr('id')}').popover('hide');">×</a>`; },
                content: function() { return $('#solariumPopover').html(); }
            });

            self.summary_table.on('change', '.input-count', function(){
                var count = parseInt($(this).val()),
                    item_price = parseInt($(this).closest('td').data('price')),
                    item_stock = parseInt($(this).closest('td').data('stock')),
                    price_col = $(this).closest('td').next().next();
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
                price_col.text(GYM._separateThousands(self.priceFormat(item_price * count)));
                self.countPrice();
            });

            self.summary_table_edit.on('change', '.input-count', function(){
                var count = parseInt($(this).val()),
                    bought = parseInt($(this).closest('td').data('bought')),
                    item_price = parseInt($(this).closest('td').data('price')),
                    item_stock = parseInt($(this).closest('td').data('stock')),
                    price_col = $(this).closest('td').next().next();
                if(item_stock > 0 && (count - bought) > item_stock){
                    N.show('error', `Tato položka je pouze ${item_stock}x na skladě.`);
                    $(this).val(item_stock);
                    count = item_stock;
                } else if(count > 999){ // validation (max 999 items)
                    $(this).val(999);
                    count = 999;
                } else if (count < $(this).attr('min')){ // validation (min 1 item)
                    $(this).val($(this).attr('min'));
                    count = $(this).attr('min');                    
                }
                price_col.find('input').val(item_price * count);
                self.countPrice(true);
            });

            self.summary_table.on('change', '.input-discount', function(){
                var discount = parseInt($(this).val()), // percentage
                    item_price = parseInt($(this).closest('td').prev().data('price')),
                    item_count = parseInt($(this).closest('td').prev().find('.input-count').val()),
                    price_col = $(this).closest('td').next(),
                    discount_value = item_price * (discount / 100),
                    price = item_count * (item_price - discount_value);

                price_col.text(GYM._separateThousands(self.priceFormat(price.toFixed(2))));
                self.countPrice();
            });

            self.summary_table_edit.on('change', '.input-discount', function(){
                var discount = parseInt($(this).val()), // percentage
                    item_price = parseInt($(this).closest('td').prev().data('price')),
                    item_count = parseInt($(this).closest('td').prev().find('.input-count').val()),
                    price_col = $(this).closest('td').next(),
                    discount_value = item_price * (discount / 100),
                    price = item_count * (item_price - discount_value);

                //price_col.find('input').val( price.toFixed(2) );
                self.countPrice(true);
            });

            self.refund_form.submit(function(e){
                e.preventDefault();

                var url = $(this).data("ajax"),
                    transId = $(this).data('trans'),
                    value = $(this).data('total');

                var purchase_types = $("#purchaseTypeTableRefund > tbody > tr"),
                    cc = false;

                $.each(purchase_types, function (i, type){
                    var id = String($(type).attr("id"));

                    if(id.split("pt_")[1] === "2"){
                        cc = true; // card transaction
                    }
                });

                if(cc){
                    TERMINALS.requestPayment(parseFloat(value), false, true)
                                .then((data) => {
                                    var VS = data.vSymbol,
                                        tID = data.terminalId;

                                    GYM._post(url, {'transaction_id': transId, "variableSymbol": VS, "terminalId": tID, "cc_refund": true}).done(function(res){
                                        if(!res.error){
                                            N.show('success', 'Transakce byla úspěšně stornována!');
                                            self.refundTransactionModal.modal("hide");
                                            self.payments_table.setData(self.payments_table_url);
                                        } else N.show('error', 'Nepodařilo se stornovat transakci, zkontrolujte údaje nebo to zkuste znovu!');
                                        NProgress.done();
                                    });
                                })
                                .catch(err => {
                                    N.show("error", err);
                                });
                }else{
                    GYM._post(url, {'transaction_id': transId, "cc_refund": false}).done(function(res){
                        if(!res.error){
                            N.show('success', 'Transakce byla úspěšně stornována!');
                            self.refundTransactionModal.modal("hide");
                            self.payments_table.setData(self.payments_table_url);
                        } else N.show('error', 'Nepodařilo se stornovat transakci, zkontrolujte údaje nebo to zkuste znovu!');
                        NProgress.done();
                    });
                }
            });

            self.edit_form.submit(function(e){
                e.preventDefault();

                var url = $(this).data("ajax"),
                    clientId = $(this).data("client"),
                    clientCardId = $(this).data("card"),
                    transId = $(this).data('trans'),
                    formData = new FormData();

                if (self.summary_table_edit.find('td').length===0){
                    N.show('error', 'Transakce neobsahuje žádné položky, vyberte variantu storno!');
                    return false;                    
                }

                // discout note
                self.summary_table_edit.find('td .input-discount').each(function () {
                    if($(this).val()>0 && (!$('#note_edit').val())){
                        N.show('error', 'Vyplňte prosím poznámku, když je aplikována sleva!');
                        return false;
                    }
                });

                if(!self.validatePurchaseTypePrices(true)){
                    N.show('error', 'Výsledná cena se nerovná součtu cen u jednotlivých typů platby!');
                    return false;
                }

                self.purchase_type_table_edit.find('tbody td input[type="number"]').each(function(){
                    formData.append(`purchase_type[${$(this).data('id')}]`,$(this).val());
                });

                formData.set('clientId',clientId);
                formData.set('clientCardId',clientCardId);
                formData.set('note',$('#note').val());
                formData.set('transaction_id',transId);

                self.summary_table_edit.find('td:first-child').next('td').each(function () {
                    let itemName = $(this).prev().contents().get(0).nodeValue, // name without child elements (eg. Depot name)
                        vat = parseFloat($(this).data('vat')),
                        value = parseFloat($(this).data('price')) / (1 + vat),
                        vat_value = value * vat;

                    if($(this).data('stock')){ // DEPOT ITEMS
                        let depotID = $(this).data('depotid'),
                            depotItemID = $(this).data('id');
                        formData.append(`items[depot][${depotID}][${depotItemID}][name]`, itemName);
                        formData.append(`items[depot][${depotID}][${depotItemID}][vat]`, vat);
                        formData.append(`items[depot][${depotID}][${depotItemID}][vat_value]`, vat_value);
                        formData.append(`items[depot][${depotID}][${depotItemID}][amount]`,parseInt($(this).find('.input-count').val()));
                        formData.append(`items[depot][${depotID}][${depotItemID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                    }
                    else if($(this).data('newsubscription') || $(this).data('subscription')){ // NEW SUBSCRIPTION & SUBSCRIPTION
                        let type = $(this).data('newsubscription') ? 'new_subscription' : 'subscription', 
                            subID = $(this).data('id');

                        if($(this).data('transid')){
                            // An actual existing subscription payment for an existing running sub
                            formData.append(`items[subscription][${subID}][transaction_id]`, $(this).data('transid'));
                        }
                        formData.append(`items[${type}][${subID}][name]`, itemName);
                        formData.append(`items[${type}][${subID}][vat]`, vat);
                        formData.append(`items[${type}][${subID}][vat_value]`, vat_value);                        
                        formData.append(`items[${type}][${subID}][amount]`,parseInt($(this).find('.input-count').val()));
                        formData.append(`items[${type}][${subID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                        formData.append(`items[${type}][${subID}][contract_id]`, $(this).data('contract'));                        
                    } else { // SERVICE ITEMS
                        let serviceID = $(this).data('id');
                        formData.append(`items[service][${serviceID}][name]`, itemName);
                        formData.append(`items[service][${serviceID}][service_type]`,$(this).data('service-type'));     
                        formData.append(`items[service][${serviceID}][service_subtype]`,$(this).data('service-subtype'));                      
                        formData.append(`items[service][${serviceID}][vat]`,$(this).data('vat'));
                        formData.append(`items[service][${serviceID}][vat_value]`,$(this).data('price'));
                        formData.append(`items[service][${serviceID}][amount]`,parseInt($(this).find('.input-count').val()));                        
                        formData.append(`items[service][${serviceID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                    }
                });

                GYM._upload(url, formData).done(function(res){
                    if(!res.error){
                        N.show('success', 'Transakce byla úspěšně upravena!');
                    } else N.show('error', 'Nepodařilo se upravit transakci, zkontrolujte údaje nebo to zkuste znovu!');
                    NProgress.done();
                });
            });

            self.add_form.submit(function(e){
                e.preventDefault();

                if(typeof self.checkout != "undefined" && self.checkout.state==0){ // open checkout if closed
                    $('#show-open-checkout-modal').click();
                    return false;
                }

                var url = $(this).data("ajax"),
                    clientId = self.client_select.val(),
                    clientCardId = self.client_select.find('option:selected').attr('data-cardId'),
                    newCard = self.client_select.find('option:selected').attr('data-new-card') ? 1 : 0,
                    checkoutId = self.checkout.checkout_eet_id,
                    card_payment = false,
                    cancel_payment = false,
                    formData = new FormData();

                if (!clientId){
                    N.show('error', 'Načtěte prosím kartu klienta!');
                    return false; 
                } else if (!clientCardId && self.client_select.find('option:selected').attr('data-non-client')!=1){
                    $('#btn-create-card-modal').click();
                    return false;
                } else if (self.summary_table.find('td').length===0){
                    N.show('error', 'Vyberte prosím alespoň jednu položku!');
                    return false;                    
                } else if(!self.purchase_type_table.find('tbody td input[type="number"]').length && parseFloat($('#price_total').text())>0){
                    N.show('error', 'Vyberte prosím způsob platby!');
                    return false;
                }

                // discout note
                self.summary_table.find('td .input-discount').each(function () {
                    if($(this).val()>0 && (!$('#note').val())){
                        N.show('error', 'Vyplňte prosím poznámku, když je aplikována sleva!');
                        return false;
                    }
                });

                if($('#vatInfoForm').is(':visible')){ // non client data
                    formData.append(`vatInfo[name]`,$('#vatInfo [name="subject_name"]').val());
                    formData.append(`vatInfo[company_id]`,$('#vatInfo [name="subject_id"]').val());
                    formData.append(`vatInfo[vat_id]`,$('#vatInfo [name="subject_vat_id"]').val());
                    formData.append(`vatInfo[street]`,$('#vatInfo [name="subject_street"]').val());
                    formData.append(`vatInfo[city]`,$('#vatInfo [name="subject_city"]').val());
                    formData.append(`vatInfo[zip]`,$('#vatInfo [name="subject_zip"]').val());
                    formData.append(`vatInfo[country]`,$('#vatInfo [name="subject_country"]').val());
                }

                if(!self.validatePurchaseTypePrices()){
                    N.show('error', 'Vysledná cena se nerovná součtu cen u jednotlivých typů platby!');
                    return false;
                }                

                self.purchase_type_table.find('tbody td input[type="number"]').each(function(){
                    let purchase_id = $(this).data('id'),
                        _this = $(this);
                    
                    if(purchase_id==5){ // Voucher
                        formData.append(`purchase_type[${purchase_id}][voucher_codes][]`,$(this).data('voucher-code')); 
                    } else if (purchase_id==4){ // Multisport
                        let multisportPay = true;
                        // find item, which is paid by multisport card
                        self.summary_table.find('td:first-child').next('td').each(function () { // get all services, that can be paid by multisport and choose one
                            // only one item can be paid by multisport, multisport amount must equal to item price
                            if(multisportPay && parseFloat($(this).data('price')) == parseFloat(_this.val()) && self.multisportServiceTypes.includes($(this).data('service-type'))){
                                formData.append(`purchase_type[${purchase_id}]`,_this.val());
                                formData.append(`multisport_item`,$(this).data('id'));
                                multisportPay = false;
                            }
                        });
                        if(multisportPay){
                            N.show('error', 'Částka hrazená multisport kartou se nerovná žádné z cen služeb, které mohou být hrazeny multisport kartou');
                            return false;
                        }
                    } else formData.append(`purchase_type[${purchase_id}]`,$(this).val());

                    if(purchase_id==2) card_payment = $(this).val();

                    if(purchase_id==14) {
                        var credit_in_cashout = self.summary_table.find(`td[data-id="0"]`).length,
                            voucher_in_cashout = self.summary_table.find(`td[data-service-type="6"]`).length,
                            checkout_total_rows = self.summary_table.find(`tbody tr`).length,
                            bank_total_rows = credit_in_cashout + voucher_in_cashout;
    
                        if(((!voucher_in_cashout) && (!credit_in_cashout)) || checkout_total_rows != bank_total_rows){
                            $(this).prop('checked',false);
                            N.show('error','Bankovním převodem lze platit pouze kredit nebo vouchery!');
                            cancel_payment = true;
                            return false;
                        }
                    }
                });  

                if(cancel_payment) return false;
                
                formData.set('clientId',clientId);
                formData.set('clientCardId',clientCardId);
                formData.set('newCard',newCard);
                formData.set('checkoutId',checkoutId);

                let note = '';
                if($('#systemNotes p').length){
                    $('#systemNotes p').each(function () {
                        note += note == '' ? $(this).text() : `\n${$(this).text()}`;
                    });
                } 
                if($('#note').val()) note += $('#note').val();
                formData.set('note',note);               

                self.summary_table.find('td:first-child').next('td').each(function () {
                    if($(this).hasClass('voucher')) return; // voucher will be proccessed on backend, so CONTINUE!

                    let itemName = $(this).prev().contents().get(0).nodeValue, // name without child elements (eg. Depot name)
                        benefitId = $(this).data('benefit') ? $(this).data('benefit') : false,
                        vat = parseFloat($(this).data('vat')),
                        value = parseFloat($(this).data('price')) / (1 + vat),
                        vat_value = value * vat;                        

                    if($(this).data('stock')){ // DEPOT ITEMS
                        let depotID = $(this).data('depotid'),
                            depotItemID = $(this).data('id');
                        formData.append(`items[depot][${depotID}][${depotItemID}][name]`, itemName);
                        formData.append(`items[depot][${depotID}][${depotItemID}][benefit]`, benefitId);
                        formData.append(`items[depot][${depotID}][${depotItemID}][vat]`, vat);
                        formData.append(`items[depot][${depotID}][${depotItemID}][vat_value]`, vat_value);
                        formData.append(`items[depot][${depotID}][${depotItemID}][value]`, value);
                        formData.append(`items[depot][${depotID}][${depotItemID}][amount]`,parseInt($(this).find('.input-count').val()));
                        formData.append(`items[depot][${depotID}][${depotItemID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                    } else if($(this).data('newsubscription') || $(this).data('subscription')){ // NEW SUBSCRIPTION & SUBSCRIPTION
                        let type = $(this).data('newsubscription') ? 'new_subscription' : 'subscription', 
                            subID = $(this).data('id');

                        if(type == 'new_subscription'){
                            formData.append(`items[${type}][${subID}][sub_start]`, $(this).data('start')); // sub start date
                        }

                        if($(this).data('transid')){
                            // An actual existing subscription payment for an existing running sub
                            formData.append(`items[subscription][${subID}][transaction_id]`, $(this).data('transid'));
                        }
                        formData.append(`items[${type}][${subID}][name]`, itemName);
                        formData.append(`items[${type}][${subID}][benefit]`, benefitId);
                        formData.append(`items[${type}][${subID}][vat]`, vat);
                        formData.append(`items[${type}][${subID}][vat_value]`, vat_value);      
                        formData.append(`items[${type}][${subID}][value]`, value);                   
                        formData.append(`items[${type}][${subID}][amount]`,parseInt($(this).find('.input-count').val()));
                        formData.append(`items[${type}][${subID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                        formData.append(`items[${type}][${subID}][contract_id]`, $(this).data('contract'));                        
                    } else { // SERVICE ITEMS
                        let serviceID = $(this).data('id'),
                            solariumId = $(this).data('solarium') ? $(this).data('solarium') : false,
                            isOvertime = $(this).data('overtime') ? true : false;
                        if(isOvertime) serviceID = 'o'+serviceID;
                        formData.append(`items[service][${serviceID}][name]`, itemName);  
                        formData.append(`items[service][${serviceID}][benefit]`, benefitId);
                        formData.append(`items[service][${serviceID}][solarium]`, solariumId);
                        formData.append(`items[service][${serviceID}][service_type]`,$(this).data('service-type'));     
                        formData.append(`items[service][${serviceID}][service_subtype]`,$(this).data('service-subtype'));                                                  
                        formData.append(`items[service][${serviceID}][vat]`, vat);
                        formData.append(`items[service][${serviceID}][vat_value]`, vat_value);
                        formData.append(`items[service][${serviceID}][value]`, value);
                        formData.append(`items[service][${serviceID}][isOvertime]`, isOvertime);
                        formData.append(`items[service][${serviceID}][amount]`,parseInt($(this).find('.input-count').val()));                        
                        formData.append(`items[service][${serviceID}][discount]`,parseInt($(this).next().find('.input-discount').val()));
                    }
                });

                if(card_payment !== false){

                    var cashback = $(`input[name="cashback"]`).val();
                    if(!cashback || parseInt(cashback) <= 0) cashback = false;

                    TERMINALS.requestPayment(card_payment, cashback)
                    .then(function(data){
                        formData.set("paymentIdentificationNumber", data.vSymbol); // "Authorization code" from the card payment
                        formData.set("terminalId", data.terminalId); // Payment terminal ID

                        $("#fullPageLoader").fadeIn();
                        GYM._upload(url, formData).done(function(res){
                            $("#fullPageLoader").fadeOut();
                            if(!res.error){
                                N.show('success', 'Transakce byla úspěšně vytvořena!');
                                if(res.invoiceId > 0 || res.contractNumber > 0 || ('vouchers' in res)){
                                    let dialogHTML = '';
                                    if(res.invoiceId > 0) dialogHTML += `<p class="font-italic mt-3 mb-1"><i class="icon-info-circle text-primary mr-2"></i>Faktura byla úspěšně vytvořena a je možné ji dohledat v sekci faktur</p><a class="btn btn-primary btn-block" href="/admin/payments/get_invoice_pdf/${res.invoiceId}" target="_blank">Vytisknout fakturu</a>`;
                                    if(res.contractNumber > 0) dialogHTML += `<p class="font-italic mt-3 mb-1"><i class="icon-info-circle text-primary mr-2"></i>Smlouva o členství byla úspěšně vytvořena a je možné ji dohledat v sekci členství</p><a class="btn btn-primary btn-block" href="/admin/contract/get_contract_pdf?contractNumber=${res.contractNumber}&userId=${clientId}" target="_blank">Vytisknout smlouvu</a>`;
                                    if('vouchers' in res){
                                        dialogHTML += '<table class="mt-3"><tr><th>Kód</th><th>Název</th><th>Cena</th>,/tr>';
                                        $.each(res.vouchers, function(v){
                                            dialogHTML += `<tr><td>${v.code}</td><td>${v.name}</td><td>${v.vat_price}</td></tr>`;
                                        });
                                        dialogHTML += '</table>';
                                    }
                                    $("#dialog").html(dialogHTML);
                                    self.showDialog('Doplňující informace');
                                }
                                // clear form
                                self.resetRegister();
                            } else N.show('error', 'Nepodařilo se vytvořit transakci, zkontrolujte údaje nebo to zkuste znovu!');
                            NProgress.done();
                        });
                    })
                    .catch(function(err){
                        N.show('error', err);
                    });
                }else{
                    $("#fullPageLoader").fadeIn();
                    GYM._upload(url, formData).done(function(res){
                        $("#fullPageLoader").fadeOut();
                        if(!res.error){
                            N.show('success', 'Transakce byla úspěšně vytvořena!');
                            if(res.invoiceId > 0 || res.contractNumber > 0 || ('vouchers' in res)){
                                let dialogHTML = '';
                                if(res.invoiceId > 0) dialogHTML += `<p class="font-italic mt-3 mb-1"><i class="icon-info-circle text-primary mr-2"></i>Faktura byla úspěšně vytvořena a je možné ji dohledat v sekci faktur</p><a class="btn btn-primary btn-block" href="/admin/payments/get_invoice_pdf/${res.invoiceId}" target="_blank">Vytisknout fakturu</a>`;
                                if(res.contractNumber > 0) dialogHTML += `<p class="font-italic mt-3 mb-1"><i class="icon-info-circle text-primary mr-2"></i>Smlouva o členství byla úspěšně vytvořena a je možné ji dohledat v sekci členství</p><a class="btn btn-primary btn-block" href="/admin/contract/get_contract_pdf?contractNumber=${res.contractNumber}&userId=${clientId}" target="_blank">Vytisknout smlouvu</a>`;
                                if('vouchers' in res){
                                    dialogHTML += '<p class="mt-3 mb-1 font-weight-bold">Založené vouchery</p><table class="table-bordered w-100"><tr style="background:var(--primary);" class="text-light"><th>Kód</th><th>Název</th><th>Cena</th></tr>';
                                    $.each(res.vouchers, function(i,v){
                                        dialogHTML += `<tr><td>${v.code}</td><td>${v.name}</td><td class="text-right">${parseInt(v.vat_price)} Kč</td></tr>`;
                                    });
                                    dialogHTML += '</table>';
                                }
                                $("#dialog").html(dialogHTML);
                                self.showDialog('Doplňující informace');
                            }
                            // clear form
                            self.resetRegister();
                        } else N.show('error', 'Nepodařilo se vytvořit transakci, zkontrolujte údaje nebo to zkuste znovu!');
                        NProgress.done();
                    });
                }
            });
        },
        returnTransType: function(c){
            var d = c.getRow().getData();
            return `${d.transType} (${d.transCategory})`;
        },
        returnPaidStatus: function(c){
            var d = c.getRow().getData(),
                el = c.getElement();
            
            if(d.paid && d.refund !== "1"){
                $(el).addClass("paid-transaction");
            }else{
                $(el).addClass("unpaid-transaction");
            }

            if(d.refund === "1" && d.transCategory == "PK"){
                return "VRÁCENO NA KARTU";
            }else if (d.refund === "1" && d.transCategory != "PK"){
                return "STORNOVÁNO";
            }else{
                return (c.getValue()) ? "ANO" : "ČEKÁ NA PLATBU";
            }
        },
        returnSubTableButtons: function(c){
            var d = c.getRow().getData();
            
            var pay_button = '';
            if(!d.paid && !d.cancelled){
                pay_button += '<a href="javascript:;" class="btn btn-success btn-xs pay-subscription" data-transid="'+d._id+'" data-vat="'+d.vat+'" data-value="'+d.value+'" data-vat_value="'+d.vat_value+'" data-start="'+d.start+'" data-end="'+d.end+'">Zaplatit</a>&nbsp;';
            }

            if(!d.cancelled) pay_button += '<a href="javascript:;" class="btn btn-danger btn-xs refund-subscription" data-paid="'+d.paid+'" data-transid="'+d._id+'" data-vat="'+d.vat+'" data-value="'+d.value+'" data-vat_value="'+d.vat_value+'" data-start="'+d.start+'" data-end="'+d.end+'">Storno</a>&nbsp;';
            return pay_button;
        },
        resetRegister: function(){
            self.summary_table.find('tbody').html('');
            self.purchase_type_table.find('tbody').html('');
            self.client_select.val('').trigger('change');
            $('#c_credit').html('');
            $(".purchase-type .card-input-element").prop('checked',false);
            $('#price_total').text('0.00');
            $('#note, #credit_value, #vatInfoForm input').val('');
            $('#invoice_client_country').val('CZ');
            $('#note, #credit_value').val('');
            $('#printContractPreview').addClass('hidden');
        },
        removeItem: function(el){
            let row = $(el).closest('tr'),
                itemId = row.find('td:first-child').next('td').data('id');
            row.remove();
            if(!self.summary_table.find(`td[data-id='${itemId}']`).length){ // there is no more items with same ID in summary table
                $(`#systemNotes p[data-id='${itemId}']`).remove();
                if(!$('#systemNotes p').length) $('#systemNotes').hide();
            }
            
            self.countPrice();
        },
        removeItemEdit: function(el){
            $(el).closest('tr').remove();
            self.countPrice(true);
        },
        removePurchaseTypeItem: function(el,id){
            var tr = $(el).closest('tr');
            if(tr.hasClass('voucher')){ // Vouchers
                let voucher_code = tr.find(`td input[type='number']`).data('voucher-code');
                self.summary_table.find(`tbody td[data-voucher-code='${voucher_code}']`).closest('tr').remove();
            }
            if(tr.data('parent')){ // purchase subtype
                let parent = tr.data('parent');
                if(self.purchase_type_table.find(`[data-parent='${parent}']`).length==1){
                    $(`#${parent}`).prop('checked',false);
                }
            }
            $(el).closest('tr').remove();
            $(`.card-input-element[value="${id}"]`).prop('checked',false);
            self.countPurchaseTypePrices(); // divides price into same pieces
        },        
        countPrice: function(edit=false,startPtCountFrom=false){
            let price_total = self.countPurchaseTypePrices(edit,startPtCountFrom); // divides price into same pieces
                        
            if(edit){
                $('#price_total_edit').text(GYM._separateThousands(self.priceFormat(price_total)));
            } else {
                $('#price_total').text(GYM._separateThousands(self.priceFormat(price_total)));
                // 10k and more -> vat enabled data
                if(price_total >= 10000) $('#vatInfoForm').collapse('show');
                else $('#vatInfoForm').collapse('hide');;
            }
            
        },
        countPurchaseTypePrices: function(edit=false,startPtCountFrom){
            var pt_table = edit ? self.purchase_type_table_edit : self.purchase_type_table,
                items_table = edit ? self.summary_table_edit : self.summary_table,
                startPtCountFrom = startPtCountFrom || pt_table.find('tbody tr:first-child'),
                ptItemsToCount = $(startPtCountFrom).nextAll('tr:not(.voucher)').addBack(),
                price_total = 0,
                pt_locked_price = 0;

            items_table.find('td:first-child').next('td:not(.voucher)').each(function () {
                let discount = parseInt($(this).next().find('.input-discount').val()),
                    item_count = parseInt($(this).find('.input-count').val()),
                    item_price = parseFloat($(this).data('price')),
                    discount_value = item_price * (discount / 100);
                
                price_total += item_count * (item_price - discount_value);
            });

            let types_count = pt_table.find('tbody tr:not(.voucher)').length;

            startPtCountFrom.prevAll('tr:not(.voucher)').find('td input[type="number"]').each(function () {
                types_count --;
                pt_locked_price += parseFloat($(this).val());
            });
            price_total -= pt_locked_price;

            let credit_amount = parseFloat(self.creditAmount.text().replace(/\s/g, '')),
                divided_price = price_total/types_count,
                ceiledCash = false;

            if(ptItemsToCount.find(`[data-id='4']`).length){ // Multisport pay
                var prices = [];
                items_table.find('td:first-child').next('td').each(function () { // get all services, that can be paid by multisport and has no discout
                    let discount = parseInt($(this).next().find('.input-discount').val()),
                        item_price = parseFloat($(this).data('price'));
                    if(discount==0 && self.multisportServiceTypes.includes($(this).data('service-type'))) prices.push(item_price); // push price to array
                });
                let lowestPrice = Math.min.apply(Math,prices);
                if(isFinite(lowestPrice)){
                    pt_table.find(`[data-id='4']`).val(lowestPrice);
                    price_total = price_total - lowestPrice; // create new price_total
                    divided_price = price_total / (types_count - 1); // create new divied price
                    types_count--;
                } else { // there is nothing to pay by multisport
                    $(`.purchase-type .card-input-element[value='4']`).prop('checked',false);
                    pt_table.find(`[data-id='4']`).closest('tr').remove();
                }
            } else if (pt_table.find(`[data-id='4']`).length){
                // remove multisport price set by receptionist
                pt_locked_price -= pt_table.find(`[data-id='4']`).val();
            }

            if(!(Number.isInteger(divided_price)) && ptItemsToCount.find(`[data-id='1']`).length){ // ceil cash value and divide again
                ptItemsToCount.find(`[data-id='1']`).val(Math.ceil(divided_price));
                let price_to_divide = price_total - (divided_price - divided_price % 1) - 1; // create new price_total
                divided_price = price_to_divide / (types_count - 1); // create new divied price                    
                ceiledCash = true;
            }

            let creditAbsence = credit_amount < divided_price;
            if(creditAbsence) types_count-1;
            
            $(ptItemsToCount).find('td input[type="number"]').each(function(){
                if( $(this).data('id')!=4 && !($(this).data('id')==1 && ceiledCash) ){ // multisport is already set & pay by cash should be without decimals
                    if($(this).data('id')==3 && creditAbsence){ // credit absence
                        $(this).val(credit_amount);
                    } else $(this).val(divided_price); 
                }
            });

            // if only pay by cash -> ceil value
            if(types_count==1 && pt_table.find(`[data-id='1']`).length) price_total = Math.ceil(price_total);

            return price_total + pt_locked_price;
        },
        validatePurchaseTypePrices: function(edit){

            edit = edit || false;
            var table = self.purchase_type_table;
            if(edit !== false) table = self.purchase_type_table_edit;

            let price_total = parseFloat($((edit) ? '#price_total_edit' : '#price_total').text().replace(/\s/g, '')),
                price_from_pruchase_type = 0;
            table.find('tbody td input[type="number"]').each(function(){
                // substract voucher and multisport
                if(!$(this).closest('tr').hasClass('voucher') && $(this).data('id')!=4) price_from_pruchase_type += parseFloat($(this).val());
            });            
            if(Math.round(price_total) == Math.round(price_from_pruchase_type)) return true;
            else return false;
        },
        checkPurchaseTypeValueChange: function(el){
            let credit_amount = parseFloat(self.creditAmount.text().replace(/\s/g, ''));
            if($(el).data('id')===3 && (credit_amount < $(el).val())){
                N.show('error', 'Klient nemá dostatek kreditu!');
                $(el).val(credit_amount);
            }
            self.countPrice(false,$(el).closest('tr').next());
        },
        getIconRemove: function(){
            return `<i class="icon-close text-danger float-right" onclick="PAYMENTS.removeItem(this);"></i>`;
        },
        getInputCount: function(value=1){
            return `<input min="1" max="999" class='form-control input-count' type='number' value='${value}' />`;
        },
        getInputDiscount: function(value=0,disabled=false){
            disabled = disabled || !GYM._isAllowed('edit','SECTION_TRANSACTION') ? 'disabled' : '';
            return `<input step="5" min="0" max="100" class='form-control input-discount' type='number' value='${value}' ${disabled}/>`;
        },
        append2Note: function(id,text){
            if(!$('#systemNotes').is(":visible")) $('#systemNotes').show();
            $('#systemNotes').append(`<p data-id="${id}">${text}</p>`)
        },
        addItemToSummary: function(item,type){
            if(!item.length || !$(item).val()){
                N.show('error', 'Není vybraná žádná položka!');
            }else{
                if(type=='depot') self.addDepotItemToSummary(item);
                else if(type=='service') self.addServiceItemToSummary(item);
            }
        },        
        addDepotItemToSummary: async function(item){
            let itemId = item.attr("value"),
                clientId = self.client_select.val(),
                cardId = self.client_select.find('option:selected').attr('data-cardId'),            
                summary_item = self.summary_table.find(`[data-id='${itemId}'][data-depotid='1']`);            
            
            if(summary_item.length){ // already in summary table
                let discount = parseInt(summary_item.closest('td').next().find('.input-discount').val()), // percentage
                    item_price = parseFloat(summary_item.data('price')),
                    summary_item_count = parseInt(summary_item.find('.input-count').val()) + 1,
                    discount_value = item_price * (discount / 100),
                    price = summary_item_count * (item_price - discount_value);

                summary_item.find('.input-count').val(summary_item_count);
                summary_item.closest('td').next().next().text(GYM._separateThousands(self.priceFormat(price)));                
            } else { // not in summary, get data and append
                await GYM._post('/admin/depot/get_item_info_simple_ajax', {client_id:clientId,card_id:cardId,item_id:itemId}).done(function (res) {
                    $.each(res.data.stocks, function(i, depot){
                        if(depot.depot_id == self.depot_select.val()){
                            if(res.benefit){
                                self.append2Note(item.attr("value"),`${item.text()} - Benefit (sleva ${res.benefit.discount} %)`);
                                let discount_value = res.data.sale_price_vat * (res.benefit.discount / 100),
                                    item_price = res.data.sale_price_vat - discount_value;
                                self.summary_table.append(`<tr><td>${item.text()}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-benefit='${res.benefit.id}' data-vat='${res.data.vat_value}' data-price='${self.priceFormat(res.data.sale_price_vat)}' data-stock='${parseInt(depot.stock)}' data-depotid='${depot.depot_id}' data-id='${item.attr("value")}'>${self.getInputCount()}</td><td>${self.getInputDiscount(res.benefit.discount)}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item_price))}</td><td>${self.getIconRemove()}</td></tr>`);
                            } else self.summary_table.append(`<tr><td>${item.text()}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-vat='${res.data.vat_value}' data-price='${self.priceFormat(res.data.sale_price_vat)}' data-stock='${parseInt(depot.stock)}' data-depotid='${depot.depot_id}' data-id='${item.attr("value")}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(res.data.sale_price_vat))}</td><td>${self.getIconRemove()}</td></tr>`);
                        }
                    });
                });
            }  
            self.countPrice();               
        },
        addSolariumToSummary: async function(el){
            let solarium_id=$(el).closest('.popover-body').find('.solarium_id option:selected'),
                minutes = $(el).closest('.popover-body').find('.solarium-minutes');
            if(!minutes.val()){
                minutes.addClass('invalid');
                N.Show('error','Vyplňte prosím počet minut');
            } else {
                let item = $("#service_item option:selected");
                await self.addServiceItemToSummary(item,parseInt(minutes.val()),{
                    solarium_id:solarium_id.attr('value'), 
                    solarium_name:solarium_id.text()
                });
                self.btnAddServiceItem.popover('hide');
            }
        },
        addServiceItemToSummary: async function(item,amount=1,additionalData=false){
            let itemId = item.attr("value"),
                clientId = self.client_select.val(),
                cardId = self.client_select.find('option:selected').attr('data-cardId'),
                summary_item = additionalData.solarium_id != null ? self.summary_table.find(`[data-id='${itemId}'][data-solarium='${additionalData.solarium_id}']`) : self.summary_table.find(`[data-id='${itemId}'][data-service='1']`);

            if(summary_item.length){ // already in summary table
                let discount = parseInt(summary_item.closest('td').next().find('.input-discount').val()), // percentage
                    item_price = parseFloat(summary_item.data('price')),
                    summary_item_count = parseInt(summary_item.find('.input-count').val()) + amount,
                    discount_value = item_price * (discount / 100),
                    price = summary_item_count * (item_price - discount_value);

                summary_item.find('.input-count').val(summary_item_count);
                summary_item.closest('td').next().next().text(GYM._separateThousands(self.priceFormat(price)));                
            } else { // not in summary, get data and append
                await GYM._post('/admin/pricelist/get_checkout_item_info_ajax', {client_id:clientId,card_id:cardId,item_id:itemId}).done(function (res) {
                    let p = res.data,
                        item_data_attributes = `data-id='${itemId}' data-service-type='${p.service_type}' data-service-subtype='${p.service_subtype}' data-price='${self.priceFormat(p.vat_price)}' data-vat='${p.vat}' data-service='1'`,
                        item_name=item.text();

                    if(additionalData && additionalData.solarium_id != null){ // is it solarium?
                        item_name += `<br /><small>Solárium: ${additionalData.solarium_name}</small>`;
                        item_data_attributes += `data-solarium='${additionalData.solarium_id}'`; 
                    }

                    if(res.benefit){ // is it with benefit?
                        self.append2Note(itemId,`${item.text()} - Benefit (sleva ${res.benefit.discount} %)`);
                        let discount_value = p.vat_price * (res.benefit.discount / 100),
                            item_price = (p.vat_price - discount_value) * amount;
                        item_data_attributes += ` data-benefit='${res.benefit.id}'`;
                        self.summary_table.append(`<tr><td>${item_name}</td><td class='text-right' ${item_data_attributes}>${self.getInputCount(amount)}</td><td>${self.getInputDiscount(res.benefit.discount)}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item_price))}</td><td>${self.getIconRemove()}</td></tr>`);
                    } else {
                        let item_price = p.vat_price * amount;
                        self.summary_table.append(`<tr><td>${item_name}</td><td class='text-right' ${item_data_attributes}>${self.getInputCount(amount)}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item_price))}</td><td>${self.getIconRemove()}</td></tr>`);
                    }
                });
            }  
            self.countPrice();
        },
        addSubscriptionToSummary: function(data){
            self.resetRegister();
            self.client_select.val(data.client_id).trigger('change');

            $.each(data.items, function(i, item){
                if(typeof item.existing_payment != 'undefined'){
                    if(typeof item.transaction_id != 'undefined'){
                        self.summary_table.append(`<tr><td>${item.item_name}</td><td class='text-right' data-stock='0' data-subscription='1' data-id='${item.sub_id}' data-vat ='${item.vat}' data-price='${self.priceFormat(item.value + item.vat_value)}' data-contract='${data.contract_number}' data-transid='${item.transaction_id}' data-subperiod='${item.sub_period}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item.value + item.vat_value))}</td><td>${self.getIconRemove()}</td>`);
                    }else{
                        self.summary_table.append(`<tr><td>${item.item_name}</td><td class='text-right' data-stock='0' data-subscription='1' data-id='${item.sub_id}' data-vat ='${item.vat}' data-price='${self.priceFormat(item.value + item.vat_value)}' data-contract='${data.contract_number}' data-subperiod='${item.sub_period}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item.value + item.vat_value))}</td><td>${self.getIconRemove()}</td>`);
                    } 
                }else{
                    self.summary_table.append(`<tr><td>${item.item_name}</td><td class='text-right' data-stock='0' data-newsubscription='1' data-start='${item.sub_start}' data-id='${item.sub_id}' data-vat ='${item.vat}' data-price='${self.priceFormat(item.value + item.vat_value)}' data-contract='${data.contract_number}' data-subperiod='${item.sub_period}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(item.value + item.vat_value))}</td><td>${self.getIconRemove()}</td>`); 
                }

                self.countPrice();
            });
        },
        addCreditToSummary: function(value){
            var summary_item = self.summary_table.find('[data-id=0]:not(.voucher)');

            if(summary_item.length){ // already in summary table && is not voucher
                let items_price = parseInt(summary_item.next().next().text().replace(' ','')) + parseInt(value);
                summary_item.closest('td').next().next().text(GYM._separateThousands(self.priceFormat(items_price)));                
                summary_item.closest('td').data('price',items_price);
            } else {
                self.summary_table.append(`<tr><td>Dobití kreditu</td><td class='text-right' data-vat='0' data-id='0' data-price='${self.priceFormat(value)}'>${self.getInputCount()}</td><td>${self.getInputDiscount()}</td><td class='text-right'>${GYM._separateThousands(self.priceFormat(value))}</td><td>${self.getIconRemove()}</td>`);            
            }
            self.countPrice();
        },
        priceFormat: function(x){ // 1 -> 1.00
            return parseFloat(Math.round(x * 100) / 100).toFixed(2);
        },
        getSessionCheckout: () => {
            if(localStorage.getItem('checkout_id')==null) return false;
            else return {
                'checkout_id':localStorage.getItem('checkout_id'), 
                'checkout_eet_id':localStorage.getItem('checkout_eet_id'), 
                'checkout_name':localStorage.getItem('checkout_name')
            };
        },
        getSessionTerminal: () => {
            if(localStorage.getItem('terminal_id')==null) return false;
            else return {
                'terminal_id':localStorage.getItem('terminal_id'), 
                'terminal_ip':localStorage.getItem('terminal_ip'), 
                'terminal_name':localStorage.getItem('terminal_name')
            };
        },
        initCheckout: () => {
            let checkout_id = self.checkout.checkout_id,
                state = $(`#checkout_id_select option[data-id=${checkout_id}]`).data('state');
            if(state==0){ // must open checkout first
                self.checkout.state=0;
                $('#show-open-checkout-modal').data('remote',`/admin/eetapp/set-checkout-state/${checkout_id}`).click();
                $('#show-close-checkout-modal, #show-open-checkout-modal').toggleClass('d-none');
            } 
            $('#checkout_name').text(self.checkout.checkout_name);
            $('#show-close-checkout-modal').data('remote',`/admin/eetapp/set-checkout-state/${checkout_id}`);            
        },
        setSessionCheckout: function(checkoutId, checkoutEETId, checkoutName){
            localStorage.setItem('checkout_id', checkoutId);
            localStorage.setItem('checkout_eet_id', checkoutEETId);
            localStorage.setItem('checkout_name', checkoutName);
            $('#show-close-checkout-modal').data('remote',`/admin/eetapp/set-checkout-state/${checkoutId}`);            
            return true;
        },
        setSessionTerminal: function(terminalId, terminalIP, terminalName){
            localStorage.setItem('terminal_id', terminalId);
            localStorage.setItem('terminal_ip', terminalIP);
            localStorage.setItem('terminal_name', terminalName);
            return true;
        },
        chooseSessionDevices: async () => {
            let next = false;
            if (localStorage.getItem("checkout_eet_id") !== null) $('#checkout_id_select').val(localStorage.getItem("checkout_eet_id"));
            if (localStorage.getItem("terminal_id") !== null) $('#terminal_id_select').val(localStorage.getItem("terminal_id"));
            $('#chooseCheckoutModal').modal({backdrop: 'static', keyboard: false});  
            $('#chooseCheckoutModal').modal("show");


            // wait till user input
            const timeout = async ms => new Promise(res => setTimeout(res, ms));
            const wait4UserInput = async () => {
                while (next === false) await timeout(100); // pause script but avoid browser to freeze ;)
                next = false; // reset var
                //console.log('user input detected');
            }

            const getDevices = async () => {
                await wait4UserInput();
                let checkout_eet_id=$('#checkout_id_select').val(),
                    checkout_id=$("#checkout_id_select option:selected").data('id'),
                    checkout_name=$("#checkout_id_select option:selected").text(),
                    terminal_id=$('#terminal_id_select').val(),
                    terminal_ip=$("#terminal_id_select option:selected").data('ip'),
                    terminal_name=$("#terminal_id_select option:selected").text();
                self.setSessionCheckout(checkout_id,checkout_eet_id,checkout_name);
                self.setSessionTerminal(terminal_id,terminal_ip,terminal_name);
                $('#checkout_name').text(checkout_name);
                $('#chooseCheckoutModal').modal("hide");
                N.show('success', 'Pokladna zvolena');  
                return {'checkout':self.getSessionCheckout(), 'terminal':self.getSessionTerminal()};         
            }

            self.chooseCheckoutModal.submit((e) => {             
                e.preventDefault();
                let checkoutSelect = $('#checkout_id_select'),
                    terminalSelect = $('#terminal_id_select');
                if(!checkoutSelect.val()){
                    checkoutSelect.addClass('invalid');
                    N.show('error','Vyberte prosím pokladnu');
                    return false;
                } else if(!terminalSelect.val()){
                    terminalSelect.addClass('invalid');
                    N.show('error','Vyberte prosím terminál');
                    return false;
                } else {
                    checkoutSelect.removeClass('invalid');
                    terminalSelect.removeClass('invalid');
                }                   
                next = true; 
            })
            return getDevices();
        },
        initPaymentsTable: function(){
            this.payments_table = new Tabulator(this.payments_table, {
                selectable: true,
                selectableRangeMode:"click",
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné transakce",
                headerFilterPlaceholder:"Hledat..",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting: true,
                ajaxFiltering: true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: '#', field: 'transactionNumber', headerFilter: true, formatter: this.lockStatus },
                    {title: 'KLIENT', field: 'client_name', headerSort:false, headerFilter: true, headerFilterPlaceholder:"Hledat podle klienta", editor: self.tableSelect2, editable: false },
                    {title: 'PROVEDL', field: 'employee_name', headerFilter: true, headerFilterPlaceholder:"Hledat podle zaměstnance", editor: self.tableSelect2, editable: false, headerSort:false },
                    {title: 'DATUM', field: 'paidOn', formatter:"datetime", headerFilter: true, headerFilterPlaceholder:"Hledat podle dne", editor: self.dateEditorPayments, editable: false, formatterParams:
                        {
                            inputFormat:"YYYY-MM-DD HH:mm:ss",
                            outputFormat:"DD.MM.YYYY HH:mm:ss",
                            invalidPlaceholder:"(invalid date)"
                        }
                    },
                    {title: 'TYP PLATBY', field: 'transType', headerFilter:true, formatter: self.returnTransType },
                    {title: 'ZAPLACENO', field: 'paid', headerFilter: true, formatter: self.returnPaidStatus },
                    {title: 'CENA', field: 'value', headerFilter:true, formatter: this.formatValues },
                    {title: '', field: '', align: 'right', formatter: this.transactionButtons}
                ],
                cellClick:function(e, cell){
                    if($(e.target).hasClass('btn')){
                        cell.getRow().toggleSelect(); // turn off row selection on this one
                        $(e.target).addClass('running'); // show loading indicator on button
                    }
                },
                rowSelectionChanged:function(data, rows){
                    if(rows.length > 0 && self.close_transaction.hasClass("disabled")) {
                        self.close_transaction.removeClass("disabled");
                    }
                    else if (rows.length <= 0) {
                        self.close_transaction.addClass("disabled");
                    }

                    if(data.length > 0){
                        var last = data[data.length - 1];
                        if(typeof last.locked != 'undefined'){
                            if(last.locked) self.payments_table.deselectRow(rows[data.length - 1].getIndex());
                        }
                    }
                },
            });
            this.payments_table.setLocale("cs-cs");
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
                    {title: 'OD', field: 'start', formatter: function(c){
                            return moment(c.getValue()).format('DD.MM.YYYY');
                        }
                    },
                    {title: 'DO', field: 'end', formatter: function(c){
                            return moment(c.getValue()).format('DD.MM.YYYY');
                        }
                    },
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
                            if(typeof c.getRow().getData().cancelled != 'undefined' && c.getRow().getData().cancelled){
                                $(el).css('background', '#e8e8e8');
                                return 'STORNO';
                            }else{
                                if(c.getValue()){
                                    $(el).css('background', '#dbffdb');
                                    if(typeof c.getRow().getData().deposit != 'undefined'){
                                        return 'Ano (záloha)';
                                    }else{
                                        return 'Ano';
                                    }
                                }else{
                                    $(el).css('background', '#ffdbdb');
                                    return 'Ne';
                                }
                            }
                        }
                    },
                    {title: '', field: '', formatter: self.returnSubTableButtons}
                ]
            });
            this.sub_payment_table.setLocale("cs-cs");
        },
        initInvoicesTables: function(){
            // invoice table
            this.invoice_table = new Tabulator(this.invoice_table, {
                layout: 'fitColumns',
                placeholder:"Nebyly nalezeny žádné faktury",
                headerFilterPlaceholder:"Hledat..",
                resizableColumns: false,
                pagination: 'remote',
                ajaxSorting: true,
                ajaxFiltering: true,
                paginationSize: 20,
                paginationSizeSelector:[10, 20, 30, 50, 100],
                langs: GYM.tabulator_czech,
                layoutColumnsOnNewData:true,
                columns: [
                    {title: '#', field: 'invoice_number', headerFilter: true },
                    {title: 'KLIENT', field: 'client_name', headerFilter: true, headerFilterPlaceholder:"Hledat podle klienta", editor: self.tableSelect2, editable: false, headerSort:false },
                    {title: 'VYTVOŘIL', field: 'employee_name', headerFilter: true, headerFilterPlaceholder:"Hledat podle správce", editor: self.tableSelect2, editable: false, headerSort:false },
                    {title: 'SPLATNOST', field: 'due_date', headerFilter:true, editor:this.dateEditor, editable: false, formatter: this.returnDueDate},
                    {title: 'ZAPLACENO', field: 'paid', headerFilter:true, formatter: this.invoiceStatus},
                    {title: 'CENA', field: 'value', headerFilter:true, formatter: this.formatValues },
                    {title: '', field: '', align: 'right', formatter: this.invoiceButtons}
                ]
            });
            this.invoice_table.setLocale("cs-cs");
        },
        formatValues: function(c){
            var value = c.getValue(),
                d = c.getRow().getData(),
                formatter = new Intl.NumberFormat('cs-CZ', {
                    style: 'currency',
                    currency: d.currency
                });
            if (d.refund !== "1") return formatter.format(value);
            else return "-" + formatter.format(value);
        },
        tableSelect2: function(c, onRender, success, cancel, params){
            var editor = document.createElement("select"),
                field = c.getField();

            editor.id = field == 'client_name' ? 'clientSelector' : 'employeeSelector';
            editor.classList.add('table-s2');
            editor.setAttribute('data-type', field == 'client_name' ? 'clients' : 'employees');
            editor.setAttribute('data-lang', field == 'client_name' ? 'klienta' : 'zaměstnance');

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
        dateEditorPayments: function(cell, onRendered, success, cancel, editorParams){
            var editor = document.createElement("input");
                editor.id = "dayselect";

            flatpickr(editor, { dateFormat: flatpickrDateFormat });
            onRendered(function(){ editor.focus(); editor.style.css = "100%"; });
            editor.style.padding = "4px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";
            editor.addEventListener("change", successFunc);
            return editor;
            function successFunc(){ success(moment(editor.value, tabulatorDateFormat).format("YYYY-MM-DD")); }
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
                                        text: item.full_name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });
            });
        },
        invoiceStatus: function(c){
            var el = c.getElement(),
                d = c.getRow().getData();

            if(d.cancelled != 1){
                if(c.getValue() == 0){
                    $(el).css('background', '#ffdbdb');
                    return 'Čeká na platbu';
                }else{
                    $(el).css('background', '#dbffdb');
                    return 'Zaplaceno' + '<br/><small>Zaplaceno: ' + moment(d.payment_date).format('DD. MM. YYYY') + '</small>';
                }
            }else{
                $(el).css('background', '#ffdbdb');
                    return 'Storno' + '<br/><small>Stornováno: ' + moment(d.payment_date).format('DD. MM. YYYY') + '</small>';
            }
        },
        returnDueDate: function(c){
            var d = c.getRow().getData();
            var today = moment().format('YYYY-MM-DD');
            var due_date = moment(d.due_date).format('YYYY-MM-DD');
            var el = c.getElement();

            if(today > due_date){
                $(el).addClass('cell-danger');
                var diff = moment(today).diff(moment(due_date), 'days');

                return moment(d.due_date).format('DD. MM. YYYY') + '<br/><small>(<strong>'+diff+' dní</strong> po splatnosti)</small>';
            }else if(today == due_date){
                $(el).addClass('cell-warning');
                return moment(d.due_date).format('DD. MM. YYYY') + '<br/><small>(splatnost dnes)</small>';
            }else{
                $(el).addClass('cell-success');

                var diff = moment(due_date).diff(moment(today), 'days');

                return moment(d.due_date).format('DD. MM. YYYY') + '<br/><small>(<strong>'+diff+' dní</strong> do splatnosti)</small>';
            }
        },
        invoiceButtons: function(c){
            var d = c.getRow().getData();
            var btn_cancel = '';
            var value = d.value;

            if(d.paid !== '1' && d.cancelled !== '1' && GYM._isAllowed('delete','SECTION_INVOICES')) btn_cancel = `<a href="javascript:;" data-invoiceid="${d.id}" class="btn-xs btn btn-danger ml-1 cancel-invoice ld-ext-right">Storno<div class="ld ld-ring ld-spin-fast"></div></a>`;
            var btn_pdf = `<a href="javascript:;" data-invoiceid="${d.id}" class="btn btn-primary btn-xs invoice-get-pdf">PDF</a>`;
            var pay_btn = "";
            if(d.paid !== '1' && d.cancelled !== '1') pay_btn = `<a href="javascript:;" data-amount="${value}" data-invoiceid="${d.id}" class="btn btn-success ml-1 btn-xs invoice-pay-now">Zaplatit</a>`;

            var voucher_btn = "";
            voucher_btn = `<br /><a href="/admin/vouchers/?invoice=${d.id}" target="_blank" class="btn btn-warning mt-1 btn-xs">Vouchery</a>`;

            return btn_pdf + pay_btn + btn_cancel + voucher_btn;
        },
        transactionButtons: function(c){
            var d = c.getRow().getData();

            var btn = '',
                btn_refund = '',
                btn_printReceipt = '';

            // Ignore subscriptions for refund, refunds for subscriptions should be in the sub modal
            // Also ignore locked trans for editing
            if(!d.locked && d.refund !== "1" && d.transCategory != "OD") btn = `<a href="javascript:;" onClick="PAYMENTS.openTransactionEditModal('${GYM._b64encode(JSON.stringify(d))}')" class="btn-xs btn btn-primary edit-transaction ld-ext-right">Editovat<div class="ld ld-ring ld-spin-fast"></div></a>`;
            if(!d.locked && d.refund !== "1" && !d.subscriptionPayment && d.transCategory != "OD" && GYM._isAllowed('delete','SECTION_TRANSACTIONS')) btn_refund = `<a href="javascript:;" onClick="PAYMENTS.openRefundModal('${GYM._b64encode(JSON.stringify(d))}')" class="btn-xs btn btn-danger ml-1 edit-transaction ld-ext-right">Storno<div class="ld ld-ring ld-spin-fast"></div></a>`;

            return btn + btn_refund;
        },
        openRefundModal: function (data){
            data = JSON.parse(GYM._b64decode(data));

            let input_count = function (amount, max, min) { return `<input disabled min="${typeof min != 'undefined' ? min : 1}" max="${typeof max != 'undefined' ? amount+max : 999}" class='form-control input-count' type='number' value='${amount}' />`; },
                input_value = function (value) { return `<input disabled min="0" class="form-control input-value" type="number" step="0.01" value="${value}" />`; };

            GYM._post('/admin/payments/get_transaction_for_editing', {trans_id: data._id}).done(function(res){
                $('.refund-trans-number').text('#'+data.transactionNumber);

                // ITEMS
                if (res.data.items.length > 0) $.each(res.data.items, function(i, item){
                    if (typeof item.depotId !== 'undefined'){
                        // DEPOT
                        GYM._post('/admin/depot/get_item_info_simple_ajax', {item_id: item.id}).done(function (r) {
                            $.each(r.data.stocks, function(i, depot){
                                if(depot.depot_id == item.depotId){
                                    let stock = parseInt(depot.stock);
                                    self.summary_table_refund.append(`<tr><td>${item.name}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-bought='${item.amount}' data-vat='${item.sale_vat}' data-price='${self.priceFormat(item.sale_value)}' data-stock='${stock}' data-depotid='${depot.depot_id}' data-id='${item.id}'>${input_count(item.amount, stock, 0)}</td><td>${self.getInputDiscount(item.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(item.sale_value))}</td></tr>`);
                                }
                            });
                        });
                    }else{
                        // PRICELIST
                        self.summary_table_refund.append(`<tr><td>${item.name}</td><td class='text-right' data-id='${item.id}' data-subpaymentid='${item.subscriptionSubPaymentId}' data-price='${self.priceFormat(item.sale_value)}' data-vat='${item.sale_vat}' data-service='1'>${input_count(item.amount, 999, 0)}</td><td>${self.getInputDiscount(item.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(item.sale_value))}</td></tr>`);
                    }
                });

                // CREDIT
                if (res.data.credit.length > 0) $.each(res.data.credit, function(i, credit){
                    self.summary_table_refund.append(`<tr><td>Dobití kreditu</td><td class='text-right' data-stock='0' data-id='0' data-vat ='${credit.sale_vat}' data-price='${self.priceFormat(credit.sale_value)}'>${input_count(credit.amount, 999, 0)}</td><td>${self.getInputDiscount(credit.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(credit.sale_value))}</td>`);
                });

                // SUBS
                if (res.data.subs.length > 0) $.each(res.data.subs, function (i, sub) {
                    self.summary_table_refund.append(`<tr><td>${sub.sub_info.name}</td><td class='text-right' data-subscription='1' data-id='${sub.id}' data-vat ='${sub.sale_vat}' data-price='${self.priceFormat(sub.sale_value)}' data-contract='${sub.contract}' data-subperiod='${sub.sub_period}'>${input_count(1, 0, 1)}</td><td>${self.getInputDiscount(sub.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(sub.sale_value))}</td>`);
                });

                $('#note_refund').val(data.text);
                self.purchase_type_table_refund.append(`<tr id="pt_${res.data.payment_type.transType}"><td>${res.data.payment_type.value}</td><td class='text-right'><input disabled min="0" max="999999" step="0.01" class='form-control' type='number' data-id='${res.data.payment_type.transType}' step="0.50" value='${res.data.total}' onchange='PAYMENTS.checkPurchaseTypeValueChange(this);' /></td></tr>`);
                self.refundTransactionModal.modal("show");
                $('.running').removeClass('running');

                self.refund_form.data('client', data.clientId);
                self.refund_form.data('card', data.cardId);
                self.refund_form.data('trans', data._id);
                self.refund_form.data('total', data.value);

                var price_total = 0;
                self.summary_table_refund.find('td:first-child').next('td').each(function () {
                    let discount = parseInt($(this).next().find('.input-discount').val()),
                        item_count = parseInt($(this).find('.input-count').val()),
                        item_price = parseFloat($(this).data('price')),
                        discount_value = item_price * (discount / 100);
                    price_total += item_count * (item_price - discount_value);
                });
                $('#price_total_refund').text(GYM._separateThousands(self.priceFormat(price_total)));

                let price_total_m = parseFloat($( '#price_total_refund' ).text().replace(/\s/g, '')),
                    types_count = self.purchase_type_table_refund.find('tbody tr').length,
                    credit_amount = parseFloat(self.creditAmount.text().replace(/\s/g, ''));
                let creditAbsence = credit_amount < (types_count/types_count),
                    divided_price = types_count/types_count;
                if(creditAbsence) types_count-1;
                self.purchase_type_table_refund.find('tbody td input[type="number"]').each(function(){
                    if(creditAbsence && $(this).data('id')==3){ // credit absence
                        $(this).val(credit_amount);
                    } else if($(this).data('id')==1){
                        $(this).val(Math.ceil(divided_price));
                    } else $(this).val(divided_price);
                });
            });
        },
        openTransactionEditModal: function (data) {
            data = JSON.parse(GYM._b64decode(data));

            let input_count = function (amount, max, min) { return `<input min="${typeof min != 'undefined' ? min : 1}" max="${typeof max != 'undefined' ? amount+max : 999}" class='form-control input-count' type='number' value='${amount}' />`; },
                input_value = function (value) { return `<input min="0" class="form-control input-value" type="number" step="0.01" value="${value}" />`; };

            GYM._post('/admin/payments/get_transaction_for_editing', {trans_id: data._id}).done(function(res){
                $('.edit-trans-number').text('#'+data.transactionNumber);
                let printReceiptTransId = data.parentTransaction === undefined ? data._id : data.parentTransaction;
                $('.btn-print-receipt').data('transactionId',printReceiptTransId);

                // ITEMS
                if (res.data.items.length > 0) $.each(res.data.items, function(i, item){
                    if (typeof item.depotId !== 'undefined'){
                        // DEPOT
                        GYM._post('/admin/depot/get_item_info_simple_ajax', {item_id: item.id}).done(function (r) {
                            $.each(r.data.stocks, function(i, depot){
                                if(depot.depot_id == item.depotId){
                                    let stock = parseInt(depot.stock);
                                    self.summary_table_edit.append(`<tr><td>${item.name}<br /><small>Sklad: ${depot.name}</small></td><td class='text-right' data-bought='${item.amount}' data-vat='${item.sale_vat}' data-price='${self.priceFormat(item.sale_value)}' data-stock='${stock}' data-depotid='${depot.depot_id}' data-id='${item.id}'>${input_count(item.amount, stock, 0)}</td><td>${self.getInputDiscount(item.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(item.sale_value*item.amount))}</td></tr>`);
                                }
                            });
                        });
                    }else{
                        // PRICELIST
                        self.summary_table_edit.append(`<tr><td>${item.name}</td><td class='text-right' data-id='${item.id}' data-subpaymentid='${item.subscriptionSubPaymentId}' data-price='${self.priceFormat(item.sale_value)}' data-vat='${item.sale_vat}' data-service='1'>${input_count(item.amount, 999, 0)}</td><td>${self.getInputDiscount(item.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(item.sale_value*item.amount))}</td></tr>`);
                    }
                });

                // CREDIT
                if (res.data.credit.length > 0) $.each(res.data.credit, function(i, credit){
                    self.summary_table_edit.append(`<tr><td>Dobití kreditu</td><td class='text-right' data-stock='0' data-id='0' data-vat ='${credit.sale_vat}' data-price='${self.priceFormat(credit.sale_value)}'>${input_count(credit.amount, 999, 0)}</td><td>${self.getInputDiscount(credit.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(credit.sale_value))}</td>`);
                });

                // SUBS
                if (res.data.subs.length > 0) $.each(res.data.subs, function (i, sub) {
                    self.summary_table_edit.append(`<tr><td>${sub.sub_info.name}</td><td class='text-right' data-subscription='1' data-id='${sub.id}' data-vat ='${sub.sale_vat}' data-price='${self.priceFormat(sub.sale_value)}' data-contract='${sub.contract}' data-subperiod='${sub.sub_period}'>${input_count(1, 0, 1)}</td><td>${self.getInputDiscount(sub.value_discount)}</td><td class='text-right'>${input_value(self.priceFormat(sub.sale_value))}</td>`);
                });

                $('#note_edit').val(data.text);
                self.purchase_type_table_edit.append(`<tr id="pt_${res.data.payment_type.transType}"><td>${res.data.payment_type.value} <a href="javascript:;" class="btn btn-xs btn-primary edit-payment-method" data-selected="${res.data.payment_type.transType}"><i class="icon-settings2"></i></a></td><td class='text-right'><input min="0" max="999999" step="0.01" class='form-control' type='number' data-id='${res.data.payment_type.transType}' step="0.50" value='${parseFloat(res.data.total)}' onchange='PAYMENTS.checkPurchaseTypeValueChange(this);' /></td></tr>`);
                self.editTransactionModal.modal('show');
                $('.running').removeClass('running');

                self.edit_form.data('client', data.clientId);
                self.edit_form.data('card', data.cardId);
                self.edit_form.data('trans', data._id);

                self.countPrice(true);
            });
        },
        lockStatus: function(c){
            var d = c.getRow().getData(),
                el = c.getElement();

            if(typeof d.locked != 'undefined'){
                if(d.locked){
                    $(el).addClass("locked-transaction");
                }else{
                    $(el).addClass("unlocked-transaction");
                }
            }else{
                $(el).addClass("unlocked-transaction");
            }

            return c.getValue();
        },
        pairCard: function(cardId){
            self.remote_modal.modal('hide');
            self.client_select.find('option:selected').attr('data-cardId',cardId).attr('data-new-card',1);
            self.addItemToSummary($('#service_item option[value="1"]'),'service'); // card fee
        },
        pairCardImmediately: function(cardId){
            let client_data = {};
            self.remote_modal.find('#createCardModalBtns ul.list-group li input').each(function(){
                if($(this).prop('checked')) client_data[$(this).attr('name')] = 1;
            });
            GYM._post('/admin/cards/submit_pair_ajax', {'client_id':self.client_select.val(), 'client_data':client_data, 'card_id':cardId}).done(function(res){
                if(!res.error){
                    self.remote_modal.modal('hide');
                    N.show('success', 'Karta byla úspěšně spárována');
                } else N.show('error', 'Nepovedlo se spárovat kartu, zkuste to znovu nebo později.');
            });
        },
        printReceipt: function(el){
            NProgress.start();
            GYM._post('/admin/payments/print_receipt', {transactions: [$(el).data('transactionId')] }).done(function(data){
                NProgress.done();
            });
        },
        fillvatInfoForm: function(s){
            $('#vatInfo [name="subject_name"]').val(`${s.first_name} ${s.last_name}`);
            $('#vatInfo [name="subject_id"]').val(s.company_id);
            $('#vatInfo [name="subject_vat_id"]').val(s.vat_id);
            $('#vatInfo [name="subject_street"]').val(s.street);
            $('#vatInfo [name="subject_city"]').val(s.city);
            $('#vatInfo [name="subject_zip"]').val(s.zip);
        },
        showDialog: function(title){
            $("#dialog").dialog({
                title: title,
                resizable: false,
                width: "400px",
                height: "auto",
                buttons: {
                    "Zavřít": function() {
                        $(this).dialog("close");
                    }
                }
            });
        },        
    }
}());

PAYMENTS.init();