const r = require('express').Router();

r.use('/add', require('./add'));
r.use('/get', require('./get')); 
r.use('/edit', require('./edit')); 
r.use('/delete', require('./delete'));
r.use('/credit', require('./credit')); 
r.use('/subs', require('./subs')); 
r.use('/saleques', require('./saleques')); 
r.use('/receipts', require('./receipts')); 

module.exports = r;