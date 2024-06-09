const serialport = require("serialport"),
      ByteLength = require('@serialport/parser-byte-length'),
      CFG = require("config"),
      log = require(__basedir + '/config/log'),
      moment = require("moment"),
      CardReads = require( __basedir + '/models/card_reads' ), // singular card read events (personificator data)
      ReaderEvent = require( __basedir + '/models/reader_events' ), // Control unit events
      ReaderSettings = require( __basedir + '/models/reader_settings' ); // Backend-side settings for particular readers ( rooms )

const self = {

    // dataprocessing loop
    processReaderData: async (array, roomPriority) => {
        let processed = 0;
        for (let i = 0; i < array.length; i++) {
            if(array[i].roomPriority == roomPriority){
                try{
                    console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].readerId+"]["+array[i].readerAddress+"] Getting status");
                    
                    if (!array[i].isPersonificator) {
                        await self.getEventData(array[i].readerId, array[i].readerAddress);
                        console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].readerId+"]["+array[i].readerAddress+"] Got status");
                    }
                    else console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].readerId+"]["+array[i].readerAddress+"] Skipping personificator");
                    
                    await self.wait(200);
                    processed += 1;
                } catch (e) {
                    console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].readerId+"]["+array[i].readerAddress+"] Error");
                    console.error(e);
                }
            }else continue;
        }

        return processed;
    },

    // Wait for a set of time in ms
    wait: ms => new Promise((r, j)=>setTimeout(r, ms)),

    overwritePortForEveryRoom: async () => {
        try {
            await ReaderSettings.updateMany({}, {"readerId":"COM12"}, {});
            console.log("Done!");
        } catch (err) {
            console.log(err);
        }
    },

    // "Checksum" from hex string
    calculateSum: async (hex, limit) => {
        try {
            let arr = hex.match(/.{1,2}/g),
                total = 0,
                i = 1;

            for await (let num of arr) {
                if(i < limit) total += parseInt(num, 16);
                i++;
            }

            // Signed 2s hex decimals
            return '0x' + parseInt( (~total + 1 >>> 0).toString(2).substr(-8), 2 ).toString(16);
        } catch(err) {
            log.error(err);
        }
    },

    hex2bin: (hex) => {
        return ("00000000" + (parseInt(hex, 16)).toString(2)).substr(-8);
    },

    // Return status ID
    returnEventStatus: (status) => {
        let bin = ("00000000" + (parseInt(status, 16)).toString(2)).substr(-8),
            sensor = parseInt(bin.slice(-4), 2), // sensor int
            result = bin[0]; // result int

        if (result !== "1") return 'denied';
        else {
            if (sensor == 0) return 'entrance';
            else if (sensor == 1) return 'exit';
            else return '';
        }
    },

    /**
     * Save data from a personificator to DB
     */
    savePersonificationData: (data) => {
        let doc = new CardReads(data);
            doc.save( err => {
                if(err) log.error(JSON.stringify(err));
                else return true;
            });
    },

    /**
     * Save raw event data from a control unit into DB
     */
    saveEventData: (data) => {
        let count = data.length,
            x = 0;

        return new Promise( (resolve, reject) => {
            data.forEach( event => {
                let doc = new ReaderEvent(event);
                doc.save( err => {
                    if(err) reject(err);
                });

                x++;
                if(x >= count) resolve();
            });
        });
    },

    // Get all ports
    getPorts: () => {
        return new Promise((resolve, reject) => {
            serialport.list().then(ports => {
                let i = 0;
    
                ports.forEach((port) => {
                    if(typeof port.path == 'undefined') return;
                    let reader_obj = {comName: port.path, serialNum: port.serialNumber};
                    reader_settings[port.path] = null;
    
                    self.getReaderSettings(port.path)
                        .then(settings => {
                            console.log('Found port '+port.path+' that matches existing system settings.');
                            reader_obj.settings = settings;
    
                            gPorts.add(port.path); // add to COM port list
                            reader_settings[port.path] = settings; // add to settings list
                            readers.push(reader_obj); // add to reader list
    
                            i++;
                            if(i >= CFG.get("readers.portCount")) resolve(); // got the ports
                        })
                        .catch(err => {
                            console.log('Skipping port '+port.path+'.', err);
                        });
                });
            }).catch(err => {
                log.error(err);
                reject(err);
            });
        });
    },

    /** 
     * Grab current system settings for readers
     */
    getReaderSettings: (comName) => {
        return new Promise( (resolve, reject) => {
            let settings = ReaderSettings.find({readerId: comName}).sort({readerAddress: 1}).exec();
                settings.then( s => {
                    if(s.length) resolve(s);
                    else reject('No document found.');
                }).catch(err => {
                    log.error(err);
                    reject(err);
                });
        });
    },
    
    /**
     * Opens all personficator ports, other ports should be open when needed by their respective fc
     */
    startPersonificators: () => {
        readers.forEach( reader => {    

            for (let x = 0; x < reader.settings.length; x++) {
                // Personificator reader (just reading)
                if(reader.settings[x].length == 1 && reader.settings[x].isPersonificator){
                    // Personifikátor 
                    let baudRate = 9600,
                        bitlen = 7,
                        parity = "none";
                
                    let port = new serialport(reader.comName, {
                        baudRate: baudRate,
                        dataBits: 8,
                        parity: parity,
                        xon: false,
                        xoff: false,
                        lock: false
                    }),
                    parser = port.pipe(new ByteLength({length: bitlen})); // personifikator = 7
                    reader.port = port; // assign
            
                    port.on('open', () => {
                        console.log('Opened personificator '+reader.comName+'');
                        port.flush((err) => {
                            if(err) console.log(err);
                            else console.log("Flushed personificator "+reader.comName+"");
                        });
                    
                        parser.on('data', data => {
                            console.log((data.toString('hex')).slice(0, -2) + ", " + data.toString('hex') + ", " + data)

                            self.savePersonificationData({
                                'readerId' : reader.comName,
                                'cardId' : (data.toString('hex')).slice(0, -2), // skip last two bits ( I believe thats some special info, TODO ask )
                            });
                        });
                        port.on("error", err => log.error(err));
                    });
                }
            }

        });
    },

    /**
     * Set the internal time of the reader unit
     */
    updateTime: (readerId, readerAddress) => {
        return new Promise( (resolve,reject) => {
            const index = readers.findIndex(r => r.comName == readerId),
                  reader = readers[index],
                  address = Buffer.from([readerAddress]),
                  instruction = Buffer.from([0x02]);

            let port = new serialport(reader.comName, {
                    baudRate: CFG.get("readers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("readers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false
                }),
                response_count = 0;

            port.on('open', () => {
                console.log("Opened reader "+readerId+"");
                // Flush buffers
                port.flush((err) => {
                    if(err) { log.error(err); reject('Port flush error!'); }
                    else {

                        console.log('Flushed reader '+readerId+'.');

                        // Send device address
                        port.write(address, (err) => {
                            console.log('Written address to reader '+readerId+'.');

                            if(err) { log.error(err); reject('Address definition error!'); }
                            else {

                                // Close the port to switch settings
                                port.close( err => {
                                    console.log('Closed reader '+readerId+'.');

                                    if(err) { log.error(err); reject('Port close error!'); }
                                    else {

                                        // Re-init the port with different settings
                                        let port = new serialport(reader.comName, {
                                            baudRate: 19200,
                                            dataBits: 8,
                                            parity: "space",
                                            xon: false,
                                            xoff: false,
                                            lock: false
                                        }),
                                            single_byte = port.pipe(new ByteLength({length: 1}));

                                        // Open it again
                                        port.on("open", () => {
                                            console.log('Opened reader '+readerId+'.');

                                            port.write(instruction, (err) => {
                                                console.log('Written instruction to reader '+readerId+'.');

                                                if(err) { log.error(err); reject('Instruction write error!'); }
                                                else {

                                                    // Get current datetime
                                                    let today = moment(),
                                                        year = parseInt(today.format("YY")),
                                                        month = parseInt(today.format("MM")).toString(16),
                                                        day = parseInt(today.format("D")),
                                                        weekday = parseInt(today.format("d")).toString(16),
                                                        hour = parseInt(today.format("H")).toString(16),
                                                        minute = parseInt(today.format("m")).toString(16),
                                                        second = parseInt(today.format("s")).toString(16);

                                                    //day = self.hex2bin(day);
                                                    let leap_calc = 64 * (year - 4 * Math.floor(year / 4)) + day;
                                                        leap_calc = leap_calc.toString(16);

                                                    let time_settings = ["0x"+month, "0x"+leap_calc, "0x"+hour, "0x"+minute, "0x"+second, "0x"+weekday];

                                                    self.calculateSum( Buffer.from(time_settings).toString('hex'), 7 ).then( sum => {
                                                        time_settings.push(+sum);
                                                        let final_payload = Buffer.from(time_settings);

                                                        // write payload
                                                        port.write(final_payload, (err) => {
                                                            if (err) { log.error(err); reject('Data payload error!'); }
                                                        });
                                                        console.log('Sent time update command to reader '+readerId+'.');
                                                        console.log('data: ', final_payload);
                                                    });
                                                }
                                            });

                                            // Receive bytedata listener
                                            single_byte.on('data', data => {
                                                console.log("Received response: ", data);

                                                let hex = data.toString('hex');
                                                if(response_count == 1 && hex == 'aa') resolve(); // succesful write
                                                else if(response_count == 1 && hex != 'aa') reject('Write unsucessful!'); // 0x00

                                                response_count++;
                                            });
                                        });

                                    }
                                });

                            }

                        });
                    }
                });
            });
        });
    },

    /**
     * Resets a particular reader
     * WARNING: This cleans the ENTIRE MEMORY, all existing permissions GONE, all events GONE!
     * Only do this if you are able to restore desired permissions and have backed up all event data!
     * 
     * This also sets the internal memory to accept 10k card codes and 600 events (see below), this can be changed.
     * 
     * Instruction 83H
     */
    resetReader: (readerId, readerAddress) => {
        return new Promise( (resolve, reject) => {
            const index = readers.findIndex(r => r.comName == readerId),
                  reader = readers[index],
                  address = Buffer.from([readerAddress]),
                  instruction = Buffer.from([0x83]);

                let settings = [0xfc, 0x27, 0x10, 0x00, 0x00]; // FC, 0x27+0x10 = 10 000, ignore, ignore, + add calc
                let port = new serialport(reader.comName, {
                    baudRate: CFG.get("readers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("readers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false
                }),
                numberOfRetries = CFG.get("readers.retryAttempts"),
                retry_counter = 0,
                response_count = 0;

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("readers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened reader "+readerId+"");
                        // Flush buffers
                        port.flush((err) => {
                            if(err) { log.error(err); reject('Port flush error!'); }
                            else {
        
                                console.log('Flushed reader '+readerId+'.');
        
                                // Send device address
                                port.write(address, (err) => {
                                    console.log('Written address to reader '+readerId+'.');
        
                                    if(err) { log.error(err); reject('Address definition error!'); }
                                    else {
        
                                        // Close the port to switch settings
                                        port.close( err => {
                                            console.log('Closed reader '+readerId+'.');
        
                                            if(err) { log.error(err); reject('Port close error!'); }
                                            else {
        
                                                // Re-init the port with different settings
                                                let port = new serialport(reader.comName, {
                                                    baudRate: 19200,
                                                    dataBits: 8,
                                                    parity: "space",
                                                    xon: false,
                                                    xoff: false,
                                                    lock: false
                                                }),
                                                    single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                // Open it again
                                                port.on("open", () => {
                                                    console.log('Opened reader '+readerId+'.');
        
                                                    port.write(instruction, (err) => {
                                                        console.log('Written instruction to reader '+readerId+'.');
        
                                                        if(err) { log.error(err); reject('Instruction write error!'); }
                                                        else {
                                                            self.calculateSum( Buffer.from(settings).toString('hex'), 6 ).then( sum => {
                                                                settings.push(+sum);
                                                                let final_payload = Buffer.from(settings);
        
                                                                // write payload
                                                                port.write(final_payload, (err) => {
                                                                    if (err) { log.error(err); reject('Data payload error!'); }
                                                                });
                                                                console.log('Sent reset command to reader '+readerId+'.');
                                                                console.log('data: ', final_payload);
                                                            });
                                                        }
                                                    });
        
                                                    // Receive bytedata listener
                                                    single_byte.on('data', data => {
                                                        console.log("Received response: ", data);
        
                                                        let hex = data.toString('hex');
                                                        if(response_count == 1 && hex == 'aa') resolve(); // succesful write
                                                        else if(response_count == 1 && hex != 'aa') reject('Write unsucessful!'); // 0x00
        
                                                        response_count++;
                                                    });
                                                });
        
                                            }
                                        });
        
                                    }
        
                                });
                            }
                        });
                    }
                });
            };
            startProcess();

        });
    },

    /**
     * Register a given cardId into a control unit via its serial number
     * Instruction 80H
     */
    registerNewCard: (cardId, readerId, readerAddress) => {
        return new Promise((resolve, reject) => {

            const index = readers.findIndex(r => r.comName == readerId),
                reader = readers[index],
                settings = reader.settings,
                address = Buffer.from([readerAddress]),
                instruction = Buffer.from([0x80]);

            let port = new serialport(reader.comName, {
                    baudRate: CFG.get("readers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("readers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                numberOfRetries = CFG.get("readers.retryAttempts"),
                retry_counter = 0,
                response_count = 0;

            if(settings.pinCode){
                // Add pin code to cardId if needed
                cardId = cardId.slice(0,8) + settings.pinCode;
            }

            const startProcess = () => {
                // Open port
                port.open((err) => {

                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("readers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened reader "+readerId+"");
                        // Flush buffers
                        port.flush((err) => {
                            if(err) { log.error(err); reject('Port flush error!'); }
                            else {
    
                                console.log('Flushed reader '+readerId+'.');
    
                                // Send device address
                                port.write(address, (err) => {
    
                                    console.log('Written address to reader '+readerId+'.');
    
                                    if(err) { log.error(err); reject('Address definition error!'); }
                                    else {
    
                                        // Close the port to switch settings
                                        port.close( err => {
    
                                            console.log('Closed reader '+readerId+'.');
    
                                            if(err) { log.error(err); reject('Port close error!'); }
                                            else {
    
                                                // Re-init the port with different settings
                                                let port = new serialport(reader.comName, {
                                                    baudRate: 19200,
                                                    dataBits: 8,
                                                    parity: "space",
                                                    xon: false,
                                                    xoff: false,
                                                    lock: false
                                                }),
                                                    single_byte = port.pipe(new ByteLength({length: 1}));
    
                                                // Open it again
                                                port.on("open", () => {
    
                                                    console.log('Opened reader '+readerId+'.');
    
                                                    port.write(instruction, (err) => {
    
                                                        console.log('Written instruction to reader '+readerId+'.');
    
                                                        if(err) { log.error(err); reject('Instruction write error!'); }
                                                        else {
                                                            // Construct the payload (card id)
                                                            let card_bytes = Buffer.from(cardId, 'hex'),
                                                                remaining_bytes = [0x7F, 0x08, 0x00, 0x16, 0x00];
                                                                                // 0x7F => 01111111 (all days in a week)
                                                                                // 0x08 => 8
                                                                                // 0x00 => 0
                                                                                // 0x16 => 22
                                                                
                                                            self.calculateSum( Buffer.concat([card_bytes, Buffer.from(remaining_bytes)]).toString('hex'), 12 ).then( sum => {
                                                                remaining_bytes.push(+sum);
                                                                let final_payload = Buffer.concat([card_bytes, Buffer.from(remaining_bytes)]);
    
                                                                // write payload
                                                                port.write(final_payload, (err) => {
                                                                    if (err) { log.error(err); reject('Data payload error!'); }
                                                                });
                                                                console.log('Sent card to reader '+readerId+'.');
                                                                console.log('data: ', final_payload);
                                                            });
                                                        }
                                                    });
    
                                                    // Receive bytedata listener
                                                    single_byte.on('data', data => {
                                                        console.log("Received response: ", data);
    
                                                        let hex = data.toString('hex');
                                                        if(response_count == 1 && hex == 'aa') resolve(); // succesful write
                                                        else if(response_count == 1 && hex != 'aa') reject('Write unsucessful!'); // 0x00
    
                                                        response_count++;
                                                    });
                                                });
    
                                            }
                                        });
    
                                    }
    
                                });
                            }
                        });
                    }
                });

            };
            startProcess();

        });
    },

    /** De-register a cardId from a reader, to prevent access 
     * Instruction 82H
    */
    deregisterCard: (cardId, readerId, readerAddress) => {
        return new Promise( (resolve, reject) => {

            const index = readers.findIndex(r => r.comName == readerId),
                  reader = readers[index],
                  address = Buffer.from([readerAddress]),
                  instruction = Buffer.from([0x82]);

            let port = new serialport(reader.comName, {
                    baudRate: CFG.get("readers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("readers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                numberOfRetries = CFG.get("readers.retryAttempts"), // Todo increase this for some priority tasks?
                retry_counter = 0,
                response_count = 0;

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("readers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened reader "+readerId+"");
                        // Flush buffers
                        port.flush((err) => {
                            if(err) log.error(JSON.stringify(err));
                            else {
        
                                console.log('Flushed reader '+readerId+'.');
        
                                // Send device address
                                port.write(address, (err) => {
        
                                    if(err) { log.error(JSON.stringify(err)); reject('Address definition error!'); }
                                    else {
        
                                        // Close the port to switch settings
                                        port.close( err => {
                                            if(err) { log.error(JSON.stringify(err)); reject('Port closing error!'); }
                                            else {
        
                                                console.log('Closed reader '+readerId+'.');
        
                                                // Re-init the port with different settings
                                                let port = new serialport(reader.comName, {
                                                    baudRate: 19200,
                                                    dataBits: 8,
                                                    parity: "space",
                                                    xon: false,
                                                    xoff: false,
                                                    lock: false
                                                }),
                                                    single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                // Open it again
                                                port.on("open", () => {
        
                                                    console.log('Opened reader '+readerId+'.');
        
                                                    port.write(instruction, (err) => {
                                                        if(err) { log.error(JSON.stringify(err)); reject('Instruction payload error!'); }
                                                        else {
                                                            // Construct the payload (card id)
                                                            let card_bytes = Buffer.from(cardId, 'hex'),
                                                                remaining_bytes = [];
        
                                                            self.calculateSum( card_bytes.toString('hex'), 7 ).then( sum => {
                                                                remaining_bytes.push(+sum);
                                                                let final_payload = Buffer.concat([card_bytes, Buffer.from(remaining_bytes)]);
                
                                                                // write payload
                                                                port.write(final_payload, (err) => {
                                                                    if(err) { log.error(JSON.stringify(err)); reject('Data payload error!'); }
                                                                    console.log('Sent card to reader  '+readerId+'.');
                                                                    console.log("data: ", final_payload);
                                                                });
                                                            });
                                                        }
                                                    });
        
                                                    // Receive bytedata listener
                                                    single_byte.on('data', data => {
                                                        console.log("Received response: ", data);
        
                                                        let hex = data.toString('hex');
        
                                                        if(response_count == 1 && hex == 'aa') resolve(); // succesful write
                                                        else if(response_count == 1 && hex != 'aa') reject('Write unsucessful!'); // 0x00
        
                                                        response_count++;
                                                    });
                                                });
        
                                            }
                                        });
        
                                    }
        
                                });
                            }
                        });
                    }
                });
            };
            startProcess();

        });
    },

    /**
     * 01H Instruction
     * Download list of event data from a particular reader
     */
    getEventData: (readerId, readerAddress) => {
        // Control unit in office: readerId => 6&32a5dfec&0&1
        return new Promise((resolve, reject) => {

            // function vars
            let total_bytes = 0,
                data_counter = 1,
                row_counter = 0,
                counter = 1,
                numberOfRetries = CFG.get("readers.retryAttempts"),
                retry_counter = 0,
                isResolved = false,
                return_data = [];

            // Reader and related
            const index = readers.findIndex(r => r.comName == readerId),
                  reader = readers[index],
                  reader_settings = reader.settings[reader.settings.findIndex(r => r.readerAddress == readerAddress)],
                  address = Buffer.from([readerAddress]),
                  instruction = Buffer.from([0x01]);

            //if ( typeof reader.port != 'undefined') reject('Port in use'); // TBD
            let port = new serialport(reader.comName, {
                baudRate: CFG.get("readers.baudRate"),
                dataBits: 8,
                parity: CFG.get("readers.parity"),
                xon: false,
                xoff: false,
                lock: false,
                autoOpen: false
            });

            const startProcess = () => {
                port.open((err) => {

                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("readers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened reader "+readerId+"");
                        port.flush((err) => {
                            console.log("Flushed reader "+readerId+"");
        
                            if(err) { log.error(JSON.stringify(err)); reject('Port flush error!'); port.close(); }
                            else {
        
                                port.write(address, (err) => {
        
                                    console.log("Written address to reader "+readerId+"");
        
                                    if(err) { log.error(JSON.stringify(err)); reject('Address definition error!'); port.close(); }
                                    else {
                                        
                                        port.close(err => {
                                            if(err) { log.error(JSON.stringify(err)); reject('Port close error!'); port.close(); }
                        
                                            console.log("Closed reader "+readerId+"");
                                            
                                            let port = new serialport(reader.comName, {
                                                autoOpen: false,
                                                baudRate: 19200,
                                                dataBits: 8,
                                                parity: "space",
                                                xon: false,
                                                xoff: false,
                                                lock: false
                                            }),
                                            single_byte = port.pipe(new ByteLength({length: 1}));
                        
                                            port.open((err) => {

                                                if(err){
                                                    console.error(err);
                                                    reject(err);
                                                }
                                                else{
                                                    console.log("Opened reader "+readerId+"");
                        
                                                    port.write(instruction, (err) => {
                                                        console.log("Written instruction to reader "+readerId+"");
                                                        if(err) { 
                                                            log.error(JSON.stringify(err)); 
                                                            if (port.isOpen) port.close((err) => console.error(err));
                                                            isResolved = true;
                                                            reject('Instruction write error!');
                                                        }
                                                    });
                            
                                                    resp_received = false;
                                                    single_byte.on('data', data => {
                                                        console.log("Byte "+counter+" ", data);
                                                        if (!resp_received) resp_received = true;
                            
                                                        // FCH
                                                        if(counter == 1) console.log('Ignoring FCH', data);
                                                        
                                                        // LSB
                                                        if(counter == 2) {
                                                            let lsb = (parseInt(data.toString('hex'), 16).toString(2)).padStart(8, '0');
                                                            console.log('LSB is ', lsb);
                                                            total_bytes = parseInt(lsb, 2);
                                                            //total_rows = (parseInt(lsb, 2) - 1) / 11; // -1 for the control byte
                                                        }
                            
                                                        // MSB
                                                        if(counter == 3) {
                                                            let msb = (parseInt(data.toString('hex'), 16).toString(2)).padStart(8, '0');
                                                            console.log('MSB is ', msb);
                            
                                                            if(msb.charAt(7) == "1"){
                                                                // 8th bit is 1 ?
                                                                // Error transfer
                                                                // ? how to deal with this actually ?
                            
                                                                // Delete?
                                                                port.write(Buffer.from([0xAA]), (err) => {
                                                                    if(err) { 
                                                                        if (port.isOpen) port.close((err) => console.error(err));
                                                                        console.log('Write err: ', err); 
                                                                        reject('Delete instruction error!'); 
                                                                    }
                                                                    else { 
                                                                        console.log('Written response to delete internal memory.');
                                                                        if (port.isOpen) port.close((err) => console.error(err));
                                                                    }
                                                                });
            
                                                                isResolved = true;
                                                                reject('Data corruption error!');
                                                            }
                                                        }
                            
                                                        // DATA -->
                                                        if(counter > 3 && counter < (total_bytes + 3)){
                                                            if(typeof return_data[row_counter] == 'undefined') return_data[row_counter] = [];
                                                            return_data[row_counter].push(data);
                                                            data_counter++;
                            
                                                            if(data_counter > 11){
                                                                data_counter = 1;
                                                                row_counter++;
                                                            }
                                                        }
                            
                                                        if(counter >= (total_bytes + 3)){
                                                            console.log('Control sum is ', (parseInt(data.toString('hex'), 16).toString(2)).padStart(8, '0'));
                            
                                                            let formatted_data = [];
            
                                                            if(return_data.length > 0){
                                                                return_data.forEach( event => {
                                                                    try {
                                                                        let obj = {};
                                                                        obj.cardId = Buffer.concat([event[0], event[1], event[2], event[3], event[4], event[5]]).toString('hex');
                                                                        obj.time = event[7].toString('hex') + ':' + event[6].toString('hex'); // hour : minute
                                                                        obj.day = event[8].toString('hex');
                                                                        obj.month = event[9].toString('hex');
                                                                        obj.eventStatus = self.returnEventStatus(event[10].toString('hex'));
                                                                        obj.readerId = reader.comName;
                                                                        obj.readerAddress = readerAddress;
                                                                        obj.gymId = reader_settings.gymId;
                                
                                                                        formatted_data.push(obj);
                                                                    }catch (e) {
                                                                        console.log("Event error: ", event);
                                                                    }
                                                                });
                                
                                                                self.saveEventData(formatted_data).then( () =>{
                                                                    // Confirm succesful receiving/saving and delete data in machine
                                                                    port.write(Buffer.from([0xAA]), (err) => {
                                                                        if(err) log.error(JSON.stringify(err));
                                                                        
                                                                        console.log('Replied with response to delete internal event memory.');
                                                                        if (port.isOpen) port.close(err => console.error(err));
                                                                        isResolved = true;
                                                                        resolve();
                                                                    });
            
                                                                    console.log('Succesfuly saved reader data into DB');
                                                                }).catch(err => {
                                                                    isResolved = true;
                                                                    if (port.isOpen) port.close(err => console.error(err));
                                                                    reject("Failed saving data to DB")
                                                                });;
                                                            }else{
                                                                isResolved = true;
                                                                console.log('No events to be pulled, moving along.');
                                                                if (port.isOpen) port.close(err => console.error(err));
                                                                reject("No events");
                                                            }
                            
                                                        }
                            
                                                        counter++;
                                                    });

                                                    setTimeout(() => {
                                                        if (!resp_received) {
                                                            console.log("No response, skipping.");
                                                            if (port.isOpen) port.close(err => console.error(err));
                                                            reject("No response");
                                                        }
    
                                                    }, CFG.get("readers.responseTimer"));

                                                    setTimeout(() => {
                                                        if(!isResolved){
                                                            console.log("Hanged, skipping");
                                                            if (port.isOpen) port.close(err => console.error(err));
                                                            reject("Response hanged");
                                                        }
                                                    }, CFG.get("readers.hangTimer"));
                                                }
                                            });
                        
                                        });
                                    }
                                });
        
                            }
        
                        });
                    }
                });
            };

            startProcess();
        });
    }

};

module.exports = self;