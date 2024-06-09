const mongoose = require('mongoose');

const DepotLogSchema = new mongoose.Schema({
    gymId: { type: String, required: true }, // ID klubu
    depotId: { type: Number, required: true }, // Id skladu
    itemId: { type: Number, required: true}, // Id skladové položky
    amount: { type: Number, required: true }, // Množství
    buyPrice: { type: Number, required: false }, // Nákupní cena
    salePrice: { type: Number, required: false }, // Prodejní cena
    invoiceId: { type: String, required: false }, // Identifikátor faktury (pro naskladnění s fakturou)
    note: { type: String, required: false }, // Poznámka
    direction: { type: String, required: true }, // Směr pohybu ( to / from / new / sale )
    loggedOn: { type: Date, default: Date.now, required: true }, // Datum/čas změny
    loggedBy: { type: String, required: true }, // Id zaměstnance provádějícího pohyb
});

const Logs = mongoose.model('DepotLog', DepotLogSchema);
module.exports = Logs;
