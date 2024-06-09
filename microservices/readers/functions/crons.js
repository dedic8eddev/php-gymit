const log = require(__basedir + '/config/log'),
      reader_api = require(__basedir + '/functions/readers'),
      CFG = require("config"),
      Agenda = require('agenda');

const crons = {
    agenda: null,
    paused: false,
    define: () => {

        // init
        crons.agenda = new Agenda({
            db: {address: CFG.get("db.url"), collection: 'reader-crons'},
            processEvery: '1 minute'
        });

        // locker pulling
        crons.agenda.define('Pull all reader data', {priority: 'high', concurrency: 1, lockLimit: 1}, (job, done) => {
            const {comport, roomPriority} = job.attrs.data;
        
            reader_api.processReaderData(reader_settings[comport], roomPriority)
                        .then((result) => {
                            console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+comport+"] Processed "+result+" readers.");
                            done();
                        })
                        .catch(err => {
                            console.error(err);
                        });
        });
        

        // events
        crons.agenda.on('success:Pull all reader data', job => {
            const {comport} = job.attrs.data;
            console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+comport+"] Finished pulling reader events")
        });
        crons.agenda.on('fail:Pull all reader data', (err, job) => {
            log.error(`Job [${job.attrs.name}] error: ${err}`);
            console.log(`Reader pulling failed with error: ${err.message}`);
        });
        crons.agenda.on('start', job => {
            console.log(`Job [${job.attrs.name}] starting`);
        });
        crons.agenda.on('complete', job => {
            console.log(`Job [${job.attrs.name}] finished`);
        });
    },
    init: async () => {
        crons.paused = false;
        await crons.agenda.start();
        await crons.agenda.every("1 minute", 'Pull all reader data', {comport: CFG.get("readers.readerPort"), roomPriority: 1}); // client access
        await crons.agenda.every("15 minutes", 'Pull all reader data', {comport: CFG.get("readers.readerPort"), roomPriority: 2}); // employee
        await crons.agenda.every("1 hour", 'Pull all reader data', {comport: CFG.get("readers.readerPort"), roomPriority: 3}); // misc
    },
    pause: async () => {
        crons.paused = true;
        await crons.agenda.stop();
    }
};

crons.define();
module.exports = crons;