const FormData = require('form-data');
require('dotenv').config();
const axios = require('axios');

let online = 0;
let waitingUsers = [];

let api_default_route = process.env.API_DEFAULT_ROUTE || 'http://127.0.0.1:8000';

function setupSocketHandlers(io, redis, channel) {

    io.on('connection', (socket) => {
        socket.on('chatMessage', ({roomId, message, client_token}) => {
            let url = `${api_default_route}/api/chat/send_message`;
            var formData = new FormData();
            formData.append('group_id', roomId);
            formData.append('message', message);

            axios.post(url, formData, {
                headers: {
                    'Authorization': `Bearer ${client_token}`,
                    ...formData.getHeaders()
                }
            }).then(function (response) {
                io.to(roomId).emit('chatMessage', response.data.result.message);
            }).catch(function (error) {
                console.log("message error: ");
                console.log(error.message);
            });
        });
    
        socket.on('disconnect', async () => {
            const waitingListKey = 'waiting_users';
            online -= 1;
            io.emit("currentOnline", online);
            await redis.lRem(waitingListKey, 1, socket.id);
            await redis.del(`user:${socket.id}`);
        });

        socket.on("chat_user_connected", function ({client_token}) {
            console.log("client_token " + client_token);

            axios.get(`${api_default_route}/api/chat/list`, {
                headers: {
                    'Authorization': `Bearer ${client_token}`,
                }
            })
            .then(function (response) {
                response.data.result.groups.forEach(chat => {
                    socket.join(chat.group_id);
                });
            })
            .catch(function (error) {
                console.log("connect fails");
                console.log(error.message);
            });
        });
    })
    
    io.engine.on("connection_error", (err) => {
        console.log("connection error: ");
        console.log(err.code);     // the error code, for example 1
        console.log(err.message);  // the error message, for example "Session ID unknown"
        console.log(err.context);  // some additional error context
    });
}

module.exports = { setupSocketHandlers };