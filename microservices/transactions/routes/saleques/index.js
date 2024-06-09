const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      SaleQues = require( __basedir + '/models/sale_que' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc)

// Get single que for a card
r.get('/get', async (req, res, next) => {  
    try{ 
        const document = await SaleQues.findOne({ cardId: req.query.cardId, createdOn: {$gte: new Date(dayjs(req.query.date).startOf('day').utc().format()), $lte: new Date(dayjs(req.query.date).endOf('day').utc().format())} }).exec();
        if(document) return res.status(200).json({'success': true, 'data': document});
        else return res.status(404).json({'error': true, 'message': 'No que found, you can create it.'});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Open a que for a card, based on sale/entrance
// This just creates an empty rows document in mongo, to populate it call another endpoint
r.post('/new', (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        multisport = d.multisportCard;

    let document = new SaleQues({ cardId: card_id, multisportCard: multisport });
        
    document.save((err) => {
        if(!err) return res.status(200).json({'success': true});
        else{
            log.error(err.message);
            return res.status(500).json({'error': true, 'message': err.message});
        }
    });
});

// Set que as paid
r.post('/pay-que', async (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        day = d.date;

    try {
        const document = await SaleQues.findOne({ cardId: card_id, createdOn: {$gte: new Date(dayjs(day).startOf('day').utc().format()), $lte: new Date(dayjs(day).endOf('day').utc().format())} }).exec();
              document.isPaid = true;

        await document.save();
        return res.status(200).json({'success': true});

    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});
// Setque as unpaid and to be resolved
r.post('/flag-que', async (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        day = d.date;

    try {
        const document = await SaleQues.findOne({ cardId: card_id, createdOn: {$gte: new Date(dayjs(day).startOf('day').utc().format()), $lte: new Date(dayjs(day).endOf('day').utc().format())} }).exec();
              document.isPaid = false;

        await document.save();
        return res.status(200).json({'success': true});

    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Add a single/multiple rows to the card que
r.post('/add-to-que', async (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        day = d.date,
        rows = d.rows; // properly formatted array of que rows

    try {
        const document = await SaleQues.findOne({ cardId: card_id, createdOn: {$gte: new Date(dayjs(day).startOf('day').utc().format()), $lte: new Date(dayjs(day).endOf('day').utc().format())} }).exec();

        rows.forEach((row) => {
            document.rows.push(row); // add to que
        });

        await document.save();
        return res.status(200).json({'success': true});

    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Remove a single row from a card que
r.post('/remove-from-que', async (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        day = d.date,
        row = d.row; // row _id

    try {
        await SaleQues.findOneAndUpdate({ cardId: card_id, createdOn: {$gte: new Date(dayjs(day).startOf('day').utc().format()), $lte: new Date(dayjs(day).endOf('day').utc().format())} }, {$pull: {rows: {_id: row}}}).exec();
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Edit an item in a que
r.post('/edit-in-que', async (req, res, next) => {  
    let d = req.body; // POST query

    let card_id = d.cardId,
        row_id = d.rowId;

    try{ 
        const document = await SaleQues.findOne({ cardId: card_id, createdOn: {$gte: new Date(dayjs(req.query.date).startOf('day').utc().format()), $lte: new Date(dayjs(req.query.date).endOf('day').utc().format())} }).exec();

        let item = document.rows.id(row_id);

            if (typeof d.addedBy !== "undefined") item.addedBy = d.addedBy;
            if (typeof d.amount !== "undefined") item.amount = d.amount;
            if (typeof d.itemId !== "undefined") item.itemId = d.itemId;
            if (typeof d.depotId !== "undefined") item.depotId = d.depotId;
            if (typeof d.discount !== "undefined") item.discount = d.discount;
            if (typeof d.timeSpent !== "undefined") item.timeSpent = d.timeSpent;
            if (typeof d.timeSpentPeak !== "undefined") item.timeSpentPeak = d.timeSpentPeak;
            if (typeof d.returnedBorrowedItem !== "undefined") item.returnedBorrowedItem = d.returnedBorrowedItem;
            if (typeof d.note !== "undefined") item.note = d.note;
            if (typeof d.benefitId !== "undefined") item.benefitId = d.benefitId;

        await document.save();
        res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

module.exports = r;