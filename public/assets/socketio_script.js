let socket = io('http://localhost:8005', {
    transports: ["websocket"]
  });

console.log("socket");

socket.on("currentOnline", (message) => {
  console.log(message);
  $('#current-online').text(message);
});

socket.on("waiting", () => {
    console.log("Ждём партнёра...");
});

socket.on("partnerFound", ({ roomId }) => {
    console.log("room created " + roomId);
    currentRoomId = roomId;
    searchSection.classList.add("d-none");
    chatSection.classList.remove("d-none");

    chatBox.innerHTML = "";

    if ($("#searchingModal").hasClass("show")) {
        $("#searchingModal").modal("hide");
    } else {
        $("#searchingModal").one("shown.bs.modal", function () {
            $("#searchingModal").modal("hide");
        });
    }

    toggleChatState(true);
});

socket.on("persistentChatRequest", ({roomId, name, age, request_id}) => {
    console.log("persistentChatRequest " + name + " " + age + " , приглашает создать постоянный чат");
    appendSystemMessage(name + " " + age + " , приглашает создать постоянный чат", "partner");
    let client_token = localStorage.getItem('auth_token');

    const buttons = document.createElement("div");
    buttons.className = "d-flex gap-2 mt-2";
    
    const yesButton = document.createElement("button");
    yesButton.textContent = "Да";
    yesButton.className = "btn btn-success btn-sm";
    yesButton.addEventListener("click", () => {
        nameInput = localStorage.getItem('user_name');
        ageInput = 18;
        socket.emit("requestAccepted", {roomId, nameInput, ageInput, request_id, client_token});
        //buttons.remove();
    });
    
    const noButton = document.createElement("button");
    noButton.textContent = "Нет";
    noButton.className = "btn btn-danger btn-sm";
    noButton.addEventListener("click", () => {
        buttons.remove();
    });
    
    buttons.appendChild(yesButton);
    buttons.appendChild(noButton);
    
    chatBox.appendChild(buttons);
    chatBox.scrollTop = chatBox.scrollHeight;
});

socket.on("requestAccepted", (chat_url) => {
    console.log("chat created")
    const buttons = document.createElement("div");
    buttons.className = "d-flex gap-2 mt-2";
    
    const redirectButton = document.createElement("button");
    redirectButton.textContent = "Перейти в чат";
    redirectButton.className = "btn btn-success btn-sm";
    redirectButton.addEventListener("click", () => {
        window.location.replace('chats/' + chat_url);
    });

    buttons.appendChild(redirectButton);
    
    appendSystemMessage("Чат создан");
    chatBox.appendChild(buttons);
});

socket.on("systemMessage", (message) => {
    appendSystemMessage(message);
});

socket.on("chatMessage", (message) => {
    appendMessage(message, "partner");
});

socket.on('leaveGroup', () => {
    appendSystemMessage('Собеседник отключился.');
    toggleChatState(false);
});

$("#end-chat").on('click', () => {
    socket.emit('endChat', currentRoomId);
    appendSystemMessage('Вы завершили чат.');
    toggleChatState(false);
});

$("#find-new").on('click', () => {
    city = document.getElementById("cityInput").value;
    age = parseInt(document.getElementById("ageInput").value);

    socket.emit("findPartner", { city, age });

    appendSystemMessage('Поиск нового собеседника...');
});

$("#create-persistent-chat").on('click', () => {
    nameInput = localStorage.getItem('user_name');
    ageInput = 18;
    client_token = localStorage.getItem('auth_token');

    console.log("persistentChatRequest " + currentRoomId)
    socket.emit("persistentChatRequest", {roomId: currentRoomId, name: nameInput, age: ageInput, token: client_token});
});

$("#back-to-main").on('click', () => {
    searchSection.classList.remove("d-none");
    chatSection.classList.add("d-none");
});