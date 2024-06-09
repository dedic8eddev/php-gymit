const mongoose = require('mongoose');

const LockerSettingsSchema = new mongoose.Schema({
    gymId: { type: String, required: true }, // ID klubu
    lockerId: {type: String, required: true}, // SerialNum
    lockerAddress: {type: Number, required: true, default: 0},
    lockerNumber: {type: Number, required: false}, // Real number of the locker (TBD)
    lockerRoom: {type: String, required: true}, //Dámské / Pánské šatny
    lockerStatus: {
        status: {type: String, required: false},
        response: {type: String, required: false}
    },
    lockerStatusLog: [{
        status: {type: String, required: true},
        response: {type: String, required: true},
        loggedOn: {type: Date, default: Date.now}
    }],
    masterCards: [String], // Master karty
    vipCards: [String], // VIP Karty
    customSettings: [mongoose.Schema.Types.Mixed] // Array of custom option key/value pairs
});

LockerSettingsSchema.pre("save", function(next){
    let record = this;

    if(record.isModified("lockerStatus.status")){
        record.lockerStatusLog.push(record.lockerStatus);
    }

    next();
});

const lockerSettings = mongoose.model('lockerSetting', LockerSettingsSchema);
module.exports = lockerSettings;
