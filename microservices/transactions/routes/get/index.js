const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Transactions = require( __basedir + '/models/transactions' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
            dayjs.extend(utc);

/**
 * GET  /get/
 * Returns all available records
 * Accepts POST, 
 * $_POST['from'] && $_POST['to'] for date range
 * $_POST['limit'] && $_POST['offset'] for pagination
 * $_POST['category'] for filtering transactions based on category
 * $_POST['client_id'] for filtering based on client_id
 * $_POST['card_id'] for filtering based on card id
 * $_POST['gym_id'] for filtering based on gym
 */
r.get('/', async (req, res, next) => {  
    let d = req.query; // query

    let data = {},
        match = {},
        sort = {},
        skip = (d.page > 1) ? (d.page-1) * parseInt(d.size) : 0,
        limit = parseInt(d.size);

    // Day
    if (d.paidOn) match.paidOn = {$gte: new Date(dayjs(d.paidOn).startOf('day').format()), $lte: new Date(dayjs(d.paidOn).endOf('day').format())};

    // Range
    if(!d.exactTime){
        if (d.from && d.to) match.paidOn = {$lte: new Date(dayjs(d.to).endOf('day').format()), $gte: new Date(dayjs(d.from).startOf('day').format())};
        if (d.from && !d.to) match.paidOn = {$gte: new Date(dayjs(d.from).startOf('day').format())};
        if (!d.from && d.to) match.paidOn = {$lte: new Date(dayjs(d.to).endOf('day').format())};
    }else{
        if (d.from && d.to) match.paidOn = {$lte: new Date(d.to), $gte: new Date(d.from)};
    }

    // Misc
    if(d.transCategory){
        if (typeof d.transCategory == 'string') match.transCategory = {$eq: d.transCategory};
        else match.transCategory = {$in: d.transCategory};
    }

    if (d.client_id) match.clientId = {$eq: d.client_id};
    if (d.card_id) match.cardId = {$eq: d.card_id};
    if (d.gym_id) match.gymId = {$eq: d.gym_id};
    if (d.gymCode) match.gymCode = {$eq: d.gymCode};
    if (d.transactionNumber) match.transactionNumber = {$regex: d.transactionNumber};
    if (d.locked) match.locked = {$eq: true};
    if (d.clientId) match.clientId = {$eq: d.clientId};
    if (d.employeeId) match.employeeId = {$eq: d.employeeId};

    // Sorting
    if(!d.sortBy){
        sort.paidOn = -1;
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
        console.log(match)
        let cursor = await Transactions.aggregate([
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

r.get('/purchase-history', async (req,res) => {
    let d = req.query; // query

    let data = {},
        match = {},
        post_match = {},
        sort = {},
        skip = (d.page > 1) ? (d.page-1) * parseInt(d.size) : 0,
        limit = parseInt(d.size);

    // Day
    if (d.paidOn) match.paidOn = {$gte: new Date(dayjs(d.paidOn).startOf('day').format()), $lte: new Date(dayjs(d.paidOn).endOf('day').format())};

    // Range
    if (d.from && d.to) match.paidOn = {$lte: new Date(dayjs(d.to).endOf('day').format()), $gte: new Date(dayjs(d.from).startOf('day').format())};
    if (d.from && !d.to) match.paidOn = {$gte: new Date(dayjs(d.from).startOf('day').format())};
    if (!d.from && d.to) match.paidOn = {$lte: new Date(dayjs(d.to).endOf('day').format())};

    // Misc
    if(d.transCategory){
        if (typeof d.transCategory == 'string') match.transCategory = {$eq: d.transCategory};
        else match.transCategory = {$in: d.transCategory};
    }

    if (d.card_id) match.cardId = {$eq: d.card_id};
    if (d.gym_id) match.gymId = {$eq: d.gym_id};
    if (d.gymCode) match.gymCode = {$eq: d.gymCode};
    if (d.transactionNumber) match.transactionNumber = {$regex: d.transactionNumber};
    if (d.locked) match.locked = {$eq: true};
    if (d.employeeId) match.employeeId = {$eq: d.employeeId};

    if (d.itemId){
        post_match['items.itemId'] = {$eq: parseInt(d.itemId)};
        if(typeof d.filterDepotItem != 'undefined') post_match['items.depotId'] = {$exists: true};
        else post_match['items.depotId'] = {$exists: false};
    }

    // Sorting
    if(!d.sortBy){
        sort.paidOn = -1;
    }else{
        for (var field in d.sortBy) {
            sort[field] = parseInt(d.sortBy[field]);
        }
    }

    match.clientId = {$eq: d.clientId}; // client
    match.items = {$exists: true, $ne: []}; // items not empty

    // If limit == 0 then dont limit (aka turn off pagination)
    let slicer = {'total': 1};
    if(d.page && d.size) slicer.data = {'$slice': ['$data', skip, limit]};
    else slicer.data = '$data';

    try{
        console.log(match, post_match)
        let cursor = await Transactions.aggregate([
            {$match: match},
            {$unwind: "$items" },
            {$match: post_match},
            {$sort: sort},
            {$project: {
                'items.paidOn': '$paidOn',
                'items.itemId': '$items.itemId',
                'items.amount': '$items.amount',
                'items.value': '$items.value',
                'items.vat': '$items.vat',
                'items.vat_value': '$items.vat_value',
                'items.depotId': '$items.depotId',
            }},
            {$group: {
                '_id': null,
                'data': {'$push': '$items'},
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

/**
 * GET  /get/:transId
 * Returns a single JSON transaction for given transaction ID
 */
r.get('/:transId', async (req, res) => {
    try{ 
        const document = await Transactions.findById(req.params.transId);
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

/**
 * GET  /get/number/:transNumber
 * Returns a single JSON transaction for given transaction number
 */
r.get('/number/:transNumber', async (req, res) => {
    let d = req.query,
        gymCode = d.gymCode;

    try{ 
        const document = await Transactions.findOne({"transactionNumber":req.params.transNumber, "gymCode":gymCode});
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

/**
 * GET  /get/invoice/:invoiceId
 * Returns a single JSON transaction for given transaction number
 */
r.get('/invoice/:invoiceId', async (req, res) => {
    let d = req.query,
        gymCode = d.gymCode;

    try{ 
        const document = await Transactions.findOne({"subscriptionContractNumber":req.params.invoiceId, "gymCode":gymCode});
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;