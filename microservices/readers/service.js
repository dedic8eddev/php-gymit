const path = require('path');
global.__basedir = path.resolve(__dirname);
global.Buffer = global.Buffer || require('buffer').Buffer;

// global overrides
// Packages and configs
const express = require('express'),
      app = express(),
      bodyParser = require('body-parser'),
      CFG = require('config'),
      mongoose = require('mongoose'),
      morgan = require('morgan'),
      winston = require('./config/log'),
      crons = require(__basedir + "/functions/crons"),
      reader_api = require(__basedir + '/functions/readers'),
      routes = require('./routes');

// App configurations
app.set('APPSECRET', CFG.get("app.secret"));
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
global.readers = [];
global.reader_settings = [];
global.gPorts = new Set();

reader_api.getPorts()
            .then(() => {
                console.log("Setup all ports with their respective setting objects.");
                console.log("Starting cron::");
                reader_api.startPersonificators();
                crons.init();
            })
            .catch(err => {
                console.error(err);
            });

setTimeout(() => {

    //reader_api.overwritePortForEveryRoom(); debug

    //reader_api.deregisterCard("07b201000000", "1&79f5d87&0&0001", 2);
    /*reader_api.registerNewCard("07b201000000", "1&79f5d87&0&0001", 2)
        .then(() => {
            console.log("Registered a new card!")
        });
    /*reader_api.resetReader("1&79f5d87&0&0001", 2).then(() => {
        console.log("Succesfuly reset the control unit.");
    });*/

    /* reader_api.updateTime("1&79f5d87&0&0001", 2).then(() => {
        console.log("YAY!");
    }).catch((err) => {
        console.log("FUCK!", err);
    }); */
}, 5000);

// Start server
app.listen(CFG.get("app.port"), () => winston.info('Succesfully started readers microservice'));

// Close ports on exit
process.on('exit', function () {
    console.log('Detaching com ports.');
    if(readers.length > 0) readers.forEach(reader => {
        if(typeof reader.port != "undefined") reader.port.close(err => {
            if(err) console.log(err);
            else console.log('Closed port '+reader.comName+'');
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