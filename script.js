const ENDPOINT = "gptchat.php";
const MAX_HISTORY = 20; // messages sent per request (must be <= max_messages on the server)

const form = document.getElementById("chat-form");
const input = document.getElementById("chat-input");
const output = document.getElementById("chat-output");
const submitBtn = document.getElementById("chat-submit");
const resetBtn = document.getElementById("chat-reset");

let history = [];
let controller = null;

function addMessage(cls, text) {
    const p = document.createElement("p");
    p.className = cls;
    p.textContent = text; // textContent: never interpret model/user output as HTML
    output.appendChild(p);
    output.scrollTop = output.scrollHeight;
    return p;
}

function setBusy(busy) {
    submitBtn.textContent = busy ? "Stop" : "Send";
    submitBtn.type = busy ? "button" : "submit";
    input.disabled = busy;
    if (!busy) input.focus();
}

function trimmedHistory() {
    const recent = history.slice(-MAX_HISTORY);
    while (recent.length && recent[0].role !== "user") recent.shift();
    return recent;
}

async function readStream(response, bubble) {
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = "";
    let text = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;
        buffer += decoder.decode(value, { stream: true });

        let idx;
        while ((idx = buffer.indexOf("\n\n")) !== -1) {
            const event = buffer.slice(0, idx);
            buffer = buffer.slice(idx + 2);
            for (const line of event.split("\n")) {
                if (!line.startsWith("data:")) continue;
                const data = line.slice(5).trim();
                if (data === "[DONE]") return text;
                const payload = JSON.parse(data);
                if (payload.error) throw new Error(payload.error);
                if (payload.delta) {
                    text += payload.delta;
                    bubble.textContent = text;
                    output.scrollTop = output.scrollHeight;
                }
            }
        }
    }
    return text;
}

async function send(message) {
    addMessage("user-message", message);
    history.push({ role: "user", content: message });

    const bubble = addMessage("bot-message", "…");
    controller = new AbortController();
    setBusy(true);

    let reply = "";
    try {
        const response = await fetch(ENDPOINT, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ messages: trimmedHistory() }),
            signal: controller.signal,
        });

        if (!response.ok) {
            let msg = `Request failed (HTTP ${response.status}).`;
            try {
                const data = await response.json();
                if (data.error) msg = data.error;
            } catch (_) { /* keep default message */ }
            throw new Error(msg);
        }

        reply = await readStream(response, bubble);
        if (!reply) bubble.textContent = "(empty response)";
    } catch (err) {
        if (err.name === "AbortError") {
            reply = bubble.textContent === "…" ? "" : bubble.textContent;
            if (!reply) bubble.remove();
        } else {
            console.error(err);
            bubble.textContent = err.message || "Something went wrong.";
            bubble.classList.add("error-message");
            reply = "";
        }
    } finally {
        controller = null;
        setBusy(false);
    }

    if (reply) {
        history.push({ role: "assistant", content: reply });
    } else {
        // Keep the history valid (alternating roles) so the user can simply retry.
        history.pop();
    }
}

form.addEventListener("submit", (e) => {
    e.preventDefault();
    const message = input.value.trim();
    if (!message || controller) return;
    input.value = "";
    send(message);
});

submitBtn.addEventListener("click", () => {
    if (controller) controller.abort();
});

resetBtn.addEventListener("click", () => {
    if (controller) controller.abort();
    history = [];
    output.innerHTML = "";
    input.focus();
});
