const mongoose = require('mongoose');

const CardSettlementSchema = new mongoose.Schema({
    requestStatus: {type: Boolean, required: true},
    responseLog: [Buffer], // "Log" of big responses
    batchNum: {type: String, required: false},
    pid: {type: String, required: false},
    requestedOn: {type: Date, default: Date.now} // When was this requested to go through
});

const CardSettlement = mongoose.model("Cardsettlement", CardSettlementSchema);
module.exports = CardSettlement;