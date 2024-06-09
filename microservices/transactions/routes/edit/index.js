const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Transactions = require( __basedir + '/models/transactions' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc);

/**
 * POST  /edit/:transId
 * Save new transaction values
 */
r.post('/:transId', async (req, res) => {

    let transaction = req.body.transaction,
        transId = req.params.transId;

    try{ 
        const document = await Transactions.findByIdAndUpdate(transId, {$set: transaction});
        res.status(200).json({'success': true, 'transaction_id': document._id, 'transaction_number': document.transactionNumber});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;