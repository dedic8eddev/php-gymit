const mongoose = require('mongoose');

const ReaderEventSchema = new mongoose.Schema({
    gymId: { type: String, required: true }, // ID klubu
    readerId: {type: String, required: true},
    readerAddress: {type: Number, required: true},
    cardId: {type: String, required: true},
    time: { type: String, required: true },
    day: { type: String, required: true },
    month: { type: String, required: true },
    eventStatus: { type: String, required: true },
    createdOn: {type: Date, default: Date.now, required: true}
});

const readerEvents = mongoose.model('ReaderEvent', ReaderEventSchema);
module.exports = readerEvents;
