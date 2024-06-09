const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      locker_api = require(__basedir + "/functions/lockers"),
      LockerSettings = require( __basedir + '/models/locker_settings' );

r.get('/remote-open-locker', async (req, res) => {
    let d = req.query,
        lockerId = d.lockerId;

    try{ 
        let document = LockerSettings.findOne({ _id: lockerId });
            document = await document.exec();

        if (document) {
            await locker_api.remoteOpenLocker(document.lockerId, document.lockerAddress);
            return res.status(200).json({'success': true});
        }else{
            return res.status(404).json({'error': true, 'message': "No locker found!"});
        }
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

r.get('/remote-open-locker-with-card', async (req, res) => {
    let d = req.query,
        lockerId = d.lockerId,
        cardId = d.cardId;

    try{ 
        let document = LockerSettings.findOne({ _id: lockerId });
            document = await document.exec();

        if (document) {
            await locker_api.remoteOpenLockerWithCard(cardId, document.lockerId, document.lockerAddress);
            return res.status(200).json({'success': true});
        }else{
            return res.status(404).json({'error': true, 'message': "No locker found!"});
        }
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

r.get('/add-mastercards-to-locker', async (req, res) => {
    let d = req.query,
        lockerId = d.lockerId,
        masterCard1 = d.masterCard1,
        masterCard2 = d.masterCard2;

    try{ 
        let document = LockerSettings.findOne({ _id: lockerId });
            document = await document.exec();

        if (document) {

            document.masterCards = [masterCard1, masterCard2];

            await locker_api.addMastercardsToLocker(masterCard1, masterCard2, lockerId, document.lockerAddress);
            await document.save(); // save only if the top await was success

            return res.status(200).json({'success': true});
        }else{
            return res.status(404).json({'error': true, 'message': "No locker found!"});
        }
    }catch(err){
        log.error(err.message);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;