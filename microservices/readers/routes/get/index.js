const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      reader_api = require(__basedir + '/functions/readers'),
      crons = require(__basedir + "/functions/crons"),
      CardReads = require( __basedir + '/models/card_reads' ),
      ReaderSettings = require( __basedir + '/models/reader_settings' ),
      ReaderEvent = require( __basedir + '/models/reader_events' );

r.get('/pause-crons', async (req, res) => {
    try{ 
        await crons.pause();
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

r.get('/resume-crons', async (req, res) => {
    try{ 
        await crons.init();
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});
      
r.get('/read-card/:readerId', async (req, res) => {
    let readerId = req.params.readerId,
        d = req.query;

    let options = { readerId: readerId };

    try{ 
        if(typeof d.minDate != 'undefined') options.createdOn = {$gte: d.minDate};

        let document = CardReads.findOne(options);

        document.sort('-createdOn');
        document = await document.exec();

        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }

});

r.get('/reader-settings', async (req, res) => {
    let d = req.query; // query

    try{ 
        let document = ReaderSettings.findOne({ roomId: Number(d.roomId), gymId: d.gymId });
            document = await document.exec();

        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

/**
 * Get reader events from Mongo
 * accepts time/dayúmonth
 * mandatory readerId, readerAddress
 */
r.get('/reader-events', async (req, res) => {
    let d = req.query; // query

    try{ 

        let options = {};

        if(typeof d.readerId != "undefined") options.readerId = d.readerId;
        if(typeof d.readerAddress != "undefined") options.readerId = d.readerAddress;

        // Greater than for time/day/month
        if(typeof d.time != 'undefined') options.time = {$gte: d.time};
        if(typeof d.day != 'undefined') options.day = {$gte: d.day};
        if(typeof d.month != 'undefined') options.month = {$gte: d.month};
        if(typeof d.createdOn != "undefined") options.createdOn = {$gte: new Date(d.createdOn)};

        if(typeof d.gymId != 'undefined') options.createdOn = d.gymId;
        
        let documents = ReaderEvent.find(options);
            documents.sort('-time');
            documents = await documents.exec();

        return res.status(200).json({'success': true, 'data': documents});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

/**
 * Get single reader event from MongoDB
 */
r.get('/reader-event', async (req, res) => {
    let d = req.query; // query

    try{ 

        let options = {};

        if(typeof d.readerId != "undefined") options.readerId = d.readerId;
        if(typeof d.readerAddress != "undefined") options.readerId = d.readerAddress;
        if(typeof d.cardId != "undefined") options.cardId = d.cardId;
        if(typeof d.eventStatus != "undefined") options.eventStatus = d.eventStatus;
        if(typeof d.time != 'undefined') options.time = {$gte: d.time};
        if(typeof d.day != 'undefined') options.day = {$gte: d.day};
        if(typeof d.month != 'undefined') options.month = {$gte: d.month};
        if(typeof d.createdOn != 'undefined') options.createdOn = {$gte: new Date(d.createdOn)};

        if(typeof d.gymId != 'undefined') options.createdOn = d.gymId;
        
        let document = ReaderEvent.findOne(options);

            document.sort('-time');
            document = await document.exec();

        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

/**
 * Get a list of users (cardIds) currently in the gym based on event data
 */
r.get('/present-clients', async (req, res) => {
    let d = req.query,
        day = d.date.split("-")[2],
        month = d.date.split("-")[1];

    let match = {day: day, month: month};
    if(typeof d.cardId != 'undefined') match.cardId = d.cardId;
    if(typeof d.gymId != 'undefined') match.gymId = d.gymId;

    try {
        let cursor = await ReaderEvent.aggregate([
            {
                $project: {_id: 1, day: 1, month: 1, cardId: 1, time: 1, readerId: 1, readerAddress: 1, eventStatus: 1, createdOn: 1}
            },
            {$match: match},
            {$group: {
                _id: "$cardId",
                eventId: { $last: "$_id"},
                cardId: { $last: "$cardId" },
                readerId: { $last : "$readerId" },
                readerAddress: { $last : "$readerAddress" },
                time: {$last : "$time"},
                day: {$last : "$day"},
                month: {$last : "$month"},
                eventStatus: { $last: "$eventStatus" },
                createdOn: { $last: "$createdOn" },
                events: {
                    $push : {
                        $cond: {
                            if: { $not: ["$last"] },
                            then : "$$ROOT",
                            else: ""
                        }
                    }
                }
            }},
            {$sort: {
                time: -1
            }}
        ]).allowDiskUse(true).cursor().exec();

        let data = [];
        await cursor.eachAsync(function(doc, err) {
            if(err) console.log(err);

            let obj = {};
                obj._id = doc.eventId;
                obj.cardId = doc.cardId;
                obj.readerId = doc.readerId;
                obj.readerAddress = doc.readerAddress;
                obj.eventStatus = doc.eventStatus;  
                obj.time = doc.time;
                obj.day = doc.day;
                obj.month = doc.month;
                obj.year = doc.createdOn.getYear() + 1900;

                obj.previous_events = [];
                doc.events.forEach(event => { 
                    event.year = event.createdOn.getYear() + 1900;
                    delete event.createdOn;
                    if(!event._id.equals(doc.eventId)) obj.previous_events.push(event); 
                });

            data.push(obj);
        });

        return res.status(200).json({'success': true, 'data': data});

    } catch (err) {
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;