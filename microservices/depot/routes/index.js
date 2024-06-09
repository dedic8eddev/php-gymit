const r = require('express').Router();

r.use('/log', require('./log'));

module.exports = r;