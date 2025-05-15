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

socket.on("persistentChatRequest", (message) => {
    console.log("persistentChatRequest " + message)
    appendSystemMessage(message, "partner");

    const buttons = document.createElement("div");
    buttons.className = "d-flex gap-2 mt-2";
    
    const yesButton = document.createElement("button");
    yesButton.textContent = "Да";
    yesButton.className = "btn btn-success btn-sm";
    yesButton.addEventListener("click", () => {
        nameInput = "ddd";
        ageInput = 18;
        socket.emit("requestAccepted", {currentRoomId, nameInput, ageInput});
        buttons.remove();
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

socket.on("requestAccepted", (message) => {
    appendSystemMessage("Чат создается...");
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

$("#confirm-persistent-chat").on('click', () => {
    nameInput = document.getElementById("nameInput").value;
    ageInput = parseInt(document.getElementById("agePersistentInput").value);

    console.log("persistentChatRequest " + currentRoomId)
    socket.emit("persistentChatRequest", {roomId: currentRoomId, name: nameInput, age: ageInput });
});

$("#back-to-main").on('click', () => {
    searchSection.classList.remove("d-none");
    chatSection.classList.add("d-none");
});