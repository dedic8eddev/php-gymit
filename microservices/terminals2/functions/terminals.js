const CFG = require('config'),
      net = require('net'),
      fs = require("fs"),
      CRC = require('crc-full').CRC,
      moment = require("moment"),
      CardSwipes = require( __basedir + "/models/card_swipes" ),
      CardSettlements = require( __basedir + "/models/card_settlements" );

const self = {
    // Top level vars
    client: null, // Socket
    STX: 0x02, // start char
    ETX: 0x03, // end char
    DLE: 0x10, // bytestuffer char
    current_trans_num: 1,
    current_info_num: 1,
    payment_processing: false, // Is the payment service in the process rn?
    terminal_error: false,
    stream: false,
    terminal_status: {
        "0" : "Volný",
        "1" : "Čeká se na kartu",
        "2" : "Čeká se na PIN",
        "3" : "Čeká se na banku",
        "4" : "Probíhá CVM",
        "5" : "Vstup klávesnicí",
        "6" : "Běžící",
        "7" : "Menu",
        "8" : "Po přečtení karty",
        "9" : "Karta vložena"
    },
    response_codes: { // Transaction response codes (oof..)
        00 : {result: true, desc: "Úspěšná transakce"},
        01 : {result: false, desc: "Neúspěšná transakce"},
        02 : {result: false, desc: "Terminál zaneprázdněn"},
        03 : {result: false, desc: "Incorrect acquirer"},
        04 : {result: false, desc: "Neznámá transakce"},
        05 : {result: false, desc: "Operace se nezdařila!"},
        06 : {result: false, desc: "Chyba požadavku"},
        07 : {result: false, desc: "Transakce zrušena"},
        09 : {result: false, desc: "Blokace fronty"},
        10 : {result: false, desc: "Blokovaná dávka, proveďte uzávěrku!"},
        11 : {result: true, desc: "Plná paměť, proveďte uzávěrku!"},
        12 : {result: false, desc: "Došel papír"},
        19 : {result: false, desc: "Zrušeno uživatelem"},
        20 : {result: false, desc: "Chyba čtení karty"},
        21 : {result: false, desc: "Karta nevložena"},
        22 : {result: false, desc: "Nepodporovaná karta"},
        23 : {result: false, desc: "Nesprávná karta"},
        24 : {result: false, desc: "Manuální zadání není povoleno"},
        25 : {result: false, desc: "Prošlá karta"},
        26 : {result: false, desc: "Neaktivní karta"},
        27 : {result: false, desc: "Špatné manuální zadání"},
        28 : {result: false, desc: "Špatná země"},
        29 : {result: false, desc: "Špatné tel. číslo"},
        30 : {result: false, desc: "Špatný operátor"},
        31 : {result: false, desc: "Špatná délka kódu"},
        32 : {result: false, desc: "Špatná měna"},
        40 : {result: false, desc: "Proveďte nejprve souhrn"},
        50 : {result: false, desc: "Neexistující VS platby"}, // Tohle se vrátí pokud vůbec není tran. v dávce ale dojde k požadavku na smazání/storno transakce
        51 : {result: false, desc: "Nesprávná částka"},
        52 : {result: false, desc: "Chybí celek souhrnu"},
        53 : {result: false, desc: "Nesprávný PIN"},
        54 : {result: false, desc: "PIN Limit vyčerpán"},
        55 : {result: false, desc: "Měnit částku není povoleno"},
        56 : {result: false, desc: "Transakce již vynulována"},
        57 : {result: false, desc: "Duplicitní sys. číslo"},
        58 : {result: false, desc: "Chybějící sys. číslo"},
        60 : {result: false, desc: "Částka příliš nízká pro cashback"},
        61 : {result: false, desc: "Cashback není povolen"},
        62 : {result: false, desc: "Maximum cashback přečerpán"},
        63 : {result: false, desc: "Cashback částka příliš nízká"},
        70 : {result: false, desc: "Vynulování nepovoleno"},
        71 : {result: false, desc: "Žádná transakce ke zrušení"},
        72 : {result: false, desc: "PID nesouhlasí"},
        80 : {result: false, desc: "Produkty nejsou povoleny"},
        81 : {result: false, desc: "Nepodporovaný SAM modul"},
        82 : {result: true, desc: "Transakce proběhla, ale EET selhalo"}
    },
    getTodayYmd: () => {
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1; //months from 1-12
        var day = dateObj.getUTCDate();
        var year = dateObj.getUTCFullYear();
        
        newdate = year + "" + month + "" + day;
        return newdate;
    },
    startLogging: () => {
        if (!self.stream) {
            console.log("Starting log..");
            self.stream = fs.createWriteStream(__basedir + `/logs/communication_log_${self.getTodayYmd()}.txt`, {flags:'a'});
        }
    },
    log: (data, direction) => {
        if(self.stream){
            console.log("Logging..");
            let str = `${moment().format("DD.MM.YY - HH:mm")} | ${(direction == "request") ? "-->" : "<--"} ${data}\n`;
            self.stream.write(str);
        }else{
            console.log("Cannot log, no logger initialized.");
        }
    },
    stopLogging: () => {
        if (self.stream) self.stream.end(`\n\n`, () => {
            self.stream = false;
            console.log("Closed log..");
        });
    },
    // Simple HEX to ASCII
    hex2string: (hex) => {
        var str = '';
        for (var i = 0; (i < hex.length && hex.substr(i, 2) !== '00'); i += 2)
            str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
        return str;
    },
    string2hex: (str) => {
        var result = '';
        for (var i=0; i<str.length; i++) {
          result += str.charCodeAt(i).toString(16);
        }
        return result;
    },
    padInt: (n, width, z) => {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    },
    /**
     * Stuff DLE characters inbefore any other control chars in the data regions
     */
    stuffDataBytes: (buffer) => {
        // TODO: This is probably not ideal
        let new_buffer = [];
        for (let i = 0; i < buffer.length; i++){
            if ( buffer[i] == self.DLE || buffer[i] == self.STX || buffer[i] == self.ETX){
                new_buffer.push(self.DLE, self.DLE, buffer[i]);
            }else{
                new_buffer.push(buffer[i]);
            }
        }

        return Buffer.from(new_buffer);
    },
    /**
     * Count a CRC of a buffer
     */
    countCRC: (buffer) => {
        const crc_model = new CRC("CRC16", 16, 0x8005, 0x0000, 0x0000, true, true);

        let temp_crc = crc_model.compute(buffer);
            temp_crc = temp_crc.toString(16);
        
        if(temp_crc.length < 4) temp_crc = "0"+temp_crc;
        let crc = Buffer.from( temp_crc.match(/[a-fA-F0-9]{2}/g).reverse().join('') , "hex");

        return crc;
    },
    /**
     * Establish a connection and release a promise on succesful connection (0x05)
     */
    connectTerminal: () => {
        return new Promise ((resolve, reject) => {
            self.client = new net.Socket();
            console.log("Connecting to terminal");

            let r = (res) => {
                self.log(res.toString("hex"), "response");
                
                    if (res.toString("hex") == "05") {
                        console.log("Connected to terminal");
                        self.client.setNoDelay(true);
                        self.client.removeListener("connect", r);
                        resolve();
                    }
                };

                self.client.connect(CFG.get("terminals.port"), CFG.get("terminals.host"));

                self.client.on("error", (err) => {
                    console.log(err);
                    reject("Chyba v připojení k terminálu.")
                });

                self.client.on("data", r);
        });
    },
    // Close the terminal connection
    disconnectTerminal: () => {
        return new Promise ((resolve, reject) => {
            // Reset
            self.current_info_num = 1;
            self.current_trans_num = 1;
            self.payment_processing = false;

            if (!self.client.destroyed) {
                try {
                    self.client.destroy();
                    self.client = null;
                    resolve();
                } catch (err) {
                    reject(err);
                }
            }
        });
    },
    // Get terminals transactions
    getTransactions: () => {
        return new Promise ((resolve, reject) => {
            let today_from = new Date(),
                today_to = new Date();

            today_from.setHours(0,0,0,0);
            today_to.setHours(23,59,59,999);

            CardSettlements.findOne({}).sort({requestedOn: -1}).exec((err, settlement) => {
                if(settlement) today_from = settlement.requestedOn;
                if(err) console.log(err);
            
                CardSwipes.find({requestedOn: {$gte: today_from, $lte: today_to}}).sort({requestedOn: -1}).exec((err, transactions) => {
                    if (err) reject("Nepovedlo se načíst dnešní transakce!");
                    else {
                        resolve(transactions);
                    }
                });
            });
        });
    },
    runHandshake: (socket) => {
        self.startLogging();
        
        return new Promise ( (resolve, reject) => {
            socket.emit("handshakeStatus", "Odesílání požadavku");
            let num = self.current_trans_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x30,0x36], // data header
                data = Buffer.from([0x30, 0x30]),
                crc; // CRC

            let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // stuff bytes

            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);

            self.log(final_payload.toString("hex"), "request");
            self.connectTerminal().then(() => {
                self.client.write(final_payload, () => {
                    socket.emit("handshakeStatus", "Kontakování autorizačního centra");
                });

                self.client.on("data", res => {
                    self.log(res.toString("hex"), "response");

                    let res_len = res.lenght;

                    if(res_len > 2){
                        console.log("What?", res.toString("hex"));

                        self.client.write(Buffer.from([0x06, res[1]]), () => {
                            self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                            self.disconnectTerminal().catch(err => console.log(err));
                        });
                    }else{
                        if(res[0] == 4) resolve(); // Apparently this just returns a 04 after printing the status information on a receipt?
                        if(res[0] == 6) socket.emit("handshakeStatus", "Požadavek přijat"); // OK
                        if(res.toString("hex") == 15) reject("Požadavek nebyl přijat"); // bye
                    }
                });
            });
        });
    },
    runSettlement: (socket) => {
        self.startLogging();
        return new Promise ( (resolve, reject) => {
            socket.emit("settlementStatus", "Odesílání požadavku");

            let num = self.current_trans_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x32,0x31], // data header
                data = Buffer.from([0x30, 0x31, 0x46, 0x1C, 0x30, 0x30, 0x30, 0x30, 0x30, 0x31]),
                crc; // CRC

            let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // stuff bytes

            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);

            self.log(final_payload.toString("hex"), "request");
            let response_count = 0;
            self.connectTerminal().then(() => {
                console.log("Performing settlement");
                self.client.write(final_payload);
                self.client.on("data", (res) => {
                    response_count++;
                    self.log(res.toString("hex"), "response");

                    let res_len = res.length;
                    if(res_len > 2){
                        let status = res.slice(20, 22).toString("hex"),
                            batch_num = res.slice(22, 30).toString("hex"),
                            pid = res.slice(30, 38).toString("hex");
                        
                        let res_response_code = parseInt(self.hex2string(status)),
                            response_type = self.response_codes[res_response_code];

                        if (response_type.result == true) {
                            socket.emit("settlementStatus", "Probíhá tisk");

                            let document = new CardSettlements({
                                batchNum: batch_num,
                                pid: pid,
                                requestStatus: true,
                                responseLog: [res]
                            });

                            document.save( (err, log) => {
                                if(!err){
                                    socket.emit("settlementStatus", "Vytvořena záloha");
                                    console.log("Saved settlement log to Mongo!");
                                }else{
                                    console.log("Failed saving settlement log to Mongo, ", err.message);
                                }

                                resolve(response_type.desc);
                            });

                        }else{
                            reject(response_type.desc);
                        }

                        self.client.write(Buffer.from([0x06, res[1]]), () => {
                            self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                            self.disconnectTerminal().catch(err => console.log(err));
                            self.stopLogging();
                        });

                    }else{
                        if(res[0] == 4 && response_count <= 1) {
                            self.stopLogging();
                            reject("Spojení zrušeno"); // If this is a response to our last trans request then terminate
                        }
                        if(res[0] == 6) socket.emit("settlementStatus", "Požadavek přijat"); // OK
                        if(res.toString("hex") == 15) {
                            self.stopLogging();
                            reject("Požadavek nebyl přijat"); // bye
                        }
                    }
                });
            }).catch(err => {
                reject(err);
            });
        });
    },
    /**
     * Get current terminal status,
     * this can be called during transactions too
     */
    getTerminalStatus: (connect = false, socket = false) => {
        self.startLogging();
        return new Promise((resolve, reject) => {
            let num = self.current_info_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_info_num, 4)), // Message counter
                message_header = [0x30, 0x33, 0x30, 0x30, 0x30, 0x30], // message header (INFORMATION REQUEST 03!!)
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x32,0x30], // data header
                data = Buffer.from([]),
                crc; // CRC

            let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // stuff bytes  
        
            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);

            if(connect){
                self.log(final_payload.toString("hex"), "request");
                self.connectTerminal().then(() => {
                    self.client.write(final_payload);

                    self.client.on("data", (res) => {
                        let res_len = res.length;
                        self.log(res.toString("hex"), "response");
    
                        if(res_len > 2){
                            // Status messages return
                            let response_type = parseInt(res.slice(3,4).toString("hex")); // message type
                            switch (response_type) {
                                case 34:
                                    let status = res[21].toString();
                                        self.client.write(Buffer.from([0x06, res[1]]), () => {
                                            self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                                            self.disconnectTerminal().catch(err => { console.log(err); });
                                            resolve(status);
                                        });
                                break;
                            }
                        }else{
                            // Status messages
                            if(res[0] == 4) {
                                self.stopLogging();
                                reject("Spojení zrušeno");
                            }
                            if(res.toString("hex") == 15) {
                                self.stopLogging();
                                reject("Požadavek nepřijat");
                            }
                        }
                    });
                }).catch(err => {
                    console.log("Couldnt connect: ", err);
                    reject("Nelze navázat spojení");
                });
            }else{
                if (self.client != null && self.client.writable && !self.payment_processing) {
                    self.log(final_payload.toString("hex"), "request");
                    self.client.write(final_payload);
                    stream.write("INFO REQUEST: " + final_payload.toString("hex") + "\n" + "\n");
                    self.current_info_num++;
                    resolve();
                }
            }
        });
    },
    /**
     * Abort the card swipe/service request
     */
    abortPayment: (socket = false) => {
        if(!self.payment_processing){
            if(socket) socket.emit("paymentStatus", "Rušení platební transakce");

            let num = self.current_trans_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x37,0x37], // data header
                data = Buffer.from([]), // message data
                crc; // CRC

            let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // Preceed data region bytes with DLE if needed

            // Generate CRC from the needed parts of the message
            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);

            self.log(final_payload.toString("hex"), "request");
            self.client.write(final_payload, () => {
                console.log("Sent!");
                self.current_trans_num++;

                if(socket) socket.emit("paymentError", "Transakce zrušena");
            });
        }else{
            // TODO: get out
            if(socket) socket.emit("paymentStatus", "Transakce již nemůže být zrušena");
        }
    },
    // Approve a payment later on, for instances where the connection to the terminal fails from the API, but payment goes through
    // This is used in a human check factor prompt
    approvePayment: (variableSymbol) => {
        return new Promise((resolve, reject) => {
            CardSwipes.updateOne({variableSymbol: variableSymbol}, {cancelled: false, requestStatus: true}).exec((err, res) => {
                if(err) {
                    reject("Failed updating cardswipe document.");
                } else {
                    resolve();
                }
            });
        });
    },
    requestReversePayment: (value, gymCode, terminalId, sysNumber, variableSymbol, socket = false) => {
        self.startLogging();

        return new Promise((resolve, reject) => {
            self.terminal_error = false;

            // Reference number to distinguish trans type
            ref_number = "0x"+self.string2hex("X");
            if(socket) socket.emit("paymentStatus", "Kontaktování API");

                value = value*100; // hundreds
            let byte_value = Buffer.from(value.toString()), // bytes
                system_number = Buffer.from(self.padInt(sysNumber, 8));

                let num = self.current_trans_num, // num (cac/sac) 
                    msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                    message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                    header = [0x30,0x39,0x30,0x30,0x31,0x31,0x37,0x36], // data header
                    trans_number = Buffer.from(variableSymbol),
                    data = Buffer.concat([Buffer.from([ref_number, 0x1C, 0x1C, 0x30, 0x31]), byte_value, Buffer.from([0x1C]), system_number, Buffer.from([0x1C, 0x30, 0x31, 0x1C, 0x30, 0x31, 0x1C]), trans_number]), // message data
                    crc;
    
                let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // Preceed data region bytes with DLE if needed
    
                // Generate CRC from the needed parts of the message
                crc = self.countCRC(Buffer.concat([
                    Buffer.from([num]),
                    Buffer.from(message_header),
                    msg_counter,
                    stuffed_data,
                    Buffer.from([self.ETX])
                ]));

                // Construct final payload
                let final_payload = Buffer.concat([
                    Buffer.from([self.STX]), // STX
                    Buffer.from([num]), // Message num (iterator)
                    Buffer.from(message_header), // Message header
                    msg_counter,
                    stuffed_data, // Data header+body
                    Buffer.from([self.ETX]), // ETX
                    crc // CRC1+CRC2
                ]);
                
                console.log("Request payload: ", final_payload.toString("hex")); // debug
                self.log(final_payload.toString("hex"), "request");

                // Connect to terminal and then write payload ->
                self.connectTerminal().then(() => {
                    if(socket) socket.emit("paymentStatus", "Připojeno k terminálu");
                    self.client.write(final_payload, () => {
                        if(socket) socket.emit("paymentStatus", "Kontaktování platebního centra");
                        console.log("Sent card service request !");
                        self.current_trans_num++;
                    });

                    // Monitor responses
                    let response_count = 0;

                    let responses = []; // collect all the responses
                    self.client.on("data", (res) => {
                        self.log(res.toString("hex"), "response");

                        let res_len = res.length;

                        if(res_len <= 2){
                            // Status messages
                            if(res[0] == 4) {
                                let confirmed_num = res[1];
                                    if(confirmed_num == self.current_trans_num) {
                                        self.stopLogging();
                                        reject("Spojení zrušeno");
                                    }
                            }
                            if(res[0] == 6 && socket) socket.emit("paymentStatus", "Požadavek přijat"); // OK
                            if(res.toString("hex") == 15) {
                                self.stopLogging();
                                reject("Požadavek nebyl přijat"); // bye
                            }
                        }
                        else{

                            let response_type = parseInt(res.slice(3,4).toString("hex")); // message type
                            switch (response_type) {
                                case 32:
                                    responses.push(res);
                                    response_count++; // count actual transaction responses

                                    // Transaction response
                                    if(response_count == 1 && !self.terminal_error){
                                        let response = responses[response_count-1];

                                        console.log("Confirming", Buffer.from([0x06, response[1]]));
                                        self.client.write(Buffer.from([0x06, response[1]]));
                                        self.log(Buffer.from([0x06, response[1]]).toString("hex"), "request");

                                        let res_response_code = parseInt(self.hex2string(response.slice(20,22).toString("hex"))),
                                            response_type = self.response_codes[res_response_code];
            
                                            console.log("RESPONSE_CODE", res_response_code);

                                        if (response_type.result == true) {
                                            self.payment_processing = true;
                                            // Payment went
                                            console.log("SUCCESS:", response_type.desc);
                                            if(socket) socket.emit("paymentStatus", response_type.desc);
                                            if(socket) socket.emit("paymentProcessing", true);

                                            if(socket) socket.emit("paymentStatus", "Transakce uložena");

                                            CardSwipes.updateOne({systemNumber: sysNumber, variableSymbol: variableSymbol}, {cancelled: true}).exec((err, res) => {
                                                if(err) console.log("Failed setting transaction as cancelled! (VS: "+variableSymbol+") ");
                                            
                                                self.disconnectTerminal().catch(err => { console.log(err); });
                                                self.stopLogging();
                                                resolve();
                                            });
                                        }else {
                                            console.log("ERROR:", response_type.desc);
                                            self.terminal_error = true;

                                            self.disconnectTerminal().catch(err => { console.log(err); });
                                            self.stopLogging();
                                            reject(response_type.desc);
                                        }
                                    }

                                break;

                                case 34:
                                    // Information response
                                    let status = res.slice(20,21).toString();

                                        if (!self.terminal_error) {
                                            self.client.write(Buffer.from([0x06, res[1]]));
                                            self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                                        }
                                        console.log("CURRENT STATUS:", self.terminal_status[status]);
                                        if(socket) socket.emit("terminalStatus", self.terminal_status[status]);
                                break;
                            }
                        } 
                    });
                }).catch(err => {
                    console.log(err);
                    self.stopLogging();
                    reject("Terminál nepřipojen");
                });
        });
    },
    requestPaymentWithCashback: (value, cashback, gymCode, terminalId, socket = false, ref_number = "P") => {
        self.startLogging();

        return new Promise((resolve, reject) => {
            self.terminal_error = false;

            // Reference number to distinguish trans type
            // + cashback setup in bytes for the instruction
            ref_number = "0x"+self.string2hex(ref_number);

                cashback = cashback*100;
            let cashback_bytes = Buffer.concat([Buffer.from([0x1C, 0x1C, 0x1C]), Buffer.from(cashback.toString())]).toString("hex");   

            if(socket) socket.emit("paymentStatus", "Kontaktování API");

                value = value*100; // hundreds
            let byte_value = Buffer.from(value.toString()), // bytes
                system_number = 1;

            // Get sys number by DB
            let today = new Date();
                today.setHours(0,0,0,0);

            CardSwipes.findOne({"requestedOn": {"$gte":today}}).sort("-requestedOn").exec((err, doc) => {
                if(socket) socket.emit("paymentStatus", "Generování systémového čísla");

                if(err) reject("Chyba systémového čísla");
                else if (doc) system_number = Buffer.from(self.padInt( (parseInt(doc.systemNumber)+1), 8 ));
                else system_number = Buffer.from(self.padInt(system_number, 8)); // First?

                let create_log = new CardSwipes({
                    "gymCode":gymCode,
                    "value":value,
                    "terminalId":terminalId,
                    "systemNumber":system_number
                });

                create_log.save((err, payment_log) => {
                    if (err) reject("Nelze se spojit s databází!");
                    else {
                        if(socket) socket.emit("paymentVariableSymbol", payment_log.variableSymbol); // announce new VS

                        let num = self.current_trans_num, // num (cac/sac) 
                            msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                            message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                            header = [0x30,0x39,0x30,0x30,0x31,0x31,0x37,0x36], // data header
                            trans_number = Buffer.from(payment_log.variableSymbol),
                            data = Buffer.concat([Buffer.from([ref_number, 0x1C, 0x1C, 0x30, 0x31]), byte_value, Buffer.from([0x1C]), system_number, Buffer.from([0x1C, 0x30, 0x31, 0x1C, 0x30, 0x31, 0x1C]), trans_number, Buffer.from(cashback_bytes, "hex")]), // message data
                            crc;
            
                        let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // Preceed data region bytes with DLE if needed
            
                        // Generate CRC from the needed parts of the message
                        crc = self.countCRC(Buffer.concat([
                            Buffer.from([num]),
                            Buffer.from(message_header),
                            msg_counter,
                            stuffed_data,
                            Buffer.from([self.ETX])
                        ]));

                        // Construct final payload
                        let final_payload = Buffer.concat([
                            Buffer.from([self.STX]), // STX
                            Buffer.from([num]), // Message num (iterator)
                            Buffer.from(message_header), // Message header
                            msg_counter,
                            stuffed_data, // Data header+body
                            Buffer.from([self.ETX]), // ETX
                            crc // CRC1+CRC2
                        ]);
                        
                        console.log("Request payload: ", final_payload.toString("hex")); // debug
                        self.log(final_payload.toString("hex"), "request");
    
                        // Connect to terminal and then write payload ->
                        self.connectTerminal().then(() => {
                            if(socket) socket.emit("paymentStatus", "Připojeno k terminálu");
                            self.client.write(final_payload, () => {
                                if(socket) socket.emit("paymentStatus", "Kontaktování platebního centra");
                                console.log("Sent card service request !");
                                self.current_trans_num++;
                            });
    
                            // Monitor responses
                            let response_count = 0;
    
                            let responses = []; // collect all the responses
                            self.client.on("data", (res) => {
                                console.log("RESPONSE: ", res.toString("hex"));
                                self.log(res.toString("hex"), "response");
    
                                let res_len = res.length;
    
                                if(res_len <= 2){
                                    // Status messages
                                    if(res[0] == 4) {
                                        let confirmed_num = res[1];
                                            if(confirmed_num == self.current_trans_num) {
                                                CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                                    if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                                    self.stopLogging();
                                                    reject("Spojení zrušeno"); // If this is a response to our last trans request then terminate
                                                });
                                            }
                                    }
                                    if(res[0] == 6 && socket) socket.emit("paymentStatus", "Požadavek přijat"); // OK
                                    if(res.toString("hex") == 15) {
                                        CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                            if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                            self.stopLogging();
                                            reject("Požadavek nebyl přijat"); // bye
                                        });
                                    }
                                }
                                else{
    
                                    let response_type = parseInt(res.slice(3,4).toString("hex")); // message type
                                    switch (response_type) {
                                        case 32:
                                            responses.push(res);
                                            response_count++; // count actual transaction responses
    
                                            // Transaction response
                                            if(response_count == 1 && !self.terminal_error){
                                                let response = responses[response_count-1];
    
                                                console.log("Confirming", Buffer.from([0x06, response[1]]));
                                                self.client.write(Buffer.from([0x06, response[1]]));
                                                self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
    
                                                let res_response_code = parseInt(self.hex2string(response.slice(20,22).toString("hex"))),
                                                    response_type = self.response_codes[res_response_code];
                    
                                                    console.log("RESPONSE_CODE", res_response_code);
    
                                                if (response_type.result == true) {
                                                    self.payment_processing = true;
                                                    // Payment went
                                                    console.log("SUCCESS:", response_type.desc);
                                                    if(socket) socket.emit("paymentStatus", response_type.desc);
                                                    if(socket) socket.emit("paymentProcessing", true);

                                                    CardSwipes.updateOne({_id: payment_log._id}, {requestStatus: true, responseLog: responses}, (err, res) => {
                                                        if (err) console.log("Failed updating CC payment log with _id "+payment_log._id+"!");

                                                        self.disconnectTerminal().catch(err => { console.log(err); });
                                                        self.stopLogging();
                                                        if(socket) socket.emit("paymentStatus", "Transakce uložena");
                                                        resolve();
                                                    });
                                                }else {
                                                    console.log("ERROR:", response_type.desc);
                                                    self.terminal_error = true;
    
                                                    CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                                        if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                                        
                                                        self.disconnectTerminal().catch(err => { console.log(err); });
                                                        self.stopLogging();
                                                        reject(response_type.desc);
                                                    });
                                                }
                                            }
    
                                        break;
    
                                        case 34:
                                            // Information response
                                            let status = res.slice(20,21).toString();
    
                                                if (!self.terminal_error) {
                                                    self.client.write(Buffer.from([0x06, res[1]]));
                                                    self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                                                }
                                                console.log("CURRENT STATUS:", self.terminal_status[status]);
                                                if(socket) socket.emit("terminalStatus", self.terminal_status[status]);
                                        break;
                                    }
                                } 
                            });
                        }).catch(err => {
                            console.log(err);

                            CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                reject("Terminál nepřipojen");
                            });

                            self.stopLogging();
                        });
                    }
                });

            });
        });
    },
    /**
     * Card swipe request
     * accepts value in standard format with max 2 decimal places, eg. 250.90 or just 251
     * ref_number == Reference number == P (sale/prodej), R (refund/návrat)
     */
    requestPayment: (value, gymCode, terminalId, socket = false, ref_number = 'P', cashback = false) => {
        self.startLogging();

        return new Promise( (resolve, reject) => {
            self.terminal_error = false;
            value = value*100; // hundreds

            // Reference number to distinguish trans type
            // + cashback setup in bytes for the instruction
            ref_number = "0x"+self.string2hex(ref_number);

            let cashback_bytes_swipe = "",
                cashback_bytes_service = "";
            if (cashback !== false) {
                cashback = cashback*100;
                cashback_bytes_swipe = Buffer.concat([Buffer.from([0x1C, 0x1C]), Buffer.from(cashback.toString()), Buffer.from([0x1C])]).toString("hex");
                //cashback_bytes_service = Buffer.concat([Buffer.from([0x1C, 0x1C, 0x1C]), Buffer.from(cashback.toString())]).toString("hex");
                
            }

            if(socket) socket.emit("paymentStatus", "Kontaktování API");

            let byte_value = Buffer.from(value.toString()), // bytes
                system_number = 1;

            // Get sys number by DB
            let today = new Date();
                today.setHours(0,0,0,0);

            CardSwipes.findOne({"requestedOn": {"$gte":today}}).sort("-requestedOn").exec((err, doc) => {
                if(socket) socket.emit("paymentStatus", "Generování systémového čísla");

                if(err) reject("Chyba systémového čísla");
                else if (doc) system_number = Buffer.from(self.padInt( (parseInt(doc.systemNumber)+1), 8 ));
                else system_number = Buffer.from(self.padInt(system_number, 8)); // First?


                let create_log = new CardSwipes({
                    "gymCode":gymCode,
                    "value":value,
                    "terminalId":terminalId,
                    "systemNumber":system_number,
                    "requestType":ref_number,
                    "cancelled":((ref_number == "R") ? true : false )
                });

                create_log.save((err, payment_log) => {
                    if (err) reject("Nelze se spojit s databází!");
                    else {
                        if(socket) socket.emit("paymentVariableSymbol", payment_log.variableSymbol); // announce new VS
                        if(socket) socket.emit("paymentSystemNumber", payment_log.systemNumber); // announce new SYSNUM

                        let num = self.current_trans_num, // num (cac/sac) 
                            msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                            message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                            header = [0x30,0x39,0x30,0x30,0x31,0x31,0x37,0x35], // data header
                            data = Buffer.concat([Buffer.from([ref_number,0x30,0x31]), byte_value, Buffer.from([0x1C,0x30,0x31]), Buffer.from(cashback_bytes_swipe, "hex")]), // message data
                            crc, // CRC
                            trans_number = Buffer.from(payment_log.variableSymbol); // variable number / auth code
    
                        let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // Preceed data region bytes with DLE if needed
    
                        // Generate CRC from the needed parts of the message
                        crc = self.countCRC(Buffer.concat([
                            Buffer.from([num]),
                            Buffer.from(message_header),
                            msg_counter,
                            stuffed_data,
                            Buffer.from([self.ETX])
                        ]));
    
                        // Construct final payload
                        let final_payload = Buffer.concat([
                            Buffer.from([self.STX]), // STX
                            Buffer.from([num]), // Message num (iterator)
                            Buffer.from(message_header), // Message header
                            msg_counter,
                            stuffed_data, // Data header+body
                            Buffer.from([self.ETX]), // ETX
                            crc // CRC1+CRC2
                        ]);
                        
                        console.log("Request payload: ", final_payload.toString("hex")); // debug
                        self.log(final_payload.toString("hex"), "request");
    
                        // Connect to terminal and then write payload ->
                        self.connectTerminal().then(() => {
                            if(socket) socket.emit("paymentStatus", "Připojeno k terminálu");
                            self.client.write(final_payload, () => {
                                console.log("Sent!");
                                if(socket) socket.emit("paymentStatus", "Požadavek odeslán");
                                self.current_trans_num++;
                            });
    
                            // Monitor responses
                            let response_count = 0;
    
                            let responses = []; // collect all the responses
                            self.client.on("data", (res) => {
                                self.log(res.toString("hex"), "response");
    
                                let res_len = res.length;
    
                                if(res_len <= 2){
                                    // Status messages
                                    if(res[0] == 4) {
                                        let confirmed_num = res[1];
                                            if(confirmed_num == self.current_trans_num) {
                                                CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                                    if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                                    self.stopLogging();
                                                    reject("Spojení zrušeno"); // If this is a response to our last trans request then terminate
                                                });
                                            }
                                    }
                                    if(res[0] == 6 && socket) socket.emit("paymentStatus", "Požadavek přijat"); // OK
                                    if(res.toString("hex") == 15) {
                                        CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                            if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                            self.stopLogging();
                                            reject("Požadavek nebyl přijat"); // bye
                                        });
                                    }
                                }
                                else{
    
                                    let response_type = parseInt(res.slice(3,4).toString("hex")); // message type
                                    switch (response_type) {
                                        case 32:
                                            responses.push(res);
                                            response_count++; // count actual transaction responses
    
                                            // Transaction response
                                            if(response_count == 1 && !self.terminal_error){
                                                let response = responses[response_count-1];
    
                                                console.log("Confirming", Buffer.from([0x06, response[1]]));
                                                self.log(Buffer.from([0x06, response[1]]).toString("hex"), "request");

                                                self.client.write(Buffer.from([0x06, response[1]])); // ACK back
    
                                                // First response
                                                let res_response_code = parseInt(self.hex2string(response.slice(20,22).toString("hex")));
                                                console.log("RESPONSE_CODE", res_response_code);
                                                let response_type = self.response_codes[res_response_code];
                                                
                                                if (response_type.result == true) {
                                                    // First response is positive => we can ask for the payment service
                    
                                                    // Overwrite original data
                                                    num = self.current_trans_num, // num (cac/sac) 
                                                    msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                                                    message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                                                    header = [0x30,0x39,0x30,0x30,0x31,0x31,0x37,0x36], // data header
                                                    data = Buffer.concat([Buffer.from([ref_number, 0x1C, 0x1C, 0x30, 0x31]), byte_value, Buffer.from([0x1C]), system_number, Buffer.from([0x1C, 0x30, 0x31, 0x1C, 0x30, 0x31, 0x1C]), trans_number, Buffer.from(cashback_bytes_service, "hex")]); // message data
                                        
                                                    stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])), // Preceed data region bytes with DLE if needed
                                        
                                                    // Generate CRC from the needed parts of the message
                                                    crc = self.countCRC(Buffer.concat([
                                                        Buffer.from([num]),
                                                        Buffer.from(message_header),
                                                        msg_counter,
                                                        stuffed_data,
                                                        Buffer.from([self.ETX])
                                                    ]));
                    
                                                    // Construct final payload
                                                    final_payload = Buffer.concat([
                                                        Buffer.from([self.STX]), // STX
                                                        Buffer.from([num]), // Message num (iterator)
                                                        Buffer.from(message_header), // Message header
                                                        msg_counter,
                                                        stuffed_data, // Data header+body
                                                        Buffer.from([self.ETX]), // ETX
                                                        crc // CRC1+CRC2
                                                    ]);
                    
                                                    console.log("Request payload: ", final_payload.toString("hex")); // debug
                                                        self.log(final_payload.toString("hex"), "request");

                                                        self.client.write(final_payload, () => { 
                                                            if(socket) socket.emit("paymentStatus", "Kontaktování platebního centra");
                                                            console.log("Sent card service request !");
                                                            self.current_trans_num++;
                                                    }); 
                    
                                                }else{
                                                    console.log("ERROR:", response_type.desc);
                                                    self.terminal_error = true;
    

                                                    CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                                        if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                                        
                                                        self.disconnectTerminal().catch(err => { console.log(err); });
                                                        self.stopLogging();
                                                        reject(response_type.desc);
                                                    }); 
                                                }
                                            }
                    
                                            if (response_count == 2 && !self.terminal_error){
                                                let response = responses[response_count-1];
    
                                                console.log("Confirming", Buffer.from([0x06, response[1]]));
                                                self.log(Buffer.from([0x06, response[1]]).toString("hex"), "request");
                                                self.client.write(Buffer.from([0x06, response[1]]));
    
                                                let res_response_code = parseInt(self.hex2string(response.slice(20,22).toString("hex"))),
                                                    response_type = self.response_codes[res_response_code];
                    
                                                    console.log("RESPONSE_CODE", res_response_code);
    
                                                if (response_type.result == true) {
                                                    self.payment_processing = true;
                                                    // Payment went
                                                    console.log("SUCCESS:", response_type.desc);
                                                    if(socket) socket.emit("paymentStatus", response_type.desc);
                                                    if(socket) socket.emit("paymentProcessing", true);

                                                    CardSwipes.updateOne({_id: payment_log._id}, {requestStatus: true, responseLog: responses}, (err, res) => {
                                                        if (err) console.log("Failed updating CC payment log with _id "+payment_log._id+"!");

                                                        if(socket) socket.emit("paymentStatus", "Transakce uložena");

                                                        self.disconnectTerminal().catch(err => { console.log(err); });
                                                        self.stopLogging();
                                                        resolve();
                                                    });
                                                }else {
                                                    console.log("ERROR:", response_type.desc);
                                                    self.terminal_error = true;
    
                                                    CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                                        if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                                                        
                                                        self.disconnectTerminal().catch(err => { console.log(err); });
                                                        self.stopLogging();
                                                        reject(response_type.desc);
                                                    });
                                                }
                                            }
                    
                                            //if (response_count > 2 && !terminal_error){
                                            //    console.log("Confirming", Buffer.from([0x06, res[1]]));
                                            //    stream.write("CONFIRMATION REQUEST: " + Buffer.from([0x06, res[1]]).toString("hex") + "\n" + "\n");
                                            //    self.client.write(Buffer.from([0x06, res[1]]));
                                            //}
    
                                        break;
    
                                        case 34:
                                            // Information response
                                            let status = res.slice(20,21).toString();
    
                                                if (!self.terminal_error) {
                                                    self.client.write(Buffer.from([0x06, res[1]]));
                                                    self.log(Buffer.from([0x06, res[1]]).toString("hex"), "request");
                                                }
                                                console.log("CURRENT STATUS:", self.terminal_status[status]);
                                                if(socket) socket.emit("terminalStatus", self.terminal_status[status]);
                                        break;
                                    }
                                } 
                            });
                        }).catch(err => {
                            console.log(err);

                            CardSwipes.deleteOne({_id: payment_log._id}, (err) => {
                                if(err) console.log("Failed deleting CC payment log with _id "+payment_log._id+"!");
                            });

                            reject("Terminál nepřipojen");
                            self.stopLogging();
                        });
                    }
                });

            });

        });
    },
    /**
     * Reprint the selected transaction receipt
     * Expects a system number of the given transaction (from Mongo)
     */
    reprintReceipt: (system_number, socket) => {
        self.startLogging();

        return new Promise ((resolve, reject) => {
            socket.emit("reprintStatus", "Odesílání požadavku");

            let num = self.current_trans_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x34,0x32], // mandatory header
                data = Buffer.concat([Buffer.from(self.padInt(system_number, 8)), Buffer.from([0x30,0x31])]), // message data
                crc; // CRC

            let stuffed_data = self.stuffDataBytes(Buffer.concat([Buffer.from(header), data])); // Preceed data region bytes with DLE if needed

            // Generate CRC from the needed parts of the message
            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);

            self.log(final_payload.toString("hex"), "request");
            self.connectTerminal().then(() => {
                socket.emit("reprintStatus", "Terminál připojen");

                self.client.write(final_payload, () => {
                    console.log("Sent!");
                    self.current_trans_num++;
                });

                self.client.on("data", (res) => {
                    self.log(res.toString("hex"), "response");

                    let res_len = res.length;

                    if(res_len <= 2){
                        // Status messages
                        if(res[0] == 4) { 
                            self.stopLogging();
                            reject("Spojení přerušeno"); // not ok
                        }
                        if(res[0] == 6) console.log("Požadavek přijat"); // OK
                        if(res.toString("hex") == 15) { 
                            self.stopLogging();
                            reject("Požadavek nebyl nepřijat"); // not ok
                        }
                    }else{
                        let code = parseInt(self.hex2string(res.slice(20,22).toString("hex")));
                        
                        if(code == 0){
                            socket.emit("reprintStatus", "Požadavek přijat");
                            self.client.write(Buffer.from([0x06, res[0]]), () => {
                                self.log(Buffer.from([0x06, res[0]]).toString("hex"), "request");
                                self.disconnectTerminal().catch(err => console.log(err)); // disconnect
                            }); // confirm
                            resolve();
                        }else{
                            self.stopLogging();
                            reject("Požadavek nebyl nepřijat");
                        }
                    }
                });
            }).catch(err => {
                reject(err);
            });
        });
    },
    /**
     * Remote restart
     */
    restartTerminal: () => {
        self.startLogging();

        return new Promise ( (resolve, reject) => {
            // Setup individual parts
            let num = self.current_trans_num, // num (cac/sac) 
                msg_counter = Buffer.from(self.padInt(self.current_trans_num, 4)), // Message counter
                message_header = [0x30, 0x31, 0x30, 0x30, 0x30, 0x30], // message header
                header = [0x30,0x39,0x30,0x30,0x31,0x31,0x30,0x34], // mandatory header
                crc; // CRC

                let stuffed_data = self.stuffDataBytes(Buffer.from(header)); // Preceed data region bytes with DLE if needed

            // Generate CRC from the needed parts of the message
            crc = self.countCRC(Buffer.concat([
                Buffer.from([num]),
                Buffer.from(message_header),
                msg_counter,
                stuffed_data,
                Buffer.from([self.ETX])
            ]));

            // Construct final payload
            let final_payload = Buffer.concat([
                Buffer.from([self.STX]), // STX
                Buffer.from([num]), // Message num (iterator)
                Buffer.from(message_header), // Message header
                msg_counter,
                stuffed_data, // Data header+body
                Buffer.from([self.ETX]), // ETX
                crc // CRC1+CRC2
            ]);
            
            self.log(final_payload.toString("hex"), "request");
            self.connectTerminal().then(() => {
                self.client.write(final_payload, () => {
                    console.log("Sent!");
                    self.current_trans_num++;
                });

                self.client.on("data", (res) => {
                    self.log(res.toString("hex"), "response");

                    let res_len = res.length;

                    if(res_len <= 2){
                        // Status messages
                        if(res[0] == 4) {
                            self.stopLogging();
                            reject("Spojení přerušeno"); // not ok
                        }
                        if(res[0] == 6) console.log("Požadavek přijat"); // OK
                        if(res.toString("hex") == 15) {
                            self.stopLogging();
                            reject("Požadavek nebyl nepřijat"); // not ok
                        }
                    }else{
                        let code = parseInt(self.hex2string(res.slice(20,22).toString("hex")));
                        if(code == 0){
                            self.client.write(Buffer.from([0x06, res[0]]), () => {
                                self.log(Buffer.from([0x06, res[0]]).toString("hex"), "request");
                                self.disconnectTerminal().catch(err => console.log(err)); // disconnect
                                self.stopLogging();
                            }); // confirm
                            resolve();
                        }else{
                            self.stopLogging();
                            reject("Požadavek nebyl nepřijat");
                        }
                    }
                });
            }).catch(err => {
                self.stopLogging();
                reject(err);
            });
        });
    }
};

module.exports = self;