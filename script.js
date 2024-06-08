// script.js
document.getElementById('chat-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const message = document.getElementById('message').value;
    const fileInput = document.getElementById('file');
    const formData = new FormData();

    formData.append('message', message);
    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    displayMessage(result.message, result.imageUrl);
});

function displayMessage(message, imageUrl) {
    const chatBox = document.getElementById('chat-box');
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message');
    messageDiv.innerHTML = `<p>${message}</p>`;

    if (imageUrl) {
        const img = document.createElement('img');
        img.src = imageUrl;
        messageDiv.appendChild(img);
    }

    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}
