const winston = require('winston'),
      options = {
        file: {
        level: 'info',
        filename: __basedir+`/logs/app.log`,
        handleExceptions: true,
        json: true,
        maxsize: 5242880, // 5MB
        maxFiles: 5,
        colorize: false,
        },
        console: {
        level: 'debug',
        handleExceptions: true,
        json: false,
        colorize: true,
        },
      };

const logger = winston.createLogger({
    transports: [
      new winston.transports.File(options.file),
      new winston.transports.Console(options.console)
    ],
    exitOnError: false
});

logger.stream = {
    write: (message, encoder) => {
        logger.info(message);
    }
};

module.exports = logger;