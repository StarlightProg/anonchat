var app = require('express')();
// const axios = require('axios');
var http = require('http');
// const FormData = require('form-data');
var cors = require('cors');
// var Redis = require('ioredis');
// var fs = require('fs');
const { Server } = require("socket.io");
// const { Readable } = require("stream");

let online = 0;

app.use(cors());

app.use((req, res, next) => {
    console.log('Time:', Date.now())
    let headers = req.rawHeaders.indexOf('Origin');
    console.log("app connect: ");
    console.log(req.rawHeaders[headers + 1]);
    next()
})
let httpServer = http.createServer(app);

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
        // "preflightContinue": false,
        // "optionsSuccessStatus": 204
    },
    connectTimeout: 20000
}
);

httpServer.listen(8005, function () {
    console.log('HTTP Listening to port 8005');
});

io.on('connection', (socket) => {
    console.log("connected someone");
    //Assign the socket variable to WebSocket variable so we can use it the GET method

    online += 1;

    socket.on('findPartner', (data) => {
        const user = {
            id: socket.id,
            city: data.city,
            age: data.age
        };

        console.log(`Ищем собеседника для ${socket.id} (${user.city}, ${user.age})`);

        // Попробуем найти подходящего собеседника
        const partnerIndex = waitingUsers.findIndex(u =>
            u.city === user.city &&
            Math.abs(u.age - user.age) <= 5 // Допустим, разница в возрасте до 5 лет
        );

        if (partnerIndex !== -1) {
            const partner = waitingUsers[partnerIndex];
            waitingUsers.splice(partnerIndex, 1); // удаляем из очереди

            const roomId = `${socket.id}-${partner.id}`;
            socket.join(roomId);
            io.sockets.sockets.get(partner.id)?.join(roomId);

            io.to(roomId).emit('partnerFound', { roomId });
            console.log(`Создана комната: ${roomId}`);
        } else {
            waitingUsers.push(user);
            socket.emit('waiting');
            console.log(`Пользователь ${socket.id} добавлен в очередь`);
        }
    });
    
    socket.on("disconnect", (reason) => {
        online -= 1;
        io.emit("currentOnline", online);
    });
    
    io.emit("currentOnline", online);
})

// var redis = new Redis();
// var api_default_route = "https://бибиг.рф"

// redis.subscribe('laravel_database_checkPassport');
// redis.subscribe('laravel_database_notification');
// redis.subscribe('laravel_database_rideChat');

// redis.subscribe('laravel_database_message', function () {
//     console.log('Listening to messages');
// });

// redis.on('message', function(channel, data) {
//     console.log(channel);
//     data = JSON.parse(data);
// });

// let userRooms = {};

// io.engine.on('initial_headers', (headers, req) => {
//     console.log('Initial headers received from client:', req.rawHeaders);
// });

// io.engine.on('headers', (headers, req) => {
//     console.log('Response headers sent to client:', headers);
// });

io.engine.on("connection_error", (err) => {
    console.log("connection error: ");
    console.log(err.code);     // the error code, for example 1
    console.log(err.message);  // the error message, for example "Session ID unknown"
    console.log(err.context);  // some additional error context
  });

//   io.engine.on('close', (reason) => {
//     console.log(`Socket engine closed connection. Reason: ${reason}`);
// });

// io.on('connection', function (socket) {

// });
