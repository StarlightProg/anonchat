const { serialize } = require('php-serialize');
const { v4: uuidv4 } = require('uuid');

class ProcessChatMessage {
    constructor(message) {
        this.message = message;
    }

    toLaravelJob() {
        const nameSpace = { 'App\\Jobs\\ProcessChatMessage': ProcessChatMessage };
        return JSON.stringify({
            uuid: uuidv4(),
            displayName: 'App\\Jobs\\ProcessChatMessage',
            job: 'Illuminate\\Queue\\CallQueuedHandler@call',
            data: {
                commandName: 'App\\Jobs\\ProcessChatMessage',
                command: serialize(this, nameSpace),
            }
        });
    }
}

module.exports = {
    ProcessChatMessage,
};