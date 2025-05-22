const amqp = require('amqplib');
const RABBITMQ_URL = process.env.RABBITMQ_URL || 'amqp://localhost';
const QUEUE_NAME = process.env.QUEUE_NAME || 'persistent_chat_requests';

async function setupMessageQueue() {
    try {
        const connection = await amqp.connect(RABBITMQ_URL);
        const channel = await connection.createChannel();
        await channel.assertQueue(QUEUE_NAME, { durable: true });
        console.log('RabbitMQ connected');
        return channel;
    } catch (error) {
        console.error('Failed to connect to RabbitMQ:', error);
    }
}

module.exports = { setupMessageQueue };