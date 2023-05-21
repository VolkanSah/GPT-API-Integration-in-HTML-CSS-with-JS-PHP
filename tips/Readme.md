### some tips
- use for better error handling
```java
if (response.ok) {
    const data = await response.json();
    if (data.choices && data.choices[0] && data.choices[0].text) {
        chatOutput.innerHTML += `<p class="bot-message">${data.choices[0].text}</p>`;
    } else {
        console.error("Error: Unexpected response format", data);
        chatOutput.innerHTML += `<p class="bot-message error-message">Entschuldigung, es gab ein Problem bei der Verarbeitung Ihrer Anfrage. Bitte versuchen Sie es später noch einmal.</p>`;
    }
} else {
    console.error("Error communicating with GPTChat API");
    chatOutput.innerHTML += `<p class="bot-message error-message">Entschuldigung, es gab ein Problem bei der Kommunikation mit dem Berater. Bitte versuchen Sie es später noch einmal.</p>`;
}
chatOutput.scrollTop = chatOutput.scrollHeight;
```
