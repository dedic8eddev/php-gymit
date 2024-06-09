const mongoose = require('mongoose');

const ReaderSettingsSchema = new mongoose.Schema({
    roomId: {type: Number, required: true}, // ID of the room
    gymId: { type: String, required: true }, // ID klubu
    readerId: {type: String, required: true}, // SerialNum
    readerAddress: {type: Number, required: true, default: 0},
    isPersonificator: {type: Boolean, required: true, default: false}, // Personificator unit?
    isBuildingEntrance: {type: Boolean, required: true, default: false}, // Building entrance?
    isBuildingExit: {type: Boolean, required: true, default: false},
    isWellness: {type: Boolean, required: true, default: false},
    isExerciseRoom: {type: Boolean, required: true, default: false},
    isActive: {type: Boolean, required: true, default: true}, // Active or disabled?
    roomPriority: {type: Number, required: true, default: 1},
    pinCode: {type: Number, required: false}, // pin
    customSettings: [mongoose.Schema.Types.Mixed] // Array of custom option key/value pairs
});

const readerSettings = mongoose.model('readerSetting', ReaderSettingsSchema);
module.exports = readerSettings;
