const mongoose = require('mongoose'),
      dayjs = require('dayjs');

const CardSwipeSchema = new mongoose.Schema({
    systemNumber: { // Unique sys num
        type: Number,
        default: 1
    },
    gymCode: { type: String, required: true },
    variableSymbol: { type: String, default: function () {
        // First
        return this.gymCode + dayjs().format('YY') + dayjs().format('MM') + '0001';
        } 
    },
    terminalId: { type: String, required: true },
    requestType: {type: String, required: true, default: "P"}, // p => sale
    requestStatus: {type: Boolean, required: true, default: 0},
    responseLog: [Buffer], // "Log" of big responses
    value: {type: Number, required: true}, // The value of the transaction
    cancelled: {type: Boolean, required: true, default: false}, // storno via the terminal
    requestedOn: {type: Date, default: Date.now} // When was this requested to go through
});

CardSwipeSchema.pre('validate', async function (next) {
    let doc = this,
        year = dayjs().format('YY'),
        month = dayjs().format("MM"),
        gymCode = doc.gymCode;

    const last = await mongoose.model('Cardswipe').findOne({'gymCode': gymCode, 'variableSymbol':{"$regex": new RegExp(`${gymCode}${year}${month}`, "i")} }, null, {sort: {'variableSymbol': -1}}).exec();
    if(last){
        let last_number = parseInt(last.variableSymbol.substr(-4));
            console.log(last_number);
            last_number++; // push
            console.log(last_number);

        // GYM CODE + YEAR (YY) + INCREMENT
        let new_number = gymCode + String(year) + String(month) + String(last_number).padStart(4, '0');
        console.log(new_number);
        doc.variableSymbol = new_number;
        next();
    }else{
        console.log("No autoincrement field found, skipping.");
    }
});

const CardSwipe = mongoose.model("Cardswipe", CardSwipeSchema);
module.exports = CardSwipe;