var app = require('express')();
var http = require('http');
var cors = require('cors');
const axios = require('axios');
const { Server } = require("socket.io");
const { createClient } = require('redis');
require('dotenv').config();

let online = 0;
let waitingUsers = [];

app.use(cors());

app.use((req, res, next) => {
    console.log('Time:', Date.now())
    let headers = req.rawHeaders.indexOf('Origin');
    console.log("app connect: ");
    console.log(req.rawHeaders[headers + 1]);
    next()
})

let httpServer = http.createServer(app);

const redis = createClient();
redis.connect();

const io = new Server(httpServer
    , {
    allowEIO3: true,
    transports: ["websocket"],
    allowRequest: async (req, callback) => {
        callback(null, true);
    },
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    },
    connectTimeout: 20000
}
);

httpServer.listen(8005, function () {
    console.log('HTTP Listening to port 8005');
    console.log("dfsfsdfsfsd");
});

io.on('connection', (socket) => {

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

    socket.on('persistentChatRequest', ({roomId, name, age}) => {
        console.log("persistentChatRequest " + roomId)
        socket.to(roomId).emit('persistentChatRequest', name + " " + age + " , приглашает создать постоянный чат" );
    });

    socket.on('requestAccepted', ({roomId, name, age}) => {
        console.log("chat request accepted");
        io.to(roomId).emit('requestAccepted');

        axios.post(url, formData, {
            headers: {
                'Authorization': `Bearer ${client_token}`,
                ...formData.getHeaders()
            }
        }).then(function (response) {
            if (data.file) {
                console.log("Отправить сообщение с СОКЕТОМ с файлом" +  data.group_id + " " + data.message);

                io.to(data.group_id).emit('send_message', {
                    group_id: data.group_id, 
                    client_id: response.data.result.client_id, 
                    message: data.message || null, 
                    message_id: response.data.result.message_id || null, 
                    file_url: response.data.result.file_url || null,
                    loading_id: data.loading_id || null,
                    time: response.data.result.time,
                    message_type: data.message_type || null,
                });
            }
        }).catch(function (error) {
            console.log("send_message error: ");
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
