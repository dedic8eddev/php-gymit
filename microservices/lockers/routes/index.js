const r = require('express').Router();

r.use('/get', require('./get'));
r.use('/add', require('./add'));
r.use('/delete', require('./delete'));
r.use('/lockers', require('./lockers'));

module.exports = r;