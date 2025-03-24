const chatForm = document.getElementById("chat-form");
const chatInput = document.getElementById("chat-input");
const chatOutput = document.getElementById("chat-output");

chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    chatOutput.innerHTML += `<p class="user-message">${message}</p>`;
    chatInput.value = "";
    chatOutput.scrollTop = chatOutput.scrollHeight;

    const response = await fetch("gptchat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message }),
    });

    if (!response.ok) {
        console.error("Error communicating with GPTChat API");
        return;
    }

    const reader = response.body.getReader();
    let botMessage = "";

    async function read() {
        const { done, value } = await reader.read();
        if (done) {
            chatOutput.innerHTML += `<p class="bot-message">${botMessage}</p>`;
            chatOutput.scrollTop = chatOutput.scrollHeight;
            return;
        }

        const text = new TextDecoder().decode(value);
        const lines = text.split("\n").filter(line => line.startsWith("data: "));
        for (const line of lines) {
            const json = line.replace("data: ", "").trim();
            if (json && json !== "[DONE]") {
                try {
                    const data = JSON.parse(json);
                    if (data.choices && data.choices[0] && data.choices[0].delta && data.choices[0].delta.content) {
                        botMessage += data.choices[0].delta.content;
                        chatOutput.innerHTML += `<span class="bot-message">${data.choices[0].delta.content}</span>`;
                        chatOutput.scrollTop = chatOutput.scrollHeight;
                    }
                } catch (err) {
                    console.error("JSON Parsing Error", err);
                }
            }
        }
        read();
    }

    read();
});
