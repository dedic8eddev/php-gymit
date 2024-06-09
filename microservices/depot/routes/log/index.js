const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Logs = require( __basedir + '/models/logs' ),
      dayjs = require('dayjs');

      const utc = require('dayjs/plugin/utc');
      dayjs.extend(utc)

// POST new Depot Log entry
r.post('/', function(req, res, next) {  
    var document = new Logs(req.body.log);
    document.save( (err, doc) => {
        if(!err){
            res.status(200).json({'success': true, 'log_id': doc._id});
        }else{
            log.error(err);
            res.status(500).json(err);
        }
    });
});

// GET all Depot logs
r.get('/', async (req, res, next) => {  
    let d = req.query; // POST query

    try{
        // Dynamic query
        let options = {};

        // filters
        if(d.from && !d.to) options.loggedOn = {$gte: new Date(dayjs(d.from).startOf('day').utc().format())};
        if(d.to && !d.from) options.loggedOn = {$lte: new Date(dayjs(d.to).endOf('day').utc().format())};   
        if(d.from && d.to) options.loggedOn = {$gte: new Date(dayjs(d.from).startOf('day').utc().format()), $lte: new Date(dayjs(d.to).endOf('day').utc().format())};
        
        if(d.loggedOn) options.loggedOn = {$gte: new Date(dayjs(d.loggedOn).utc().format()), $lte: new Date(dayjs(d.loggedOn).add(1, 'day').utc().format())};
        if(d.itemId) options.itemId = {$eq: d.itemId};
        if(d.depotId) options.depotId = {$eq: d.depotId};
        if(d.loggedBy) options.loggedBy = {$eq: d.loggedBy};
        if(d.gymId) options.gymId = {$eq: d.gymId};
        if(d.direction) options.direction = {$eq: d.direction};

        // Init query
        let documents = Logs.find(options);

        // Pagination if supplied
        if(d.limit && d.offset){
            documents.limit(parseInt(d.limit));
            documents.skip((parseInt(d.offset) - 1) * parseInt(d.limit));
        }

        // default sort
        documents.sort('-loggedOn');

        documents = await documents.exec();
        let count = await Logs.countDocuments(options).exec();
        return res.status(200).json({'success': true, 'data': documents, 'total': count});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

// GET single depot log by its ID
r.get('/:logId', async (req, res, next) => {  
    try{ 
        const document = await Logs.findById(req.params.logId);
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

r.get('/delete_all/:itemId', async (req, res, next) => {
    try{
        let item_id = req.params.itemId;
        let delete_docs = await Logs.deleteMany({itemId: item_id});

        if(!delete_docs){
            return res.status(404).json({'error': true, 'message': 'No documents deleted, perhaps no found.'});
        }else{
            return res.status(200).json({'success': true});
        }

    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

// GET depot logs for a single itemId
r.get('/item/:itemId', async (req, res, next) => {  
    let d = req.query;

    try{ 
        let item_id = req.params.itemId,
            options = {};

            options.itemId = item_id;
            if(d.from) options.loggedOn = {$gte: d.from};
            if(d.to) options.loggedOn = {$lte: d.to};        
            if(d.loggedOn){

                console.log(dayjs().utc(d.loggedOn, 'YYYY-MM-DD').format());

                options.loggedOn = {$gte: new Date(dayjs(d.loggedOn).utc().format())};   
                options.loggedOn = {$lte: new Date(dayjs(d.loggedOn).add(1, 'day').utc().format())};   
            }
            if(d.itemId) options.itemId = {$eq: d.itemId};
            if(d.depotId) options.depotId = {$eq: d.depotId};
            if(d.loggedBy) options.loggedBy = {$eq: d.loggedBy};
            if(d.gymId) options.gymId = {$eq: d.gymId};     
            if(d.direction) options.direction = {$eq: d.direction};

        let documents = Logs.find(options);
            documents.sort('-loggedOn');
            documents = await documents.exec();

        let count = await Logs.countDocuments(options).exec();

        return res.status(200).json({'success': true, 'data': documents, 'total': count});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;