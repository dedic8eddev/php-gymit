const mongoose = require('mongoose');

// Schema for a single que row
const rowSchema = new mongoose.Schema({
    itemId: {type: Number, required: true}, // id položky
    depotId: {type: Number, required: false}, // id skladu (pokud se jedná o skladovou položku / pokud ne => pricelist)
    amount: {type: Number, required: true, default: 1}, // Množství
    discount: {type: Number, required: false},
    benefitId: {type: Number, required: false},
    timeSpent: {type: Number, required: false},
    timeSpentPeak: {type: Number, required: false},
    returnedBorrowedItem: {type: Boolean, required: false}, // vrácení půjčené položky
    addedBy: {type: Number, required: false}, // user Id
    note: {type: String, required: false}
});

const queSchema = new mongoose.Schema({
    createdOn: { type: Date, default: Date.now, required: true }, // Vytvoření fronty
    cardId: { type: String, required: true },
    rows: { type: [rowSchema], required: true },
    multisportCard: { type: Boolean, required: true, default: false },
    isPaid: { type: Boolean, required: true, default: true } // Je fronta zaplacená? má se řešit?
});

const SaleQue = mongoose.model('SaleQue', queSchema);
module.exports = SaleQue;