// global overrides
const path = require('path');
global.__basedir = path.resolve(__dirname);

// Packages and configs
const express = require('express'),
      app = express(),
      bodyParser = require('body-parser'),
      CFG = require('config'),
      mongoose = require('mongoose'),
      morgan = require('morgan'),
      winston = require('./config/log'),
      routes = require('./routes');

// App configurations
app.set('APPSECRET', CFG.get("app.secret"));
app.use(bodyParser.urlencoded({ extended : true}));
app.use(bodyParser.json());
app.use('/', routes);
app.use(morgan(CFG.get("app.logFormat"), { stream: winston.stream }));

// MongoDB
mongoose.connect(CFG.get("db.url"), CFG.get("db.config"));
const db = mongoose.connection;
db.on('error', (err) => winston.error(err) );
db.once('open', () => winston.info('Succesfuly connected to MongoDB') );

// Start server
app.listen(CFG.get("app.port"), () => winston.info('Succesfully started transactions microservice'));