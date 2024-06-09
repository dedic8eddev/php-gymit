const path = require('path');
global.__basedir = path.resolve(__dirname);
global.Buffer = global.Buffer || require('buffer').Buffer;

// global overrides
// Packages and configs
const express = require('express.oi'),
      app = express(),
      CFG = require('config'),
      mongoose = require('mongoose'),
      terminals = require(__basedir + '/functions/terminals');

// MongoDB
mongoose.connect(CFG.get("db.url"), CFG.get("db.config"));
const db = mongoose.connection;
      db.on('error', (err) => console.log(err) );
      db.once('open', () => console.log('Succesfuly connected to MongoDB') );

// Middleware for authenticating based on simple API key
const checkAuth = (token, res) => {
    return new Promise((resolve, reject) => {
        console.log("Checking key..");

        if(typeof token !== "undefined" && token.length > 0){
            if(token == CFG.get("apikey")) {
                console.log("YEP!");
                resolve();
            }
            else 
            {
                reject();
                res.status(403).json({"error":true, "message":"Permission denied."});
            }
        }else{
            reject();
            res.status(403).json({"error":true, "message":"Permission denied."});
        }
    });
};

/** SOCKETS */
app.http().io();

app.io.route("terminal", {
    abortPayment: (req, res) => {
        checkAuth()
            .then(() => {
                terminals.abortPayment(req.socket); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("terminalError", "Nepovolený přístup!");
            });
    },
    restartTerminal: (req, res) => {
        checkAuth()
            .then(() => {
                terminals.restartTerminal().then(() => {
                    req.socket.emit("restartSuccesful");
                }).catch(err => {
                    req.socket.emit("restartError", err);
                }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("restartError", "Nepovolený přístup!");
            });
    },
    handshake: (req, res) => {
        checkAuth()
            .then(() => {
                terminals.runHandshake(req.socket).then(() => {
                    req.socket.emit("handshakeSuccesful");
                }).catch(err => {
                    req.socket.emit("handshakeError", err);
                }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("handshakeError", "Nepovolený přístup!");
            });
    },
    reprint: (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data,
                system_number = d.system_number;
    
                terminals.reprintReceipt(system_number, req.socket).then(() => {
                    req.socket.emit("reprintSuccesful");
                }).catch(err => {
                    req.socket.emit("reprintError", err);
                }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("reprintError", "Nepovolený přístup!");
            });
    },
    getTransactions: (req, res) => {
        checkAuth()
            .then(() => {
                terminals.getTransactions()
                    .then((transactions) => {
                        req.socket.emit("transactionData", transactions);
                    })
                    .catch(err => req.socket.emit("terminalError", err)); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("terminalError", "Nepovolený přístup!");
            });
    },
    status: (req, res) => {
        checkAuth(req.query.token, res)
            .then(() => {
                let d = req.data,
                    connect = false,
                    socket = false;
    
                if (typeof d != "undefined" && typeof d.connect != "undefined") {
                    connect = d.connect;
                    socket = req.socket;
                }
        
                terminals.getTerminalStatus(connect, socket)
                        .then(res => {
                            socket.emit("terminalStatus", terminals.terminal_status[res]);
                        })
                        .catch(err => {
                            socket.emit("terminalError", err);
                        });
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("terminalError", "Nepovolený přístup!");
            });
    },
    settlement: (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data;

                terminals.runSettlement(req.socket)
                         .then(() => {
                            req.socket.emit("settlementComplete");
                         })
                         .catch(err => {
                            req.socket.emit("settlementError", err);
                         }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("settlementError", "Nepovolený přístup!");
            });
    },
    requestPayment: (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data;

                if(!d.amount) { 
                    req.socket.emit("errorMessage", "Missing amount parameter!");
                    return false;
                }
                if(!d.gymCode) { 
                    req.socket.emit("errorMessage", "Missing gymCode parameter!");
                    return false;
                }
                if(!d.terminalId) {
                    req.socket.emit("errorMessage", "Missing terminalId parameter!");
                    return false;
                }
    
                req.socket.emit("paymentStatus", "Starting process");
    
                terminals.requestPayment(d.amount, d.gymCode, d.terminalId, req.socket) // send the socket as well
                         .then(() => {
                            req.socket.emit("paymentComplete");
                         })
                         .catch(err => {
                            req.socket.emit("paymentError", err);
                         }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("paymentError", "Nepovolený přístup!");
            });
    },
    requestPaymentWithCashBack: (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data;

                if(!d.amount) { 
                    req.socket.emit("errorMessage", "Missing amount parameter!");
                    return false;
                }
                if(!d.cashback) { 
                    req.socket.emit("errorMessage", "Missing cashback parameter!");
                    return false;
                }
                if(!d.gymCode) { 
                    req.socket.emit("errorMessage", "Missing gymCode parameter!");
                    return false;
                }
                if(!d.terminalId) {
                    req.socket.emit("errorMessage", "Missing terminalId parameter!");
                    return false;
                }
    
                req.socket.emit("paymentStatus", "Starting process");
    
                terminals.requestPaymentWithCashback(d.amount, d.cashback, d.gymCode, d.terminalId, req.socket) // send the socket as well
                         .then(() => {
                            req.socket.emit("paymentComplete");
                         })
                         .catch(err => {
                            req.socket.emit("paymentError", err);
                         }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("paymentError", "Nepovolený přístup!");
            });
    },
    requestPaymentReverse : (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data;

                if(!d.amount) { 
                    req.socket.emit("errorMessage", "Missing amount parameter!");
                    return false;
                }
                if(!d.gymCode) { 
                    req.socket.emit("errorMessage", "Missing gymCode parameter!");
                    return false;
                }
                if(!d.terminalId) {
                    req.socket.emit("errorMessage", "Missing terminalId parameter!");
                    return false;
                }
                if(!d.sysNumber) {
                    req.socket.emit("errorMessage", "Missing sysNumber parameter!");
                    return false;
                }
                if(!d.variableSymbol) {
                    req.socket.emit("errorMessage", "Missing variableSymbol parameter!");
                    return false;
                }
        
                req.socket.emit("paymentStatus", "Starting process");
        
                terminals.requestReversePayment(d.amount, d.gymCode, d.terminalId, d.sysNumber, d.variableSymbol, req.socket)
                         .then(() => {
                            req.socket.emit("paymentComplete");
                         })
                         .catch(err => {
                            req.socket.emit("paymentError", err);
                         }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("paymentError", "Nepovolený přístup!");
            });
    },
    requestPaymentRefund : (req, res) => {
        checkAuth()
            .then(() => {
                let d = req.data;

                if(!d.amount) { 
                    req.socket.emit("errorMessage", "Missing amount parameter!");
                    return false;
                }
                if(!d.gymCode) { 
                    req.socket.emit("errorMessage", "Missing gymCode parameter!");
                    return false;
                }
                if(!d.terminalId) {
                    req.socket.emit("errorMessage", "Missing terminalId parameter!");
                    return false;
                }
        
                req.socket.emit("paymentStatus", "Starting process");
        
                terminals.requestPayment(d.amount, d.gymCode, d.terminalId, req.socket, "R") // send the socket as well
                    .then(() => {
                        req.socket.emit("paymentComplete");
                    })
                    .catch(err => {
                        req.socket.emit("paymentError", err);
                    }); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("paymentError", "Nepovolený přístup!");
            });
    },
    approveDisconnectedPayment: (req, res) => {
        checkAuth()
            .then(() => {
                let vs = req.data.variableSymbol;

                terminals.approvePayment()
                         .then(() => console.log("Approved payment => "+vs+""))
                         .catch(err => console.log(err)); 
            })
            .catch(err => {
                console.log("Unathorized access.");
                req.socket.emit("settlementError", "Nepovolený přístup!");
            });
    }

});

app.listen(CFG.get("app.port"));

// Log out any uncaught exceptions without crashing the app,
// this was added mainly because I was sometimes getting ECONNREFUSED error while connecting to a disconnected terminal..
process.on('uncaughtException', function (err) {
    console.error(err.stack);
    console.log("Node NOT Exiting...");
});