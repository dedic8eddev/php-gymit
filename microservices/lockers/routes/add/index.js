const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      LockerSettings = require( __basedir + '/models/locker_settings' );

r.post('/add-locker-settings', (req, res) => {
    var document = new LockerSettings(req.body);

    document.save( (err, settings) => {
        if(!err){
            res.status(200).json({'success': true});
        }else{
            log.error(JSON.stringify(err));
            res.status(500).json(err);
        }
    });
});

r.post('/save-locker-settings', async (req, res) => {
    let d = req.body; // POST query

    try{
        let id = d.id;

        let document = await LockerSettings.findOne({_id: id});

            document.readerId = d.lockerId;
            document.readerId = d.lockerAddress;

            await document.save();
            return res.status(200).json({'success': true});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;