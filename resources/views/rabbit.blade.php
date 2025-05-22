<!DOCTYPE html>
<html>
<head>
  <title>Chat</title>
  <script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
</head>
<body>
  <h1>Chat</h1>
  <textarea id="message"></textarea>
  <button onclick="sendMessage()">Send</button>

  <script>
    const socket = io("http://localhost:8005", {
    transports: ["websocket"]
  });

    function sendMessage() {
        console.log("cmwkgwekg")
      const message = document.getElementById("message").value;
      socket.emit("chat_message", message);
      document.getElementById("message").value = "";
    }

    // Получение сообщений от сервера
    socket.on("new_message", (data) => {
      console.log("New message:", data);
      alert(`New message: ${data.message}`);
    });
  </script>
</body>
</html>
