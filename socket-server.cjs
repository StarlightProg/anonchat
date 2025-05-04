var app = require('express')();
var http = require('http');
var cors = require('cors');
const { Server } = require("socket.io");
const { createClient } = require('redis');

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
        let headers = req.rawHeaders.indexOf('Origin');
        console.log("server connect: ");
        console.log(req.rawHeaders[headers + 1]);
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
});

io.on('connection', (socket) => {
    console.log("connected someone");

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
                socket.join(roomId);
                const partnerSocket = io.sockets.sockets.get(otherId);
                partnerSocket?.join(roomId);

                io.to(roomId).emit('partnerFound', { roomId });
                return;
            }
        }

        socket.emit('waiting');
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
