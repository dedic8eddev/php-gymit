const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      LockerSettings = require( __basedir + '/models/locker_settings' );

r.post('/locker-settings', async (req, res) => {
    let d = req.body; // POST query

    try{
        let id = d.id;

        await LockerSettings.deleteOne({_id: id});
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;