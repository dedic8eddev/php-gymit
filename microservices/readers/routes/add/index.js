const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      reader_api = require(__basedir + '/functions/readers'),
      CardReads = require( __basedir + '/models/card_reads' ),
      ReaderSettings = require( __basedir + '/models/reader_settings' );

r.post('/add-reader-settings', (req, res) => {
    var document = new ReaderSettings(req.body);

    document.save( (err, settings) => {
        if(!err){
            res.status(200).json({'success': true});
        }else{
            log.error(JSON.stringify(err));
            res.status(500).json(err);
        }
    });
});

r.post('/save-reader-settings', async (req, res) => {
    let d = req.body; // POST query

    try{
        let room_id = d.roomId,
            gym_id = d.gymId;

        let document = await ReaderSettings.findOne({roomId: room_id, gymId: gym_id});

            document.isPersonificator = d.isPersonificator ? d.isPersonificator : false;
            document.readerId = d.readerId;
            document.isBuildingEntrance = d.isBuildingEntrance ? d.isBuildingEntrance : false;
            document.isBuildingExit = d.isBuildingExit ? d.isBuildingExit : false;
            document.isWellness = d.isWelnness ? d.isWelnness : false;
            document.isExerciseRoom = d.isExerciseRoom ? d.isExerciseRoom : false;
            document.roomPriority = d.roomPriority;

            if(!d.pinCode){
                document.pinCode = undefined;
            }else{
                document.pinCode = String(d.pinCode).substring(0,4); // Max 4
            }

            await document.save();
            return res.status(200).json({'success': true});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;