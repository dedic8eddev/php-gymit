const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Receipts = require( __basedir + '/models/receipts' ),
      dayjs = require('dayjs'),
      receipt_api = require(__basedir + "/functions/receipts");

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc)

// Get single receipt
r.get('/get', async (req, res, next) => {  
    try{ 
        const document = await Receipts.findOne({transactionId: req.query.transactionId}).exec();
        if(document) return res.status(200).json({'success': true, 'data': document});
        else return res.status(404).json({'error': true, 'message': 'No receipt found.'});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// add a receipt
r.post('/add', (req, res, next) => {  
    let d = req.body; // POST query

    console.log(d);

    let document = new Receipts({
        "transactionId":d.transactionId,
        "gymCode":d.gymCode,
        "data": [d.data]
    });
        
    document.save((err) => {
        if(!err) return res.status(200).json({'success': true, 'receiptNumber': document.receiptNumber});
        else{
            log.error(err.message);
            return res.status(500).json({'error': true, 'message': err.message});
        }
    });
});

// Remove a single row from a card que
r.post('/remove', async (req, res, next) => {  
    let d = req.body; // POST query
    let transId = d.transactionId;

    try {
        await Receipts.findOneAndDelete({transactionId: transId}).exec();
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Print receipt
r.post('/print', async (req, res, next) => {
    try {
        await receipt_api.print(req.body);
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Print receipt
r.get('/open_cashdesk', async (req, res, next) => {
    try {
        await receipt_api.openCashDesk();
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

module.exports = r;