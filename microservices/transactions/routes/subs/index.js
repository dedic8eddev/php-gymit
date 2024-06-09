const r = require('express').Router(),
      mongoose = require('mongoose'),
      log = require(__basedir + '/config/log'),
      Subscriptions = require( __basedir + '/models/subscriptions' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc);

// Get single subscription for a user/card pair
r.get('/get', async (req, res, next) => {  
    try{ 
        const document = await Subscriptions.findOne({ clientId: req.query.clientId, gymCode: req.query.gymCode }).exec();
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
    }
});

r.get('/get-all', async (req, res, next) => {  
    let d = req.query; // query

    let data = {},
        match = {},
        sort = {},
        skip = (d.page > 1) ? (d.page-1) * parseInt(d.size) : 0,
        limit = parseInt(d.size);

    // Day
    if (d.createdOn) match.createdOn = {$gte: new Date(dayjs(d.createdOn).startOf('day').format()), $lte: new Date(dayjs(d.createdOn).endOf('day').format())};

    // Range
    if(!d.exactTime){
        if (d.from && d.to) match.createdOn = {$lte: new Date(dayjs(d.to).endOf('day').format()), $gte: new Date(dayjs(d.from).startOf('day').format())};
        if (d.from && !d.to) match.createdOn = {$gte: new Date(dayjs(d.from).startOf('day').format())};
        if (!d.from && d.to) match.createdOn = {$lte: new Date(dayjs(d.to).endOf('day').format())};
    }else{
        if (d.from && d.to) match.createdOn = {$lte: new Date(d.to), $gte: new Date(d.from)};
    }

    if (d.clientId) match.clientId = {$eq: d.clientId};
    if (d.gymId) match.gymId = {$eq: d.gymId};
    if (d.gymCode) match.gymCode = {$eq: d.gymCode};
    if (d.contractNumber) match.contractNumber = {$regex: d.contractNumber};

    // Sorting
    if(!d.sortBy){
        sort.createdOn = -1;
    }else{
        for (var field in d.sortBy) {
            sort[field] = parseInt(d.sortBy[field]);
        }
    }

    // If limit == 0 then dont limit (aka turn off pagination)
    let slicer = {'total': 1};
    if(d.page && d.size) slicer.data = {'$slice': ['$data', skip, limit]};
    else slicer.data = '$data';

    try{
        let cursor = await Subscriptions.aggregate([
            {$match: match},
            {$sort: sort},
            {$group: {
                '_id': null,
                'data': {'$push': '$$ROOT'},
                'total': { '$sum': 1 }
            }},
            {$project: slicer}
        ])
            .allowDiskUse(true)
            .cursor()
            .exec();

        await cursor.eachAsync( (doc, err) => {
            if (err) console.log(err);
            else data = doc;
        });
    
        data.success = true;

        if (typeof data.data == 'undefined'){
            data.data = []; 
            data.total = 0;
        }

        return res.status(200).json(data);

    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

// Get single subscription by its contract number
r.get('/get/:contractId', async (req, res, next) => {  
    var contract = req.params.contractId;

    try{ 
        const document = await Subscriptions.findOne({ contractNumber: contract }).exec();
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
    }
});

// Create a new Subscription based on input data    
r.post('/new', (req, res, next) => {  
    let d = req.body; // POST query

        let client_id = d.clientId,
            sub_type = d.subType,
            sub_period = d.subPeriod,
            gym_id = d.gymId,
            gym_code = d.gymCode,
            initial_transaction = d.initialTransaction,
            membership_id = d.membershipId,
            future_transactions = d.transactions;

        let document = new Subscriptions({
            gymCode: gym_code,
            clientId: client_id,
            subType: sub_type,
            subPeriod: sub_period,
            gymId: gym_id,
            membershipId: membership_id,
            transactions: []
        });

        document.transactions.push(initial_transaction); // 1st transaction
        if(typeof future_transactions != 'undefined' && future_transactions.length > 0) future_transactions.forEach(t => { document.transactions.push(t) });
        
        document.save((err) => {
                if(!err){
                    return res.status(200).json({'success': true, 'contractNumber': document.contractNumber});
                }else{
                    log.error(err);
                    return res.status(500).json({'error': true, 'message': err});
                }
            });
});

// Set sub month as cancelled
// Also move the month to the end of the sub if specified
r.post('/cancel_month', async (req, res, next) => {  
    let d = req.body; // POST query

    try {
        let contract_number = d.contractNumber,
            transaction_id = d.transactionId,
            note = typeof d.note != "undefined" ? d.note : false,
            move_month = typeof d.move_month != 'undefined' ? d.move_month : false;

        let document = await Subscriptions.findOne({contractNumber: contract_number});
        let transaction = document.transactions.id(transaction_id);
    
            transaction.cancelled = true;
            if(note != false) transaction.note = note;

        if(move_month != false){
            // sub movement occuring
            document.transactions.push(move_month);
        }
            
        await document.save();
        return res.status(200).json({'success': true});
    }catch (err) {
        log.error(err.message);
    }
});

// Set existing Subs payment as paid
r.post('/pay_transaction', async (req, res, next) => {  
    let d = req.body; // POST query

        let client_id = d.clientId,
            gymCode = d.gymCode,
            contract_number = d.contractNumber,
            transaction_id = d.transactionId;

    try {
        let document = await Subscriptions.findOne({clientId: client_id, gymCode: gymCode, contractNumber: contract_number});

        let transaction = document.transactions.id(transaction_id);
            transaction.paid = true;
        
        await document.save();
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Edit existing sub payment
r.post('/edit_transaction', async (req, res, next) => {  
    let d = req.body; // POST query

    try {
        let subId = d.subId;
        let document = await Subscriptions.findOne({'transactions._id': mongoose.Types.ObjectId(subId)});

        let transaction = document.transactions.id(subId);
            transaction.value = d.value;
            transaction.vat = d.vat;
            transaction.vat_value = d.vat_value;
        
        await document.save();
        return res.status(200).json({'success': true});
    }catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

// Remove subscription
// Switch to prepaid_card sub
r.post('/remove', async (req, res, next) => {  
    let d = req.body; // POST query

    try {
        let contractNumber = d.contractNumber,
            transaction_id = d.transactionId;
        /*
        let doc = await Subscriptions.findOne({"contractNumber": contractNumber});

        let end_time = doc.transactions.id(transaction_id);

            doc.subType = 'prepaid_card';
            doc.membershipId = 10;

        let ids = [];
        doc.transactions.forEach((tran) => {
            ids.push(tran._id);

            if(tran.end > end_time.end) {
                transaction.cancelled = true;
                transaction.note = 'Zrušení členství';
            } TODO after meetings
        });*/

        return res.status(200).json({'success': true});
    }catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});


module.exports = r;