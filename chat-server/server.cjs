var app = require('express')();
var http = require('http');
var cors = require('cors');
const { Server } = require("socket.io");
const { createClient } = require('redis');
require('dotenv').config();
const { setupMessageQueue } = require('./message-queue.cjs');
const { setupSocketHandlers } = require('./socket-handlers.cjs');

let online = 0;
let waitingUsers = [];

let api_default_route = process.env.API_DEFAULT_ROUTE || 'http://127.0.0.1:8000';

const PORT = process.env.PORT || 8005;

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

let channel;
// rabbitmq connection
setupMessageQueue().then(ch => {
    channel = ch;
});

// socket io handlers
setupSocketHandlers(io, redis, channel);
