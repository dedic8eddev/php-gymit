const mongoose = require('mongoose'),
      dayjs = require('dayjs');

const itemSchema = new mongoose.Schema({
    itemId: {type: Number, required: true}, // id položky
    depotId: {type: Number, required: false}, // id skladu (pokud se jedná o skladovou položku)
    isOvertime: {type: Boolean, default: false}, // Jedná se o přesčas v dané službě?
    addedBy: {type: Number, required: false}, // ID uživatele co přidal item do transakce
    amount: {type: Number, required: true, default: 1}, // Množství
    value: {type: Number, required: true}, // částka s DPH
    value_discount: { type: Number, required: false },
    vat_value: {type: Number, required: true}, // částka DPH
    vat: { type: Number, required: true } // % vat
});

const TransactionSchema = new mongoose.Schema({
    transactionNumber: { type: String, default: function () {
            // First
            return this.gymCode + dayjs().format('YY') + dayjs().format('MM') + '000001';
        } 
    },

    parentTransaction: { type: mongoose.ObjectId, required: false}, // ObjectId nadřazené transakce (např. v případě rozdělení metody platby)
    
    paid: { type: Boolean, default: true, required: true }, // Zaplaceno? (BA převody, FA platb y => čekají na potvrzení!)
    paidOn: { type: Date, default: Date.now, required: true }, // Kdy zaplaceno

    gymId: { type: String, required: true }, // ID klubu
    gymCode: { type: String, required: true }, // Kód klubu (01, 02, 03,..)
    transCategory: { type: String, default: 'PK' }, // Kategorie transakce (string)
    transType: { type: Number, required: true }, // Typ transakce
    
    items: { type: [itemSchema], required: false },
    invoiceItems: { type: [{

        item_id: {type: Number, required: false},
        item_type: {type: String, required: true},
        item_name: {type: String, required: true},
        item_value: {type: Number, required: true},
        item_discount: {type: Number, required: true, default: 0},
        item_amount: {type: Number, required: true, default: 1},
        autocont_account: {type: String, required: false}

    }], required: false }, // items specific to invoices (for now)

    subscriptionPayment: { type: Boolean, required: true, default: false}, // Jedná se o platbu za členství?
    subscriptionContractNumber: { type: String, required: false }, // Číslo smlouvy pro členství
    subscriptionSubPaymentId: { type: [String], required: false },

    voucherSale: { type: Boolean, required: true, default: false }, // Jedná se o nákup voucheru?
    voucherIdentification: { type: [String], required: false }, // Pokud ano => id voucheru (var. symbol)

    marketingSale: { type: Boolean, required: false }, // Marketingový prodej

    refund: { type: String, required: true, default: false }, // storno ? refundace
    refundBankAccount: { type: String, required: false }, // bankovní účet pro vrácení peněz (pokud má být vráceno na bankovní účet)

    terminalId: { type: String, required: false }, // Id terminálu
    clientId: { type: String, required: true }, // Id kupujícího klienta
    employeeId: { type: String, required: true }, // Id zaměstnance provádějícího platbu
    
    invoiceId: { type: String, required: false }, // Id faktury
    paymentIdentificationNumber: { type: 'String', required: false }, // Variabilní symbol pro transakce provedené skrze banku (?) / faktury

    disposablePayment: { type: Boolean, required: false }, // Transakce bez členské karty ?
    cardId: { 
        type: String, 
        required: function() { return (this.disposablePayment === null || this.disposablePayment === false) } // require if disposablePayment is false or not set
    }, // Id klubové karty
    currency: { type: String, default: 'CZK' }, // Měna, default CZK
    
    value: { type: Number, required: true }, // Hodnota transakce s DPH
    value_discount: { type: Number, required: false },
    vat: { type: Number, required: false }, // % hodnota DPH
    vat_value: { type: Number, required: false }, // částka DPH

    text: { type: String, required: false },
    locked: { type: Boolean, required: true, default: false }
});

// Increment the custom trans number
TransactionSchema.pre('validate', async function (next) {
    let doc = this,
        year = dayjs().format('YY'),
        month = dayjs().format("MM"),
        gymCode = doc.gymCode;

    const last = await mongoose.model('Transaction').findOne({'gymCode': gymCode, 'transactionNumber':{"$regex": new RegExp(`${gymCode}${year}${month}`, "i")} }, null, {sort: {'transactionNumber': -1}}).exec();
    if(last){
        let last_number = parseInt(last.transactionNumber.substr(-6));
            console.log(last_number);
            last_number++; // push
            console.log(last_number);

        // GYM CODE + YEAR (YY) + INCREMENT
        let new_number = gymCode + String(year) + String(month) + String(last_number).padStart(6, '0');
        console.log(new_number);
        doc.transactionNumber = new_number;
        next();
    }else{
        console.log("No autoincrement field found, skipping and letting the Schema decide.");
    }
});

const Transaction = mongoose.model('Transaction', TransactionSchema);
module.exports = Transaction;

