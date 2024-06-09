const mongoose = require('mongoose');

const CreditSchema = new mongoose.Schema({
    currentValue: { type: Number, default: 0, set: (value) => { 
            this._previousValue = this.currentValue; 
            return value; 
        } 
    },
    lastChange: { type: Date, default: Date.now, required: true }, // Kdy zaplaceno
    clientId: { type: String, required: true },
    cardId: { type: String, required: true, unique: true },
    history: [
        {
            value: {type: Number},
            date: {type: Date, default: Date.now}
        }
    ]
});

// Pre save hook, update the history array, modify lastChange date
CreditSchema.pre('validate', function (next) {
    let previousValue = this._previousValue;

    try {
        if(!this.isModified('currentValue')) next();

        this.history.push({'value': previousValue});
        this.lastChange = new Date();
        
        next();
    } catch (err) {
        return next(err);
    }
});

const Credit = mongoose.model('Credit', CreditSchema);
module.exports = Credit;
