<!DOCTYPE html>
<html>
<head>
    <title>AI Booking Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; background: #0D0D0D; color: white; }
        #chat-box { height: 400px; overflow-y: auto; border: 1px solid #333; padding: 10px; }
        .user { text-align: right; margin: 5px; }
        .bot { text-align: left; margin: 5px; }
        button { margin: 5px; padding: 8px; background: purple; color: white; border: none; }
    </style>
</head>
<body>

<h2>🤖 Codyex AI Booking</h2>

<div id="chat-box"></div>

<input type="text" id="message" placeholder="Type your message..." />
<button onclick="sendMessage()">Send</button>

<div id="slots"></div>

<script>
function appendMessage(text, type) {
    let box = document.getElementById("chat-box");
    box.innerHTML += `<div class="${type}">${text}</div>`;
    box.scrollTop = box.scrollHeight;
}

function sendMessage(msg = null) {
    let input = document.getElementById("message");
    let message = msg || input.value;

    appendMessage("You: " + message, "user");
    input.value = "";

    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message: message })
    })
    .then(res => res.json())
    .then(data => {
        appendMessage("Bot: " + data.message, "bot");

        // show slots as buttons
        if (data.slots) {
            let slotDiv = document.getElementById("slots");
            slotDiv.innerHTML = "<h4>Available Slots:</h4>";

            data.slots.forEach(slot => {
                let btn = document.createElement("button");
                btn.innerText = slot;
                btn.onclick = () => sendMessage(slot);
                slotDiv.appendChild(btn);
            });
        }
    });
}
</script>

</body>
</html>
