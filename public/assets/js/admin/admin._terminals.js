'use strict';

var TERMINALS = TERMINALS || (function () {
    var self;
    return {
        URL: null,
        socketOpts: {
            query: {
                token: "J2ASaJ5wbQn6qD53ZrpA"
            },
            timeout: 4000
        },
        terminalId: null, // TODO: bank has to give us the TID
        gymCode: null,
        socket: false,//{emit: function(){console.log("emiting")}, on: function(event){return event;}}, // debug
        modal: null,
        terminal_error: false,
        payment_error: false,
        api_connected: true,
        modal_base: '<div id="paymentModalBase"><div class="bg"></div><div class="payment-modal"><div class="row payment-modal-header"><div class="col-md-12">Platební terminál</div></div><div class="row payment-modal-body"></div><div class="row payment-modal-footer"><div class="payment-loader-container"><div class="payment-loader"></div></div></div></div></div>',
        // Start and assign a socket communication
        startSocket: async function () {
            self = TERMINALS;
            if (typeof io === 'function') self.socket = io(self.URL, self.socketOpts);
            if (typeof io === 'function') self.socket.on("connect_timeout", () => {
                N.show("error", "Spojení s platebním terminálem bylo ztraceno!");
                self.api_connected = false;
            });
            if (typeof io === 'function') self.socket.on("reconnect", () => {
                N.show("success", "Spojení s platebním terminálem bylo obnoveno.");
                self.api_connected = true;
            });
            self.gymCode = await GYM._gymcode();
        },
        modal_verify_payment: function () {
            var html = '<div class="col-md-12 text-center">';

                html += '<div id="terminalStatus">';
                    html += 'Proběhla platba úspěšně?';
                html += '</div>';

                html += '<div id="terminalControls">';

                        html += '<a class="btn btn-success btn-md verify-payment">ANO</a>';
                        html += '<a class="btn btn-danger btn-md unverify-payment">NE</a>';

                html += '</div>';

            html += '</div>';

            return html;
        },
        modal_transactions: function () {
            var html = '<div class="col-md-12 text-center">';

                html += '<table id="terminalTransactions" class="table table-hover">';
                    html += '<thead>';
                        html += '<tr>';
                            html += '<th>#</th>';
                            html += '<th>Čas</th>';
                            html += '<th>Kč</th>';
                            html += '<th></th>';
                        html += '</tr>';
                    html += '</thead>';

                    html += '<tbody></tbody>';

                html += '</table>';
                
                    html += '<a class="btn btn-outline-secondary btn-md mb-4" onClick="TERMINALS.removeModal()">Zpět do pokladny</a>';
                html += '</table>';
            html += '</div>';

            return html;
        },
        // Setup terminal menu
        modal_menu: function (status) {
            var html = '<div class="col-md-12">';
                    html += '<div id="terminalStatus">';
                        html += 'Stav terminálu: '+( status != false ? '<span class="text-success">Dostupný</span>' : '<span class="text-danger">Nedostupný</span>' )+'';
                    html += '</div>';

                    html += '<div id="terminalControls">';

                        html += '<a class="btn btn-success btn-md" onClick="TERMINALS.requestTransactions()">Přehled plateb</a>';
                        html += '<a class="btn btn-primary btn-md" onClick="TERMINALS.requestSettlement()">Provést uzávěrku</a>';
                        html += '<a class="btn btn-warning btn-md" onClick="TERMINALS.requestHandshake()">Provést test linky</a>';
                        html += '<a class="btn btn-danger btn-md" onClick="TERMINALS.requestRestart()">Restartovat terminál</a>';

                        html += '<a class="btn btn-outline-secondary btn-md" onClick="TERMINALS.removeModal()">Zpět do pokladny</a>';
                    html += '</div>';
                html += '</div>';

                return html;
        },
        // Setup content for payment processing
        modal_payment_process: function (amount) {
            var html = '<div class="col-md-12">';
                    html += '<div id="paymentValue">'+amount+'<span>CZK</span></div>';
                    html += '<div id="paymentStatusMessages"><p>Vytváření spojení</p></div>';
                    html += '<div id="paymentControls">';
                        html += '<a class="btn btn-danger btn-md cancel-payment" onClick="TERMINALS.abortPayment()">Zrušit</a>';
                        //html += '<a class="btn btn-warning btn-md cancel-payment disabled">Status</a>';
                        //html += '<a class="btn btn-success btn-md cancel-payment disabled">Potvrdit</a>';
                    html += '</div>';
                html += '</div>';

                return html;
        },
        // content for "x" reversal
        modal_reversal_process: function () {
            var html = '<div class="col-md-12">';
                    html += '<div id="paymentValue">STORNO TRANSAKCE</div>';
                    html += '<div id="paymentStatusMessages"><p>Vytváření spojení</p></div>';
                html += '</div>';

                return html;
        },
        // Setup content for settlement
        modal_service_process: function (name) {
            var html = '<div class="col-md-12">';
                    html += '<div id="paymentHeadline">'+name+'</div>';
                    html += '<div id="paymentStatusMessages"><p>Vytváření spojení</p></div>';
                html += '</div>';

                return html;
        },
        // Success animation
        modal_success: function () {
            var html = '<div class="sa"> <div class="sa-success"> <div class="sa-success-tip"></div> <div class="sa-success-long"></div> <div class="sa-success-placeholder"></div> <div class="sa-success-fix"></div> </div> </div>';
                return html;
        },
        // Error animation
        modal_error: function (err) {
            if(typeof err == "object") err
            var html = '<div class="sa"> <div class="sa-error"> <div class="sa-error-x"> <div class="sa-error-left"></div> <div class="sa-error-right"></div> </div> <div class="sa-error-placeholder"></div> <div class="sa-error-fix"></div> </div> </div><p class="terminal-error">'+err+'</p>';
                return html;
        },
        // Open the cardpayment modal
        openModal: function () {
            return new Promise((resolve, reject) => {
                if(!self.modal){
                    // Append modal
                    $("body").append($(self.modal_base));
                    setTimeout(function(){ 
                        self.modal = $("body").find("#paymentModalBase");
                        self.modal.addClass("shown");

                        setTimeout(function(){
                            resolve();
                        }, 1000);
                    }, 400);

                    // disable scrolling (todo)
                    document.body.addEventListener('touchmove', function(e){e.preventDefault()}, { passive: false });
                    window.onscroll=function(){};
                }else resolve();
            });
        },
        removeModal: function () {
            return new Promise((resolve, reject) => {
                if(!self.modal) reject("Chybí platební modal!");

                self.modal.removeClass("shown");
                setTimeout(function(){
                    self.modal.remove();
                    self.modal = null;

                    self.terminal_error = false;
                    self.socket.close();
                    self.socket = null;
                    self.socket = io(self.URL, self.socketOpts);

                    resolve();
                }, 1000);
            });
        },
        populateModal: function (template) {
            return new Promise((resolve, reject) => {
                if(!self.modal) reject("Chybí platební modal!");
                var content = self.modal.find(".payment-modal-body"),
                    loader = self.modal.find(".payment-loader");
                    if(loader.is(":visible")) loader.fadeOut();

                loader.fadeIn(function(){
                    content.fadeOut(function(){
                        content.html("");
                        content.html(template);
                        content.fadeIn(function(){
                            loader.fadeOut(function(){
                                resolve();
                            });
                        });
                    });
                });
            });
        },
        requestMenu: function () {
            return new Promise ((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.socket.emit("terminal:status", {connect: true});

                        if (self.api_connected){
                            self.socket.on("terminalStatus", (status) => {
                                self.populateModal(self.modal_menu(status))
                                    .then(() => {
                                        resolve();
                                    })
                                    .catch(err => {
                                        console.log(err);
                                    });
                            });
    
                            self.socket.on("terminalError", (err) => {
                                self.populateModal(self.modal_menu(false))
                                    .then(() => {
                                        resolve();
                                    })
                                    .catch(err => {
                                        console.log(err);
                                    });
                            });
                        }else{
                            self.populateModal(self.modal_error("Spojení nemohlo být navázáno!"))
                                .then(() => { 
                                    self.removeModal()
                                        .then( () => resolve() );
                                 })
                                .catch(err => console.log(err));
                        }
                    })
                    .catch(err => console.log(err));
            });
        },
        verifyPayment: function () {
            return new Promise ((resolve, reject) => {
                self.populateModal(self.modal_verify_payment())
                    .then(() => {
                        var loader = self.modal.find(".payment-loader");

                        self.modal.find(".verify-payment").click(function(){
                            resolve();
                        });
                        self.modal.find(".unverify-payment").click(function(){
                            reject("Platba neproběhla, zopakujte ji.");
                        });
                    });
            });
        },
        requestReversal : function (tran) {
            if (typeof tran !== "object") tran = JSON.parse(GYM._b64decode(tran));

            return new Promise ((resolve, reject) => {
                self.populateModal(self.modal_reversal_process())
                    .then(() => {
                        var loader = self.modal.find(".payment-loader"),
                            payment_processing = false,
                            status_container = self.modal.find('#paymentStatusMessages');

                        loader.fadeIn(function (){
                            self.socket.emit("terminal:requestPaymentReverse", {amount: tran.value, terminalId: tran.terminalId, gymCode: tran.gymCode, sysNumber: tran.systemNumber, variableSymbol: tran.variableSymbol});
                        });

                        // Grab the current status of the transaction
                        self.socket.on("paymentStatus", (status) => {
                            // Switch up status texts
                            var current = status_container.find("p");
                                $(current).fadeOut(function(){
                                    $(current).text(status).fadeIn();
                                });
                        });
                        // Terminal status for timeouts eetc.
                        self.socket.on("terminalStatus", (status) => {
                            // Switch up status texts
                            var current = status_container.find("p");
                                $(current).fadeOut(function(){
                                    $(current).text(status).fadeIn();
                                });
                        });
            
                        // Grab any potential errors
                        self.socket.on("paymentError", (data) => {
                            if (!self.payment_error) {
                                self.populateModal(self.modal_error(data)).then(() => {
                                    self.payment_error = true;
                                    setTimeout(function(){
                                        self.removeModal().then(() => {
                                            reject(data); // Error, reject
                                        });
                                    }, 3400);
                                });
                            }
                        });

                        // Wait on transaction completion
                        self.socket.on("paymentComplete", (data) => {
                            self.populateModal(self.modal_success()).then(() => {
                                setTimeout(function(){
                                    self.removeModal().then(() => {
                                        resolve(); // payment succesful (send transaction)
                                    });
                                }, 2000);
                            });
                        });
                    });
            });
        },
        requestTransactions : function () {
            return new Promise ((resolve, reject) => {
                self.populateModal(self.modal_transactions())
                    .then(() => {
                        var loader = self.modal.find(".payment-loader");

                        loader.fadeIn(function (){
                            self.socket.emit("terminal:getTransactions");
                        });

                        self.socket.on("transactionData", (transactions) => {
                            if(transactions.length > 0){
                                var table = new Tabulator("#terminalTransactions", {
                                    layout: "fitColumns",
                                    resizableColumns: false,
                                    langs: GYM.tabulator_czech,
                                    layoutColumnsOnNewData:true,
                                    paginationSize: 5,
                                    paginationButtonCount:3,
                                    pagination: "local",
                                    columns: [
                                        {title: "#", field: "systemNumber"},
                                        {title: "Čas", field: "requestedOn"},
                                        {title: "Kč", field: "value"},
                                        {title: "", field: "buttons", formatter: "html"}
                                    ]
                                });
                                table.setLocale("cs-cs");

                                var t_len = transactions.length;
                                $.each(transactions, function (i, tran){

                                    console.log(tran, i);
                                    let cancelled = (tran.cancelled == "1") ? true : false; // cancelled?

                                    table.addData([{
                                        "systemNumber" : GYM._padInt(tran.systemNumber, 8),
                                        "requestedOn" : moment(tran.requestedOn).format("HH:MM"),
                                        "value" : (cancelled) ? "-" + (tran.value / 100) : (tran.value / 100),
                                        "buttons" : `<a class="btn btn-xs btn-danger${(cancelled || i > 0) ? " disabled" : ""}" onClick="TERMINALS.requestReversal('${GYM._b64encode(JSON.stringify(tran))}')"><i class="icon icon-trash"></i></a>
                                        <a class="btn btn-xs btn-primary" onClick="TERMINALS.requestReprint(${tran.systemNumber})">Tisk</a>`
                                    }], false);

                                    if(i+1 >= t_len){
                                        loader.fadeOut();
                                        console.log(table.getData());
                                    }
                                });
                            }else{
                                self.modal.find("tbody").append(`<tr><td colspan="3">Neprovedeny žádné transakce</td></tr>`);
                                loader.fadeOut();
                            }
                        });

                        self.socket.on("terminalError", (err) => {
                            self.populateModal(self.modal_error(err)).then(() => {
                                setTimeout(function(){
                                    self.removeModal().then(() => {
                                        reject(err); // Error, reject
                                    });
                                }, 3400);
                            });
                        });
                    });
            });
        },
        requestReprint : function (system_number) {
            return new Promise ((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.populateModal(self.modal_service_process("Tisk účtenky"))
                            .then(() => {
                                var loader = self.modal.find(".payment-loader"),
                                    status_container = self.modal.find('#paymentStatusMessages');

                                loader.fadeIn(function (){
                                    self.socket.emit("terminal:reprint", {system_number: system_number});
                                });

                                self.socket.on("reprintStatus", (status) => {
                                    // Switch up status texts
                                    var current = status_container.find("p");
                                        $(current).fadeOut(function(){
                                            $(current).text(status).fadeIn();
                                        });
                                });
                    
                                // Grab any potential errors
                                self.socket.on("reprintError", (data) => {
                                    if (!self.payment_error) {
                                        self.populateModal(self.modal_error(data)).then(() => {

                                            setTimeout(function(){
                                                self.removeModal().then(() => {
                                                    reject(data); // Error, reject
                                                });
                                            }, 3400);
                                        });
                                    }
                                });

                                // Wait on transaction completion
                                self.socket.on("reprintSuccesful", (data) => {
                                    self.populateModal(self.modal_success()).then(() => {
                                        setTimeout(function(){
                                            self.removeModal().then(() => {
                                                resolve(); // reprint instruction succesful
                                            });
                                        }, 2000);
                                    });
                                });
                            });
                    });
            });
        },
        requestHandshake : function () {
            return new Promise ((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.populateModal(self.modal_service_process("Test linky"))
                            .then(() => {
                                var loader = self.modal.find(".payment-loader"),
                                    status_container = self.modal.find('#paymentStatusMessages');

                                loader.fadeIn(function (){
                                    self.socket.emit("terminal:handshake");
                                });

                                self.socket.on("handshakeStatus", (status) => {
                                    // Switch up status texts
                                    var current = status_container.find("p");
                                        $(current).fadeOut(function(){
                                            $(current).text(status).fadeIn();
                                        });
                                });
                    
                                // Grab any potential errors
                                self.socket.on("handshakeError", (data) => {
                                    if (!self.payment_error) {
                                        self.populateModal(self.modal_error(data)).then(() => {

                                            setTimeout(function(){
                                                self.removeModal().then(() => {
                                                    reject(data); // Error, reject
                                                });
                                            }, 3400);
                                        });
                                    }
                                });

                                // Wait on transaction completion
                                self.socket.on("handshakeSuccesful", (data) => {
                                    self.populateModal(self.modal_success()).then(() => {
                                        setTimeout(function(){
                                            self.removeModal().then(() => {
                                                resolve(); // handshake instruction succesful
                                            });
                                        }, 2000);
                                    });
                                });
                            });
                    });
            });
        },
        requestRestart : function () {
            return new Promise ((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.populateModal(self.modal_service_process("Restart"))
                            .then(() => {
                                var loader = self.modal.find(".payment-loader"),
                                    status_container = self.modal.find('#paymentStatusMessages');

                                loader.fadeIn(function (){
                                    self.socket.emit("terminal:restartTerminal");
                                });
                    
                                // Grab any potential errors
                                self.socket.on("restartError", (data) => {
                                    if (!self.payment_error) {
                                        self.populateModal(self.modal_error(data)).then(() => {

                                            setTimeout(function(){
                                                self.removeModal().then(() => {
                                                    reject(data); // Error, reject
                                                });
                                            }, 3400);
                                        });
                                    }
                                });

                                // Wait on transaction completion
                                self.socket.on("restartSuccesful", (data) => {
                                    self.populateModal(self.modal_success()).then(() => {
                                        setTimeout(function(){
                                            self.removeModal().then(() => {
                                                resolve(); // restart succesful
                                            });
                                        }, 2000);
                                    });
                                });
                            });
                    });
            });
        },
        requestSettlement: function () {
            return new Promise ((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.populateModal(self.modal_service_process("Uzávěrka"))
                            .then(() => {
                                var loader = self.modal.find(".payment-loader"),
                                    status_container = self.modal.find('#paymentStatusMessages');

                                loader.fadeIn(function (){
                                    self.socket.emit("terminal:settlement");
                                });

                                // Grab the current status of the transaction
                                self.socket.on("settlementStatus", (status) => {
                                    // Switch up status texts
                                    var current = status_container.find("p");
                                        $(current).fadeOut(function(){
                                            $(current).text(status).fadeIn();
                                        });
                                });
                    
                                // Grab any potential errors
                                self.socket.on("settlementError", (data) => {
                                    if (!self.payment_error) {
                                        self.populateModal(self.modal_error(data)).then(() => {

                                            setTimeout(function(){
                                                self.removeModal().then(() => {
                                                    reject(data); // Error, reject
                                                });
                                            }, 3400);
                                        });
                                    }
                                });

                                // Wait on transaction completion
                                self.socket.on("settlementComplete", (data) => {
                                    self.populateModal(self.modal_success()).then(() => {
                                        setTimeout(function(){
                                            self.removeModal().then(() => {
                                                resolve(); // settlement succesful
                                            });
                                        }, 2000);
                                    });
                                });
                            });
                    });
            });
        },
        abortPayment: function () {
            if (!self.modal.find(".cancel-payment").hasClass("disabled")){
                self.socket.emit("terminal:abortPayment");
            }
        },
        requestPayment: function (value, cashback = false, refund = false){

            var display_value = value;
            if(cashback) display_value = parseFloat(value) + parseFloat(cashback);
            
            return new Promise((resolve, reject) => {
                self.openModal()
                    .then(() => {
                        self.populateModal(self.modal_payment_process(display_value))
                            .then(() => {
                                
                                    var loader = self.modal.find(".payment-loader"),
                                        variableSymbol = null, // to be assigned
                                        systemNumber = null, // tba
                                        status_container = self.modal.find('#paymentStatusMessages'),
                                        payment_processing = false,
                                        payment_finished = false;

                                    // Check status after 8 seconds
                                    setTimeout(function(){
                                        if(!payment_finished && !payment_processing){
                                            self.socket.emit("terminal:status", {connect: false});
                                        }
                                    }, 8000);
                                    // Check timeout after 30 seconds and display a potential exit button since this might be some bad timeout issue
                                    setTimeout(function(){
                                        if(!payment_finished && self.modal){
                                            let controls = self.modal.find("#paymentControls");
                                                controls.append('<a class="btn btn-warning back-to-checkout">Zpět na pokladnu</a>');
                                                controls.append('<span class="text-warning">Operace trvá déle než obvykle, pokud terminál nezobrazuje žádné informace tak se vraťte do pokladny a zkuste platbu opakovat, v opačném případě sledujte obrazovku terminálu pro další instrukce</span>');

                                            controls.find(".back-to-checkout").click(function(){
                                                self.verifyPayment()
                                                    .then(() => {
                                                        self.populateModal(self.modal_success()).then(() => {
                                                            self.socket.emit("terminal:approveDisconnectedPayment", {vSymbol: variableSymbol}); // reverse approve

                                                            self.removeModal().then(() => {
                                                                resolve({vSymbol: variableSymbol, terminalId: self.terminalId}); // payment succesful (send transaction)
                                                            });
                                                        });
                                                    })
                                                    .catch((err) => {

                                                        self.requestReversal({
                                                            value: value,
                                                            terminalId: self.terminalId,
                                                            gymCode: self.gymCode,
                                                            systemNumber: systemNumber,
                                                            variableSymbol: variableSymbol
                                                        })
                                                            .then(() => {
                                                                reject(err);
                                                            })
                                                            .catch(reversal_error => {
                                                                reject(reversal_error);
                                                            });
                                                    });
                                            });
                                        }
                                    }, 30000);

                                    loader.fadeIn(function () {

                                        if (!cashback && !refund) self.socket.emit("terminal:requestPayment", {amount: value, terminalId: self.terminalId, gymCode: self.gymCode});
                                        else if (refund && !cashback) self.socket.emit("terminal:requestPaymentRefund", {amount: value, terminalId: self.terminalId, gymCode: self.gymCode});
                                        else if (cashback && !refund) self.socket.emit("terminal:requestPaymentWithCashBack", {amount: value, cashback: cashback, terminalId: self.terminalId, gymCode: self.gymCode});

                                        self.socket.on("paymentVariableSymbol", (vs) => {
                                            variableSymbol = vs; // asign VS
                                        });

                                        self.socket.on("paymentSystemNumber", (sysnum) => {
                                            systemNumber = sysnum;
                                        });

                                        // Grab the current status of the transaction
                                        self.socket.on("paymentStatus", (status) => {
                                            // Switch up status texts
                                            var current = status_container.find("p");
                                                $(current).fadeOut(function(){
                                                    $(current).text(status).fadeIn();
                                                });
                                        });
                                        // Terminal status for timeouts eetc.
                                        self.socket.on("terminalStatus", (status) => {
                                            // Switch up status texts
                                            var current = status_container.find("p");
                                                $(current).fadeOut(function(){
                                                    $(current).text(status).fadeIn();
                                                });
                                        });
                            
                                        // Grab any potential errors
                                        // Todo: more error handling? (reporting? what? hm? eh?)
                                        self.socket.on("paymentError", (data) => {
                                            if (!self.payment_error) {
                                                self.populateModal(self.modal_error(data)).then(() => {
                                                    self.payment_error = true;
                                                    setTimeout(function(){
                                                        self.removeModal().then(() => {
                                                            reject(data); // Error, reject
                                                        });
                                                    }, 3400);
                                                });
                                            }
                                        });

                                        // Payment processing
                                        self.socket.on("paymentProcessing", (data) => {
                                            self.modal.find(".cancel-payment").addClass("disabled");
                                            payment_processing = true;
                                        });
        
                                        // Wait on transaction completion
                                        self.socket.on("paymentComplete", (data) => {
                                            payment_finished = true;

                                            self.populateModal(self.modal_success()).then(() => {
                                                setTimeout(function(){
                                                    self.removeModal().then(() => {
                                                        resolve({vSymbol: variableSymbol, terminalId: self.terminalId}); // payment succesful (send transaction)
                                                    });
                                                }, 2000);
                                            });
                                        });
                                    });

                            })
                            .catch(err => {});
                    })
                    .catch(err => {});
            });
        }
    }
}());