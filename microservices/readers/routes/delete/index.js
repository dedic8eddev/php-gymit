const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      reader_api = require(__basedir + '/functions/readers'),
      CardReads = require( __basedir + '/models/card_reads' ),
      ReaderSettings = require( __basedir + '/models/reader_settings' );

r.post('/reader-settings', async (req, res) => {
    let d = req.body; // POST query

    try{
        let room_id = d.roomId,
            gym_id = d.gymId;

        await ReaderSettings.deleteOne({roomId: room_id, gymId: gym_id});
        return res.status(200).json({'success': true});
    }catch(err){
        log.error(err);
        return res.status(500).json({'error': true, 'message': err});
    }
});

module.exports = r;