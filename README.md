# ChatGPT 4 API Integration for your Websites in PHP
ChatGPT is interesting, let use it for creating a better world.

This README.md file will guide you through the process of integrating the ChatGPT API into your PHP website, allowing you to set up a chat functionality using OpenAI's language models. **Be carful and set Limits on your OpenAi Dashboard**

## Prerequisites

-  PHP 7.4 or higher installed on your web server
-  A web server with support for PHP (e.g., Apache or Nginx)
-  Knowledge of HTML, CSS, and JavaScript (for designing the chat interface)
-  An API key for the GPTChat API

## API Key

- Register for an account or log in to the OpenAi platform.
- Navigate to the API Keys section and generate a new API key.
- Store the API key safely, as you will need it in the next step.

## Setting up the PHP Script

Create a new PHP file named gptchat.php and place the following code inside it:

```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Replace YOUR_API_KEY with your actual GPTChat API key
define('API_KEY', 'YOUR_API_KEY');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = urlencode($input['message']);
    
// change this domain to your needs, please
    $url = "https://api.gptchat.com/v1/engines/gpt-4/complete?api_key=" . API_KEY . "&message=" . $message;

    $response = file_get_contents($url);
    echo $response;
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
```

Replace YOUR_API_KEY with the API key you generated in the previous step and set your api-domain

## Creating a Chat Interface

Create an HTML file named index.html and place the following code inside it:

```html

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPTChat API Integration</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="chat-container">
        <div id="chat-output" class="chat-output"></div>
        <form id="chat-form" class="chat-input-form">
            <input type="text" id="chat-input" class="chat-input" placeholder="Type your message here" autocomplete="off">
            <button type="submit" class="chat-submit">Send</button>
        </form>
    </div>
</body>
</html>
```

### Next, create a CSS file named style.css for styling the chat interface:
```css
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
}

.chat-container {
    width: 100%;
    max-width: 600px;
    margin: 40px auto;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 3px 10px rgba(0,0, 0, 0.1);
display: flex;
flex-direction: column;
}

.chat-output {
flex-grow: 1;
padding: 20px;
overflow-y: auto;
max-height: 400px;
}

.chat-output p {
margin: 10px 0;
}

.user-message {
text-align: right;
font-weight: bold;
}

.bot-message {
text-align: left;
font-weight: normal;
}

.chat-input-form {
display: flex;
align-items: center;
padding: 10px;
background-color: #f0f0f0;
border-top: 1px solid #e0e0e0;
}

.chat-input {
flex-grow: 1;
border: none;
border-radius: 3px;
padding: 10px;
margin-right: 10px;
}

.chat-submit {
padding: 10px 20px;
background-color: #007bff;
color: #fff;
font-weight: bold;
border: none;
cursor: pointer;
border-radius: 3px;
}

.chat-submit:hover {
background-color: #0056b3;
}
```
### Lastly, create a JavaScript file named `script.js` to handle the chat functionality:
```javascript
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
```
Now you have a basic chat interface to communicate with the GPTChat API.
Testing the Chat Functionality

- Upload the index.html, style.css, script.js, and gptchat.php files to your web server.
- Access the index.html file in your browser (e.g., https://yourdomain.com/index.html).
- Type a message in the chat input field and press Enter or click the Send button to send the message.
-  The GPTChat API should respond with a message from the language model.

That's it! You have successfully integrated the GPTChat API into your PHP website. You can now customize the chat interface and improve the user experience as needed.


