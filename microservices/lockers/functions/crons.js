const log = require(__basedir + '/config/log'),
      locker_api = require(__basedir + '/functions/lockers'),
      CFG = require('config'),
      Agenda = require('agenda');

const crons = {
    agenda: null,
    paused: false,
    define: () => {

        // init
        crons.agenda = new Agenda({
            db: {address: CFG.get("db.url"), collection: 'locker-crons'},
            processEvery: '1 minute'
        });

        // locker pulling
        crons.agenda.define('Pull all lockers status', {priority: 'high', concurrency: 2, lockLimit: 2}, (job, done) => {
            const {comport} = job.attrs.data;
        
            locker_api.processLockerStatuses(lockers_settings[comport])
                        .then((result) => {
                            console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+comport+"] Processed "+result+" lockers.");
                            done();
                        })
                        .catch(err => {
                            console.error(err);
                        });
        });
        

        // events
        crons.agenda.on('success:Pull all lockers status', job => {
            const {comport} = job.attrs.data;
            console.log("["+(new Date()).toLocaleString("cs-CZ")+"]["+comport+"] Finished pulling status")
        });
        crons.agenda.on('fail:Pull all lockers status', (err, job) => {
            log.error(`Job [${job.attrs.name}] error: ${err}`);
            console.log(`Lockers pulling failed with error: ${err.message}`);
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
        const femalePortChecks = crons.agenda.create("Pull all lockers status", {comport: CFG.get("lockers.femalePort")});
        const malePortChecks = crons.agenda.create("Pull all lockers status", {comport: CFG.get("lockers.malePort")});
    
        await crons.agenda.start(); // Start cron jobs
    
        await femalePortChecks.repeatEvery("3 minutes").save(); // FEMALE LOCKERS
        await malePortChecks.repeatEvery("3 minutes").save(); // MALE LOCKERS
    },
    pause: async () => {
        crons.paused = true;
        await crons.agendar.stop();
    }
};

crons.define();
module.exports = crons;