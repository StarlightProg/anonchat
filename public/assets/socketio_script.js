let socket = io('http://localhost:8005', {
    transports: ["websocket"]
  });

console.log("socket");



const searchSection = document.getElementById("search-section");
const chatSection = document.getElementById("chat-section");
const chatBox = document.getElementById("chat");

let currentRoomId = null;

document.getElementById("search-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const city = document.getElementById("cityInput").value;
    const age = parseInt(document.getElementById("ageInput").value);

    // const modal = new bootstrap.Modal(document.getElementById('searchingModal'));
    // modal.show();

    socket.emit("findPartner", { city, age });
});

$("#searchingModal").on("hidden.bs.modal", function () {
  console.log("cancelling search")
  socket.emit("cancelSearch");
});

socket.on("currentOnline", (message) => {
  console.log(message);
  $('#current-online').text(message);
});

socket.on("waiting", () => {
    console.log("Ждём партнёра...");
});

socket.on("partnerFound", ({ roomId }) => {
    currentRoomId = roomId;
    searchSection.classList.add("d-none");
    chatSection.classList.remove("d-none");

    socket.emit("joinRoom", roomId);
});

document.getElementById("chat-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const message = document.getElementById("message").value;
    if (message.trim() === "") return;

    appendMessage(message, "me");
    socket.emit("chatMessage", { roomId: currentRoomId, message });
    document.getElementById("message").value = "";
});

socket.on("chatMessage", ({ message }) => {
    appendMessage(message, "partner");
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