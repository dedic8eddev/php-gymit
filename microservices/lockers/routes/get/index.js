const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      crons = require(__basedir + "/functions/crons"),
      LockerSettings = require( __basedir + '/models/locker_settings' );

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

r.get('/locker-settings', async (req, res) => {
    let d = req.query,
        id = d.id;

    try{ 
        let document = LockerSettings.findOne({ _id: id });
            document = await document.exec();

        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

r.get('/lockers', async (req, res) => {
    let d = req.query,
        gymId = d.gymId;

    try{ 
        let documents = LockerSettings.find({ gymId: gymId }).sort({lockerNumber: 1});
            documents = await documents.exec();

        return res.status(200).json({'success': true, 'data': documents});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

r.get('/locker', async (req, res) => {
    let d = req.query,
        gymId = d.gymId,
        lockerId = d.lockerId;

    try{ 
        let document = LockerSettings.findOne({ gymId: gymId, _id: lockerId });
            document = await document.exec();

        return res.status(200).json({'success': true, 'data': document});
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;