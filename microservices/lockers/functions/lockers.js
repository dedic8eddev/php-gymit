const serialport = require("serialport"),
      ByteLength = require('@serialport/parser-byte-length'),
      CFG = require('config'),
      log = require(__basedir + '/config/log'),
      moment = require("moment"),
      LockerSettings = require( __basedir + '/models/locker_settings' ); // Backend-side settings for particular readers ( rooms )

const self = {

    processLockerStatuses: async (array) => {
        processed = 0;
        for (let i = 0; i < array.length; i++) {
            try{
                console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].lockerId+"]["+array[i].lockerAddress+"] Getting status");
                await self.getLockerStatus(array[i].lockerId, array[i].lockerAddress);
                console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].lockerId+"]["+array[i].lockerAddress+"] Got status");
                await self.wait(250);
                processed += 1;
            } catch (e) {
                console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+array[i].lockerId+"]["+array[i].lockerAddress+"] Error");
                console.error(e);
            }
        }

        return processed;
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
            log.error(err.message);
        }
    },

    // Add lockers to DB
    populateDbWithLockers : (gender) => {
        return new Promise ((resolve, reject) => {
            const total = gender == "male" ? 152 : 150;
        
            for (let x = 1; x <= total; x++){
                let doc = new LockerSettings({
                    "gymId" : "01",
                    "lockerId" : gender == "male" ? CFG.get("lockers.malePort") : CFG.get("lockers.femalePort"),
                    "lockerAddress" : x,
                    "lockerNumber" : x,
                    "lockerRoom" : gender
                });
    
                doc.save((err, doc) => {
                    if (!err) console.log("Added "+gender+" locker #"+x+"");
                    else console.log(err.message);
                });
    
                if(x == total) resolve();
            }
        });
    },

    // Wait function, to not overload these suckers
    wait: ms => new Promise((r, j)=>setTimeout(r, ms)),

    // Hex conversion stuff
    hex2bin: (hex) => {
        return ("00000000" + (parseInt(hex, 16)).toString(2)).substr(-8);
    },
    int2hex: (int) => {
        return '0x'+int.toString(16);
    },
    hex2int: (hex) => {
        return parseInt(hex, 16);
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

    // Get all ports
    getPorts: () => {
        return new Promise((resolve, reject) => {
            serialport.list().then(ports => {
                let i = 0;
    
                ports.forEach((port) => {
                    if(typeof port.serialNumber == 'undefined') return;
                    let locker_obj = {comName: port.path, serialNum: port.serialNumber};
                    lockers_settings[port.path] = null;
    
                    self.getLockerSettings(port.path)
                        .then(settings => {
                            console.log('Found port '+port.path+' that matches existing system settings.');

                            locker_obj.settings = settings;
                            lockers.push(locker_obj); // parent locker objects (COMX, COMX => COMX.settings => lockers)
                            lockers_settings[port.path] = settings; // actual locker settings
                            gPorts.add(port.path); // port list
    
                            i++;
                            if(i >= CFG.get("lockers.portCount")) resolve(); // got the ports
                        })
                        .catch(err => {
                            console.log('Skipping port '+port.path+'.', err);
                        });
                });
            }).catch(err => {
                log.error(err.message);
                reject(err);
            });
        });
    },

    /** 
     * Grab current system settings for lockers
     */
    getLockerSettings: (portPath) => {
        return new Promise( (resolve, reject) => {
            let settings = LockerSettings.find({lockerId: portPath}).sort({lockerAddress: 1}).exec();
                settings.then( s => {
                    if(s.length) resolve(s);
                    else reject('No document found.');
                }).catch(err => {
                    log.error(err.message);
                    reject(err);
                });
        });
    },

    /**
     * Run this just once on the first day of setup..
     */
    firstSetupLockers: async () => {
        let processed = 0,
            total = 0;

        for (let i = 0; i < total; i++){
            let settings = lockers[i].settings;
            for (let x = 0; x < settings.length; x++) {
                console.log("Processing locker setup for =>", "P:"+lockers[i].comName, "A:"+settings[x].lockerAddress);

                try {
                    await locker_api.wait(500); // wait for .5s in every loop before registering master cards
                    await locker_api.addMasterCardsToLocker(settings[x].masterCards[0], settings[x].masterCards[1], lockers[i].comName, settings[x].lockerAddress);

                    processed++;
                } catch (err) {
                    log.error("Locker setup loop error: " + err.message);
                }

                total++;
            }
        }
    },

    // Save the current status information into DB
    updateLockerStatus: (status, lockerId, lockerAddress) => {
        return new Promise((resolve, reject) => {
            LockerSettings.findOne({lockerId: lockerId, lockerAddress: lockerAddress}).exec().then((document, err) => {
                if(err) reject(err);
                else {
                    document.lockerStatus.status = status.status;
                    document.lockerStatus.response = status.response;
        
                    document.save().then(() => {
                        resolve();
                    }).catch(err => {
                        reject("Couldnt update locker status!");
                    });
                }
            });
        });
    },

    /**
     * Confirm an arrival of a message
     * 06H confirmation (needed for 01H)
     */
    confirmArrival: (lockerId, lockerAddress) => {
        return new Promise( (resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  instruction = Buffer.from([0x06]);

            let port = new serialport(locker.comName, {
                        baudRate: CFG.get("lockers.baudRate"),
                        dataBits: 8,
                        parity: CFG.get("lockers.parity"),
                        xon: false,
                        xoff: false,
                        lock: false
                    }),
                    response_count = 0,
                    single_byte = port.pipe(new ByteLength({length: 1}));

            port.on('open', () => {
                console.log("Opened locker "+lockerId+"");

                port.flush((err) => {
                    if(err) { log.error(err.message); reject('Port flush error!'); }
                    else {

                        console.log('Flushed locker '+lockerId+'.');
                        port.write(address_one, (err) => {
                            console.log('Written #1 address to locker '+lockerId+'.');
                            if (err) { log.error(err.message); reject('Address definition error!'); }
                            else {
                                port.write(address_two, (err) => {
                                    console.log('Written #2 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {

                                        const sendInstruction = () => {
                                            port.close( err => {
                                                console.log('Closed locker '+lockerId+'.');

                                                if(err) { log.error(err.message); reject('Port close error!'); }
                                                else {
                                                    // Re-init the port with different settings
                                                    let port = new serialport(locker.comName, {
                                                        baudRate: 19200,
                                                        dataBits: 8,
                                                        parity: "space",
                                                        xon: false,
                                                        xoff: false,
                                                        lock: false
                                                    });

                                                    port.on("open", () => {
                                                        console.log('Opened locker '+lockerId+'.');

                                                        port.write(instruction, (err) => {
                                                            console.log('Written confirmation to locker '+lockerId+'.');

                                                            if(err) { 
                                                                if(port.isOpen) port.close();
                                                                log.error(err.message); 
                                                                reject('Instruction write error!'); 
                                                            }
                                                            else {
                                                                if(port.isOpen) port.close();
                                                                resolve();
                                                            }
                                                        });
                                                    });

                                                }
                                            });
                                        };

                                        single_byte.on('data', data => {
                                            console.log("Received address response: ", data);

                                            let hex = data.toString('hex');
                                            if(response_count == 0 && hex != 'aa') reject("No locker responded"); // succesful write
                                            else if (response_count == 0 && hex == 'aa') {
                                                response_count++;
                                                sendInstruction();
                                            }
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
     * Remove card blockation from locker
     * Instruction 0BH
     */
    resetCardsBlockInLocker: (lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x0B]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Get a locker in the system, this returns the type of the locker (normal / VIP)
     * 
     * Instruction 08H
     */
    getLocker: (lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x08]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                                console.log(hex, response_count);

                                                                            if(response_count == 3 && hex == "54"){
                                                                                resolve("STANDARD");
                                                                            }else if (response_count == 3 && hex == "52"){
                                                                                resolve("VIP");
                                                                            }

                                                                            response_count++;
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Reset the card memory in a locker
     * removes all cards in the memory of this locker, thus making it open to anyone
     * Instruction 0AH
     */
    resetLocker: (lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x0A]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Add master cards entry to a locker
     * Instruction 03H
     */
    addMasterCardsToLocker: (cardId1, cardId2, lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  card_one_bytes = Buffer.from(cardId1, "hex"),
                  card_two_bytes = Buffer.from(cardId2, "hex"),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x03]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
    
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
        
                                                                        self.calculateSum(Buffer.concat([card_one_bytes.slice(0,4), card_two_bytes.slice(0,4), Buffer.from([0x00])]))
                                                                            .then(sum => {
                                                                                let final_payload = Buffer.concat([card_one_bytes.slice(0,4), card_two_bytes.slice(0,4), Buffer.from([0x00]), Buffer.from([sum])]);
                                                                                port.write(final_payload, (err) => {
                                                                                    if(err){ log.error(err.message); reject("Data payload error!");}
                                                                                });
        
                                                                        });
        
        
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Open a locker with a CardId
     * Instruction 0DH
     * This should be run every time a client exits the gym and had a locker locked => to delete the cardId from the units
     */
    remoteOpenLockerWithCard: (cardId, lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  card_bytes = Buffer.from(cardId, "hex"),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x0D]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
        
                                                                        self.calculateSum(Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00])]))
                                                                            .then(sum => {
                                                                                let final_payload = Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00]), Buffer.from([sum])]);
                                                                                port.write(final_payload, (err) => {
                                                                                    if(err){ log.error(err.message); reject("Data payload error!");}
                                                                                });
        
                                                                        });
        
        
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Add a card entry to a locker
     * Instruction 02H
     */
    addCardToLocker: (cardId, lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  card_bytes = Buffer.from(cardId, "hex"),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x02]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if (err) {
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
        
                                                                        self.calculateSum(Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00])]))
                                                                            .then(sum => {
                                                                                let final_payload = Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00]), Buffer.from([sum])]);
                                                                                port.write(final_payload, (err) => {
                                                                                    if(err){ log.error(err.message); reject("Data payload error!");}
                                                                                });
        
                                                                        });
        
        
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Remove a card entry from a locker
     * Instruction 04H
     */
    deleteCardFromLocker: (cardId, lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  card_bytes = Buffer.from(cardId, "hex"),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x05]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                response_count = 0,
                retry_counter = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {    
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
        
                                                                        self.calculateSum(Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00])]))
                                                                            .then(sum => {
                                                                                let final_payload = Buffer.concat([card_bytes.slice(0,4), Buffer.from([0x00]), Buffer.from([sum])]);
                                                                                port.write(final_payload, (err) => {
                                                                                    if(err){ log.error(err.message); reject("Data payload error!");}
                                                                                });
        
                                                                        });
        
        
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Open a locker remotely
     * Instruction 05H
     * Run this during the night for maintenance to preamptively unlock every locker
     */
    remoteOpenLocker: (lockerId, lockerAddress) => {
        return new Promise ((resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],
                  locker_settings = locker.settings[locker.settings.findIndex(r => r.lockerAddress == lockerAddress)],
                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  numberOfRetries = CFG.get("lockers.retryAttempts"),
                  instruction = Buffer.from([0x05]);
        
            let port = new serialport(locker.comName, {
                    baudRate: CFG.get("lockers.baudRate"),
                    dataBits: 8,
                    parity: CFG.get("lockers.parity"),
                    xon: false,
                    xoff: false,
                    lock: false,
                    autoOpen: false
                }),
                retry_counter = 0,
                response_count = 0,
                single_byte = port.pipe(new ByteLength({length: 1}));        

            const startProcess = () => {
                port.open((err) => {
                    if(err){
                        if(retry_counter > numberOfRetries){
                            log.error(err.message); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }
    
                        retry_counter++;
                    }else{
                        console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) {log.error(err.message); reject("Port flush error!");}
                            else {
                                port.write(address_one, (err) => {
                                    console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { log.error(err.message); reject('Port close error!'); }
                                                        else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.on("open", () => {
                                                                console.log('Opened locker '+lockerId+'.');
        
                                                                port.write(instruction, (err) => {
                                                                    console.log('Written instruction to locker '+lockerId+'.');
        
                                                                    if(err) { log.error(err.message); reject('Instruction write error!'); }
                                                                    else {
        
        
                                                                        // Receive bytedata listener
                                                                        single_byte.on('data', data => {
                                                                            console.log("Received instruction response: ", data);
        
                                                                            let hex = data.toString('hex');
                                                                            if(response_count == 1 && hex != 'aa') reject("Transfer failure"); // unsuccesful write
                                                                            else if (response_count == 1 && hex == 'aa') {
                                                                                resolve();
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            });
        
                                                        }
                                                    });
                                                };
        
                                                single_byte.on('data', data => {
                                                    console.log("Received address response: ", data);
        
                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // unsuccesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        response_count++; // yay
                                                        sendInstruction();
                                                    }
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
     * Get a lockers status
     * Instruction 01H
     */
    getLockerStatus: (lockerId, lockerAddress) => {
        return new Promise( (resolve, reject) => {
            const index = lockers.findIndex(r => r.comName == lockerId),
                  locker = lockers[index],

                  address_one = Buffer.from([0x00]),
                  address_two = Buffer.from([self.int2hex(lockerAddress)]),
                  instruction = Buffer.from([0x01]),
                  
                  numberOfRetries = CFG.get("lockers.retryAttempts");

            let port = new serialport(locker.comName, {
                        autoOpen: false,
                        baudRate: CFG.get("lockers.baudRate"),
                        dataBits: 8,
                        parity: CFG.get("lockers.parity"),
                        xon: false,
                        xoff: false,
                        lock: false
                    }, false),
                    response_count = 0,
                    retry_counter = 0,
                    single_byte = port.pipe(new ByteLength({length: 1}));
            
            const startProcess = () => {
                port.open((err) => {
                    if (err) {
                        if(retry_counter > numberOfRetries){
                            log.error(err); 
                            reject("Port open error!");
                        }else{
                            self.wait(CFG.get("lockers.retryDelay")).then(() => {
                                //console.log("Retrying locker status..");
                                startProcess(); // retry
                            }).catch(err => {
                                reject("Port open error, retry failed.");
                            });
                        }

                        retry_counter++;
                    }
                    else{
                        //console.log("Opened locker port "+lockerId+"");
                        port.flush((err) => {
                            if(err) { log.error(err.message); reject('Port flush error!'); }
                            else {
        
                                //console.log('Flushed locker '+lockerId+'.');
                                port.write(address_one, (err) => {
                                    //console.log('Written #1 address to locker '+lockerId+'.');
                                    if (err) { log.error(err.message); reject('Address definition error!'); }
                                    else {
                                        port.write(address_two, (err) => {
                                            //console.log('Written #2 address to locker '+lockerId+'.');
                                            if (err) { log.error(err.message); reject('Address definition error!'); }
                                            else {
        
                                                let isResolved = false;
                                                const sendInstruction = () => {
                                                    port.close( err => {
                                                        //console.log('Closed locker '+lockerId+'.');
        
                                                        if(err) { 
                                                            log.error(err.message);
                                                            isResolved = true; 
                                                            reject('Port close error!'); 
                                                        }else {
        
                                                            // Re-init the port with different settings
                                                            let port = new serialport(locker.comName, {
                                                                autoOpen: false,
                                                                baudRate: 19200,
                                                                dataBits: 8,
                                                                parity: "space",
                                                                xon: false,
                                                                xoff: false,
                                                                lock: false
                                                            }),
                                                                new_single_byte = port.pipe(new ByteLength({length: 1}));
        
                                                            port.open((err) => {
                                                                if (err){
                                                                    isResolved = true;
                                                                    console.error(err);
                                                                    reject("Port open error!");
                                                                }else{
                                                                    //console.log('Opened locker '+lockerId+'.');
        
                                                                    port.write(instruction, (err) => {
                                                                        //console.log('Written instruction to locker '+lockerId+'.', instruction);
            
                                                                        if(err) { 
                                                                            log.error(err.message); 
                                                                            isResolved = true;
                                                                            reject('Instruction write error!'); 
                                                                        }else {
            
                                                                            let basic_status = false,
                                                                                instruction_response_received = false,
                                                                                response = [];

                                                                            // Receive bytedata listener
                                                                            new_single_byte.on('data', data => {
                                                                                instruction_response_received = true;
                                                                                //console.log("Received instruction response: ", data);
            
                                                                                if(response_count == 1){
                                                                                    // first byte response 
                                                                                    var hex = data.toString("hex"),
                                                                                        int = self.hex2int(hex);
            
                                                                                    if(int >= 240){
            
                                                                                        var bin = self.hex2bin(hex);
                                                                                        if (bin[5] == "1"){ 
                                                                                            self.updateLockerStatus({
                                                                                                "status" : "intrusion",
                                                                                                "response" : hex
                                                                                            }, lockerId, lockerAddress)
                                                                                            .then(() => {
                                                                                                isResolved = true;
                                                                                                if(port.isOpen) port.close(err => console.error(err));
                                                                                                resolve();
                                                                                            })
                                                                                            .catch(err => {
                                                                                                isResolved = true;
                                                                                                if(port.isOpen) port.close(err => console.error(err));
                                                                                                reject(err);
                                                                                            }); // Oops fuck call the cops?
                                                                                        } else {
                                                                                            if(port.isOpen) port.close(err => console.error(err));
                                                                                            isResolved = true;
                                                                                            resolve({
                                                                                                "status" : "no-change", // Nothing changed since last update
                                                                                                "response" : hex
                                                                                            });
                                                                                        }
            
                                                                                    }else{
                                                                                        if (hex == "8f") basic_status = "locked";
                                                                                        if (hex == "0f" || hex == "0e") basic_status = "unlocked";
                                                                                    }
                                                                                }else{
                                                                                    if (response_count != 7) response.push(data);
                                                                                }
            
                                                                                response_count++;
                                                                                if(response_count > 7){
                                                                                    port.close((err) => {
                                                                                        if (err) console.error(err);
    
                                                                                        self.confirmArrival(lockerId, lockerAddress)
                                                                                            .then(() => {
                                                                                                //console.log("Confirmed status, attempting DB save.");
    
                                                                                                self.updateLockerStatus({
                                                                                                    "status" : basic_status,
                                                                                                    "response" : Buffer.concat(response).toString("hex")
                                                                                                }, lockerId, lockerAddress)
                                                                                                    .then(() => {
                                                                                                        isResolved = true;
                                                                                                        if (port.isOpen) port.close(err => console.error(err));
                                                                                                        resolve();
                                                                                                    })
                                                                                                    .catch(err => {
                                                                                                        isResolved = true;
                                                                                                        if (port.isOpen) port.close(err => console.error(err));
                                                                                                        reject(err);
                                                                                                    });
                                                                                            })
                                                                                            .catch(err => {
                                                                                                isResolved = true;
                                                                                                if (port.isOpen) port.close(err => console.error(err));
                                                                                                resolve();
                                                                                                //console.log("Confirmation err: ", err);
                                                                                            });
                                                                                    });
                                                                                }
                                                                            });

                                                                            setTimeout(() => {
                                                                                if(!instruction_response_received) {
                                                                                    isResolved = true;
                                                                                    if (port.isOpen) port.close(err => console.error(err));
                                                                                    reject("No response");
                                                                                }
                                                                            }, 2000);
                                                                        }
                                                                    });
                                                                }
                                                            });
        
                                                        }
                                                    });

                                                    setTimeout(() => {
                                                        if(!isResolved){
                                                            if (port.isOpen) port.close(err => console.error(err));
                                                            reject("No response");
                                                        }
                                                    }, CFG.get("lockers.hangTimer"));
                                                };
        
                                                let resp_received = false;
                                                single_byte.on('data', data => {
                                                    //console.log("Received address response: ", data);
                                                    resp_received = true;

                                                    let hex = data.toString('hex');
                                                    if(response_count == 0 && hex != 'aa') reject("No locker responded"); // succesful write
                                                    else if (response_count == 0 && hex == 'aa') {
                                                        //console.log("send ins");
                                                        response_count++;
                                                        sendInstruction();
                                                    }
                                                });
                                                setTimeout(() => {
                                                    if (!resp_received) {
                                                        if (port.isOpen) port.close(err => console.error(err));
                                                        reject("No response");
                                                    }
                                                }, CFG.get("lockers.responseTimer"));
        
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

};

module.exports = self;