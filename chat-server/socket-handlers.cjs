const { ProcessChatMessage } = require('./jobs.cjs');
const FormData = require('form-data');
require('dotenv').config();
const axios = require('axios');

let online = 0;
let waitingUsers = [];

let api_default_route = process.env.API_DEFAULT_ROUTE || 'http://127.0.0.1:8000';

function setupSocketHandlers(io, redis, channel) {
    let online = 0;

    io.on('connection', (socket) => {
        console.log("BTRJRTBRTBRT");
        online += 1;
    
        socket.on('findPartner', async ({ city, age }) => {
            const userKey = `user:${socket.id}`;
            const waitingListKey = 'waiting_users';
    
            // save user data
            await redis.hSet(userKey, { socketId: socket.id, city: city, age: age });
            await redis.rPush(waitingListKey, socket.id);
    
            const allIds = await redis.lRange(waitingListKey, 0, -1);
    
            for (const otherId of allIds) {
                if (otherId === socket.id) continue;
    
                const partner = await redis.hGetAll(`user:${otherId}`);
                if (partner && partner.city === city && Math.abs(partner.age - age) <= 5) {
                    // deleteing from queue
                    await redis.lRem(waitingListKey, 1, otherId);
                    await redis.lRem(waitingListKey, 1, socket.id);
    
                    // deleting users
                    await redis.del(userKey);
                    await redis.del(`user:${otherId}`);
    
                    // create room
                    const roomId = `room:${socket.id}:${otherId}`;
                    await socket.join(roomId);
                    const partnerSocket = io.sockets.sockets.get(otherId);
                    await partnerSocket?.join(roomId);
    
                    io.to(roomId).emit('partnerFound', { roomId });
                    return;
                }
            }
    
            socket.emit('waiting');
        });
    
        socket.on('chatMessage', ({roomId, message}) => {
            console.log("new message");
            socket.to(roomId).emit('chatMessage', message);
        });
    
        socket.on('persistentChatRequest', ({roomId, name, age, token}) => {
            let formData = new FormData();
            let sockets = roomId.split(":")
            let socket_first_id = sockets[1];
            let socket_second_id = sockets[2];

            formData.append('socket_first_id', socket_first_id);
            formData.append('socket_second_id', socket_second_id);
            formData.append('name', name);
            formData.append('age', age);

            let url = `${api_default_route}/api/chat/request`;

            console.log("persistentChatRequest " + roomId)
            axios.post(url, formData, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    ...formData.getHeaders()
                }
            }).then(function (response) {
                console.log("ChatRequest created")
                socket.to(roomId).emit('persistentChatRequest', {
                    roomId,
                    name, 
                    age, 
                    request_id: response.data.result.request_id
                });
            }).catch(function (error) {
                console.log("request error: ");
                console.log(error.message);
            });
            
        });
    
        socket.on('requestAccepted', ({roomId, nameInput, ageInput, request_id, client_token}) => {
            let formData = new FormData();
            let sockets = roomId.split(":")
            let socket_first_id = sockets[1];
            let socket_second_id = sockets[2];

            formData.append('socket_first_id', socket_first_id);
            formData.append('socket_second_id', socket_second_id);
            formData.append('name', nameInput);
            formData.append('age', ageInput);
            formData.append('request_id', request_id);

            let url = `${api_default_route}/api/chat/create`;

            console.log("persistentChatCreate " + roomId)
            console.log("token " + client_token)
            axios.post(url, formData, {
                headers: {
                    'Authorization': `Bearer ${client_token}`,
                    ...formData.getHeaders()
                }
            }).then(function (response) {
                console.log("Chat created")
                socket.to(roomId).emit('requestAccepted', {name, age});
            }).catch(function (error) {
                console.log("request error: ");
                console.log(error.message);
            });
        });
    
    
        socket.on("endChat", async (roomId) => {
    
            socket.to(roomId).emit('leaveGroup', roomId);
    
            const clients = await io.in(roomId).fetchSockets();
    
            clients.forEach(s => {
                s.leave(roomId);
            });
        });
    
        socket.on("new_messagedasrwqrq", async (message) => {
            console.log("vsdnwennjwejnwgwgewemk");
            const connection = await amqp.connect(RABBITMQ_URL);
            const channel = await connection.createChannel();
            await channel.assertQueue(QUEUE_NAME, { durable: true });
    
            const job = new ProcessChatMessage({
                user: socket.id,
                text: message,
                timestamp: new Date().toISOString(),
            });
            console.log(job.toLaravelJob());
            console.log("BUFFERRR  " + Buffer.from(job.toLaravelJob()));
      
              // Отправка сообщения в очередь
              await channel.sendToQueue(QUEUE_NAME, Buffer.from(job.toLaravelJob()), {
                persistent: true
              });
            console.log("Message sent to queue:");
        });
    
        socket.on("disconnecting", () => {
            console.log(socket.rooms);
            for (const room of socket.rooms) {
                if (room !== socket.id) {
                    socket.to(room).emit('leaveGroup', room);
                }
            }
        });
    
        socket.on('disconnect', async () => {
            const waitingListKey = 'waiting_users';
            online -= 1;
            io.emit("currentOnline", online);
            await redis.lRem(waitingListKey, 1, socket.id);
            await redis.del(`user:${socket.id}`);
        });
        
        io.emit("currentOnline", online);
    })
    
    io.engine.on("connection_error", (err) => {
        console.log("connection error: ");
        console.log(err.code);     // the error code, for example 1
        console.log(err.message);  // the error message, for example "Session ID unknown"
        console.log(err.context);  // some additional error context
    });
}

module.exports = { setupSocketHandlers };