'use strict';

var PERSONIFICATORS = PERSONIFICATORS || (function () {
    return {
        cardLoader: $('#cardLoader'),
        workers: [],
        startPolling: function(readerId, callback, interval){
            interval = interval || 1250;
            console.log('Polling reader '+readerId+' at '+interval+'ms.');
            PERSONIFICATORS.cardLoader.addClass('spin');

            var now = moment().utc().format('DD-MM-HH-mm-ss');
            var now_format = moment().utc().toISOString();

            PERSONIFICATORS.workers[readerId] = setInterval(function () {
                GYM._post('/admin/dashboard/read_personificator_data', {readerId: readerId, date: now_format}).done(function(res){
                    if(res && !res.error){
                        var read_time = moment(res.createdOn).utc().format('DD-MM-HH-mm-ss');
                        console.log(now, 'vs', read_time);
                        if(read_time > now) {
                            callback(res.cardId);
                        }
                    }
                });
            }, interval);
        },
        stopPolling: function(readerId){
            console.log('Stopping polling of reader '+readerId+'.');
            PERSONIFICATORS.cardLoader.removeClass('spin');
            
            if(typeof PERSONIFICATORS.workers[readerId] != 'undefined') clearInterval(PERSONIFICATORS.workers[readerId]);
            return true;
        },
        getSessionReader: function(){
            return localStorage.getItem('personificator');
        },
        setSessionReader: function(readerId){
            localStorage.setItem('personificator', readerId);
            return true;
        },
        chooseSessionReader: async () => {
            let body_res = await fetch("/admin/cards/get_personificators_ajax"),
                body = await body_res.text(),
                footer = '<button class="btn btn-primary">Potvrdit</button>',
                next = false;

            GYM._createDynamicModal('#chooseReaderModal', 'modal-dialog-centered', 'Výběr čtečky karet', body, footer);
            $('#chooseReaderModal').find('.modal-header button.close').hide();
            $('#chooseReaderModal').show();

            // wait till user input
            const timeout = async ms => new Promise(res => setTimeout(res, ms));
            const wait4UserInput = async () => {
                while (next === false) await timeout(100); // pause script but avoid browser to freeze ;)
                next = false; // reset var
                //console.log('user input detected');
            }

            const getReaderId = async () => {
                await wait4UserInput();
                $('#chooseReaderModal').modal('hide');
                let readerId = $('#reader_id').val();
                PERSONIFICATORS.setSessionReader(readerId);
                return readerId;
            }

            $('#chooseReaderModal button').click(() => next = true)
            return getReaderId();
        },
    }
}());