const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      mongoose = require('mongoose'),
      Transactions = require( __basedir + '/models/transactions' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc);

r.post('/', function(req, res, next) {  
    var document = new Transactions(req.body.transaction);
    document.save( (err, transaction) => {
        if(!err){
            res.status(200).json({'success': true, 'transaction_id': transaction._id, 'transaction_number': transaction.transactionNumber});
        }else{
            log.error(err.message);
            res.status(500).json(err);
        }
    });
});

r.post('/close-transactions', async (req, res, next) => {  
    let d = req.body,
        transactions = d.transactions;

    const transactionToId = (t) => mongoose.Types.ObjectId(t._id),
          ids = transactions.map(transactionToId);

    try {
        const r = await Transactions.updateMany({_id: { $in: ids } }, {locked: true});
        res.status(200).json({'success': true, 'modified':r.nModified});
    } catch (err) {
        log.error(err.message);
        res.status(500).json({'error': true});
    }
});

r.post('/close-transaction-day', async (req, res, next) => {  
    let d = req.body,
        day = d.day;

    try {
        const r = await Transactions.updateMany({paidOn: { $gte: new Date(dayjs(day).format()), $lte: new Date(dayjs(day).endOf('day').format()) } }, {locked: true});
        res.status(200).json({'success': true, 'modified':r.nModified});
    } catch (err) {
        log.error(err.message);
        res.status(500).json({'error': true});
    }
});

module.exports = r;