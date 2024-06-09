const mongoose = require('mongoose'),
      dayjs = require('dayjs');

// Subscription internal transaction schema
// For nested transactions inside the Subscription object, to keep track of paid/unpaid transactions
const SubTransactionSchema = new mongoose.Schema({
    paid: { type: Boolean, default: false, required: true },
    cancelled: { type: Boolean, default: false, required: true },
    processedFuturePayment: { type: Boolean, default: false, required: true },
    deposit: { type: Boolean, required: false },
    gymId: { type: String, required: true },
    gymCode: { type: String, required: true },
    currency: { type: String, default: 'CZK' },
    value: { type: Number, required: true },    
    vat: { type: Number, required: true },
    vat_value: { type: Number, required: true },
    start: { type: Date, required: true },
    end: { type: Date, required: false }, // end => not required, can be a prepaid_card that has no end
    note: { type: String, required: false }
});

// The actual Subscription schema
// 
const SubSchema = new mongoose.Schema({
    contractNumber: { type: String, default: function () {
        return this.gymCode + dayjs().format('YYYY') + '000001';
        }
    }, 

    gymCode: { type: String, required: true },
    gymId: { type: String, required: true },
    clientId: { type: String, required: true },
    subType: { type: String, required: true },
    membershipId: { type: String, required: true },
    subPeriod: { type: String, required: false },
    active: { type: Boolean, required: true, default: true },
    createdOn: { type: Date, default: Date.now, required: true },
    expiresOn: { type: Date, required: false },
    transactions: { type: [SubTransactionSchema] },
    subscriptionNote: { type: String, required: false }
});

// Increment the custom trans number
SubSchema.pre('validate', async function (next) {
    let doc = this,
        year = dayjs().format('YYYY'),
        gymCode = doc.gymCode;

    const last = await mongoose.model('Subscription').findOne({'gymCode': gymCode, 'contractNumber':{"$regex": new RegExp(`${gymCode}${year}`, "i")} }, null, {sort: {'contractNumber': -1}}).exec();
    if(last){
        let last_number = parseInt(last.contractNumber.substr(-6));
            console.log(last_number);
            last_number++; // push
            console.log(last_number);

        // GYM CODE + YEAR (YYYY) + INCREMENT
        let new_number = gymCode + String(year) + String(last_number).padStart(6, '0');
        console.log(new_number);
        doc.contractNumber = new_number;
        next();
    }else{
        console.log("No autoincrement field found, skipping and letting the Schema decide.");
    }
});

const Subscription = mongoose.model('Subscription', SubSchema);
module.exports = Subscription;
