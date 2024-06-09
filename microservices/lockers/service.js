const path = require('path');
global.__basedir = path.resolve(__dirname);
global.Buffer = global.Buffer || require('buffer').Buffer;

// global overrides
// Packages and configs
const express = require('express'),
      app = express(),
      helmet = require('helmet'),
      bodyParser = require('body-parser'),
      CFG = require("config"),
      mongoose = require('mongoose'),
      morgan = require('morgan'),
      crons = require(__basedir + '/functions/crons'),
      winston = require('./config/log'),
      locker_api = require(__basedir + '/functions/lockers'),
      routes = require('./routes');

// App configurations
app.set('APPSECRET', CFG.get("app.secret"));

app.use(helmet());
app.use(bodyParser.urlencoded({ extended : true}));
app.use(bodyParser.json());

// Middleware for authenticating based on simple API key
const authReq = (req, res, next) => {
    let header_key = req.header('X-Api-Key');
    if(typeof header_key !== "undefined" && header_key.length > 0){
        if(header_key == CFG.get("apikey")) {
            next();
        }
        else 
        {
            res.status(403).json({"error":true, "message":"Permission denied."});
        }
    }else{
        res.status(403).json({"error":true, "message":"Permission denied."});
    }
};

app.use(authReq); // use the middleware for the whole app

app.use('/', routes);
app.use(morgan(CFG.get("app.logFormat"), { stream: winston.stream }));

// MongoDB
mongoose.connect(CFG.get("db.url"), CFG.get("db.config"));
const db = mongoose.connection;
db.on('error', (err) => winston.error(err) );
db.once('open', () => winston.info('Succesfuly connected to MongoDB') );

// Init readers
global.lockers = []; // GLOBAL lockers array
global.lockers_settings = {}; // GLOBAL settings object with arrays
global.gPorts = new Set();  // GLOBAL ports set

locker_api.getPorts()
          .then(() => {
              console.log("Setup all ports with their respective setting objects.");
              console.log("Starting cron::");
              crons.init();
          })
          .catch(err => {
              console.log("Error setting up ports: ", err);
          });

setTimeout(() => {

    //console.log(lockers_settings["COM10"]);

    //locker_api.getLockerStatus("COM11", 2).then((d) => console.log("yay!", d)).catch(err => console.error("nay!", err));
    
    // debug db population yay
    //locker_api.populateDbWithLockers("male").then(() => console.log("yay male!")).catch(err => console.log(err));
    //locker_api.populateDbWithLockers("female").then(() => console.log("yay female!")).catch(err => console.log(err));
    
    /*locker_api.getLockerStatus("COM3", 1)
              .then((data) => {
                console.log("Yay!", data);
              }).catch(err => {
                console.log("Locker status error: ", err);
              });*/
    /* locker_api.remoteOpenLocker("COM3", 1)
              .then(() => console.log("Yay!"))
              .catch(err => console.log(err)); */
    /* locker_api.deleteCardFromLocker("151605000000", "COM3", 1)
              .then(() => console.log("Yay!"))
              .catch(err => console.log(err)); */

    //locker_api.getLockerStatus("COM3", 1).then(() => {console.log("ohyah")}).catch(err => {console.log(err)});

    //locker_api.getLocker("COM3", 1).then((type) => { console.log("This locker is "+type); }).catch(err => console.log(err));
}, 2000);

// Start server
app.listen(CFG.get("app.port"), () => winston.info('Succesfully started lockers microservice'));

// Close ports on exit
process.on('exit', function () {
    console.log('Detaching com ports.');
    if(lockers.length > 0) lockers.forEach(locker => {
        if(typeof locker.port != "undefined") locker.port.close(err => {
            if(err) console.log(err);
            else console.log('Closed port '+locker.serialNum+'');
        });
    });
});

process.on('SIGINT', function () {
    process.exit(2);
});

process.on('uncaughtException', function(e) {
    console.log('Uncaught Exception:');
    console.log(e.stack);
    process.exit(99);
});