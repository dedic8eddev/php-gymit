const mongoose = require('mongoose'),
      dayjs = require('dayjs');

const receiptSchema = new mongoose.Schema({
    receiptNumber: { type: String, default: function () {
            return this.gymCode + dayjs().format('YY') + dayjs().format('MM') + '000001';
        }
    },
    gymCode: { type: String, required: true },
    createdOn: { type: Date, default: Date.now, required: true },
    data: { type: [mongoose.Mixed] },
    transactionId: { type: mongoose.ObjectId, required: true }
});

receiptSchema.pre('validate', async function (next) {
    let doc = this,
        year = dayjs().format('YY'),
        month = dayjs().format("MM"),
        gymCode = doc.gymCode;

    const last = await mongoose.model('Receipt').findOne({'gymCode': gymCode, 'receiptNumber':{"$regex": new RegExp(`${gymCode}${year}${month}`, "i")} }, null, {sort: {'receiptNumber': -1}}).exec();
    if(last){
        let last_number = parseInt(last.receiptNumber.substr(-6));
            console.log(last_number);
            last_number++; // push
            console.log(last_number);

        // GYM CODE + YEAR (YY) + INCREMENT
        let new_number = gymCode + String(year) + String(month) + String(last_number).padStart(6, '0');
        console.log(new_number);
        doc.receiptNumber = new_number;
        next();
    }else{
        console.log("No autoincrement field found, skipping and letting the Schema decide.");
    }
});

const Receipts = mongoose.model('Receipt', receiptSchema);
module.exports = Receipts;