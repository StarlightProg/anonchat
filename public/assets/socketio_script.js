let socket = io('http://localhost:8005', {
    transports: ["websocket"]
  });
console.log("socket");
socket.on("currentOnline", (message) => {
    console.log(message);
    $('#current-online').text(message);
})