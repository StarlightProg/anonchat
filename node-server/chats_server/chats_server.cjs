var app = require('express')();
var http = require('http');
var cors = require('cors');
const { Server } = require("socket.io");
const { createClient } = require('redis');
require('dotenv').config();
const { setupSocketHandlers } = require('./socket-handlers.cjs');

process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err.message, err.stack);
  });
  
process.on('unhandledRejection', (err) => {
    console.error('Unhandled Rejection:', err.message || err);
});

const PORT = process.env.CHATS_PORT || 8006;

app.use(cors());

app.use((req, res, next) => {
    console.log('Time:', Date.now())
    let headers = req.rawHeaders.indexOf('Origin');
    console.log("app connect: ");
    console.log(req.rawHeaders[headers + 1]);
    next()
})

let httpServer = http.createServer(app);

const redis = createClient({
    url: (process.env.REDIS_HOST != undefined) ? `redis://default:${process.env.REDIS_PASSWORD}@${process.env.REDIS_HOST}:${process.env.REDIS_PORT}` : `redis://127.0.0.1:6379`
});
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

httpServer.listen(PORT, function () {
    console.log(`HTTP Listening to port ${PORT}`);
});

// socket io handlers
setupSocketHandlers(io, redis);
