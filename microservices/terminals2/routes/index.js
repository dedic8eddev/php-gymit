const r = require('express').Router();

r.use('/get', require('./get'));

module.exports = r;