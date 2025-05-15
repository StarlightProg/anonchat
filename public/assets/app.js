
const searchSection = document.getElementById("search-section");
const chatSection = document.getElementById("chat-section");
const chatBox = document.getElementById("chat");
const messageForm = document.getElementById('message-form');
const messageInput = document.getElementById('message');
const findNewBtn = document.getElementById('find-new');
const backToMain = document.getElementById('back-to-main');
const endChat = document.getElementById('end-chat');

let currentRoomId = null;

document.getElementById("search-form").addEventListener("submit", function (e) {
    e.preventDefault();
    let city = document.getElementById("cityInput").value;
    let age = parseInt(document.getElementById("ageInput").value);

    // const modal = new bootstrap.Modal(document.getElementById('searchingModal'));
    // modal.show();

    socket.emit("findPartner", { city, age });
});

$("#searchingModal").on("hidden.bs.modal", function () {
  console.log("cancelling search");
});

document.getElementById("message-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const message = document.getElementById("message").value;
    if (message.trim() === "") return;

    appendMessage(message, "me");
    socket.emit("chatMessage", { roomId: currentRoomId, message });
    document.getElementById("message").value = "";
});

document.addEventListener('DOMContentLoaded', function () {
    const createChatButton = document.getElementById('create-persistent-chat');
    const persistentChatForm = document.getElementById('persistent-chat-form');
    const cancelPersistentChatButton = document.getElementById('cancel-persistent-chat');
    const confirmPersistentChatButton = document.getElementById('confirm-persistent-chat');
    
    createChatButton.addEventListener('click', function () {
        persistentChatForm.classList.remove('d-none');
    });
    
    cancelPersistentChatButton.addEventListener('click', function () {
        persistentChatForm.classList.add('d-none');
    });
});

function appendMessage(msg, type) {
    const div = document.createElement("div");
    div.className = `message ${type}`;
    div.textContent = msg;
    div.style.padding = "8px";
    div.style.marginBottom = "6px";
    div.style.borderRadius = "12px";
    div.style.background = type === "me" ? "#d1e7dd" : "#e2e3e5";
    div.style.alignSelf = type === "me" ? "flex-end" : "flex-start";
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function appendSystemMessage(text) {
    const sys = document.createElement('div');
    sys.className = 'text-muted text-center fst-italic mb-2';
    sys.textContent = text;
    chatBox.appendChild(sys);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function toggleChatState(active) {
    messageForm.classList.toggle('d-none', !active);
    backToMain.classList.toggle('d-none', active);
    findNewBtn.classList.toggle('d-none', active);
    endChat.classList.toggle('d-none', !active);
    messageInput.disabled = !active;
}

function toggleScreens(active) {
    searchSection.classList.toggle("d-none", active);
    chatSection.classList.toggle("d-none", !active);
}