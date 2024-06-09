const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Transactions = require( __basedir + '/models/transactions' );

/**
 * GET  /delete/:transId    
 * delete a given transaction based on its id
 */
r.get('/:transId', async (req, res) => {
    try{ 
        await Transactions.deleteOne({'_id': req.params.transId});
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

module.exports = r;