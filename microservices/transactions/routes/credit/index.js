const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      Credits = require( __basedir + '/models/credits' );

r.get('/get', async (req, res, next) => {  
    try{ 
        const document = await Credits.findOne({ clientId: req.query.clientId }).exec();
        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err.message);
    }
});

r.post('/new', (req, res, next) => {  
    let d = req.body; // POST query

        let client_id = d.clientId,
            card_id = d.cardId;

        let document = new Credits({clientId: client_id});
            document.save((err) => {
                if(!err){
                    return res.status(200).json({'success': true});
                }else{
                    log.error(JSON.stringify(err));
                    return res.status(500).json({'error': true, 'message': err});
                }
            });
});

r.post('/edit', async (req, res, next) => {  
    let d = req.body; // POST query

    try {
        let client_id = d.clientId,
            new_card_id = d.newCardId;

        let document = await Credits.findOne({clientId: client_id});
            document.cardId = new_card_id;

        await document.save();
        return res.status(200).json({'success': true});
    } catch (err) {
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

r.post('/delete', async (req, res, next) => {  
    let d = req.body; // POST query

    try{ 
        await Credits.deleteOne({'clientId': d.clientId});
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

r.post('/set', async (req, res, next) => {  
    let d = req.body; // POST query

    try{
        let client_id = d.clientId,
            card_id = d.cardId,
            new_value = d.newValue;

        let document = await Credits.findOne({clientId: client_id});

            document.currentValue = new_value;
            await document.save();
            return res.status(200).json({'success': true});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

module.exports = r;