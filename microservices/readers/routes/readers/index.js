const r = require('express').Router(),
      log = require(__basedir + '/config/log'),
      reader_api = require(__basedir + '/functions/readers'),
      CardReads = require( __basedir + '/models/card_reads' ),
      ReaderSettings = require( __basedir + '/models/reader_settings' );

// Test endpoint, registers card in reader
r.post('/register-card', async (req, res) => {
    let d = req.body, // POST query
        cardId = d.cardId,
        readerId = d.readerId,
        readerAddress = d.readerAddress;

    reader_api.registerNewCard(cardId, readerId, readerAddress).then(() => {
        return res.status(200).json({'success': true});
    }).catch(err => {
        return res.status(500).json({'error': true, 'message': err});
    });
});

// Test endpoint, removes card from reader
r.post('/remove-card', async (req, res) => {
    let d = req.body, // POST query
        cardId = d.cardId,
        readerId = d.readerId,
        readerAddress = d.readerAddress;

    reader_api.deregisterCard(cardId, readerId, readerAddress).then(() => {
        return res.status(200).json({'success': true});
    }).catch(err => {
        return res.status(500).json({'error': true, 'message': err});
    });
});

// DEBUG endpoint, pull data from reader to DB
r.post('/pull-events', async (req, res) => {
    let d = req.body, // POST query
        readerId = d.readerId,
        readerAddress = d.readerAddress;

    reader_api.getEventData(readerId, readerAddress).then(() => {
        return res.status(200).json({'success': true});
    }).catch(err => {
        return res.status(500).json({'error': true, 'message': err});
    });
});

// Reset reader
r.post('/reset', async (req, res) => {
    let d = req.body, // POST query
        readerId = d.readerId,
        readerAddress = d.readerAddress;

    reader_api.resetReader(readerId, readerAddress).then(() => {
        return res.status(200).json({'success': true});
    }).catch(err => {
        return res.status(500).json({'error': true, 'message': err});
    });
});

// Reset reader
r.post('/reset-readers', async (req, res) => {
    let d = req.body, // POST query
        readerList = d.readerList;

    try {

        let readerToPromise = (v) => reader_api.resetReader(v.reader_id, v.address),
            promises = readerList.map(readerToPromise);

        await Promise.all(promises);
        return res.status(200).json({'success': true});

    } catch (err) {
        return res.status(500).json({'error': true, 'message': err.message});
    }
});

module.exports = r;