const mongoose = require('mongoose');

const CardReadSchema = new mongoose.Schema({
    readerId: {type: String, required: true},
    cardId: {type: String, required: true},
    createdOn: {type: Date, default: Date.now, required: true}
});

const CardReads = mongoose.model('CardRead', CardReadSchema);
module.exports = CardReads;
