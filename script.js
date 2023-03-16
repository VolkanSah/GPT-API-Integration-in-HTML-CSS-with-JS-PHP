document.addEventListener('DOMContentLoaded', () => {
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatOutput = document.getElementById('chat-output');

    chatForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = chatInput.value.trim();

        if (message.length === 0) return;

        chatOutput.innerHTML += `<p class="user-message">${message}</p>`;
        chatInput.value = '';
        chatOutput.scrollTop = chatOutput.scrollHeight;

        const response = await fetch('gptchat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message }),
        });

        if (response.ok) {
            const data = await response.json();
            chatOutput.innerHTML += `<p class="bot-message">${data.choices[0].text}</p>`;
            chatOutput.scrollTop = chatOutput.scrollHeight;
        } else {
            console.error('Error communicating with GPTChat API');
        }
    });
});
