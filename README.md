# ChatGPT API Integration (in HTML/CSS with JS/PHP)
###### Updated: 25.04.2023 (Working well)
![Screenshot](chatgpt.png)

#### Example of GPT-3, 3.5, and 4 integration for your websites using HTML, CSS, JS, and PHP.

- [free 'ChatGPT Comment reply' Plugin for WordPress](https://github.com/VolkanSah/ChatGPT-Comments-Reply-WordPress-Plugin)
###### ChatGPT is interesting, let's use it to create a better world.

This README.md file will guide you through the process of integrating the ChatGPT API into your PHP-based website, enabling chat functionality using OpenAI's API.

**Please note that this code example is for a basic web application, and you should consider additional security measures please read [ChatGPT Security Best Practices](https://github.com/VolkanSah/ChatGPT-Security-Best-Practices)!**
## Prerequisites
-  PHP 7.4 or higher installed on your web server
-  A web server with support for PHP (e.g., Apache or Nginx)
-  Knowledge of HTML, CSS, and JavaScript (for designing the chat interface)
-  An API key for the ChatGPT API from Openai.com

## API Key
- Register for an account or log in to the OpenAi platform.
- Navigate to the API Keys section and generate a new API key.
- Store the API key safely, as you will need it in the next step.

## Setting Up
- Clone or download this repository.
- Replace YOUR_API_KEY and MODEL in config.php.
- Replace the API_ENDPOINT URL in gptchat.php according to your needs (see list below).
- Upload index.html, style.css, script.js, and gptchat.php files to your web server.
- Access index.html in your browser (e.g., https://yourdomain.com/index.html).
- Type a message in the chat input field and press Enter or click the Send button to send the message.
- The OpenAI API should respond with a message from the language model.
- Be cautious and set limits on your OpenAI Dashboard.
## OpenAi-API Endpoints
**please change gptchat.php & config.php to your needs**

ENDPOINT | MODEL NAME
-- | --
/v1/chat/completions | gpt-4, gpt-4-0314, gpt-4-32k, gpt-4-32k-0314, gpt-3.5-turbo, gpt-3.5-turbo-0301
/v1/completions | text-davinci-003, text-davinci-002, text-curie-001, text-babbage-001, text-ada-001
/v1/edits | text-davinci-edit-001, code-davinci-edit-001
/v1/audio/transcriptions | whisper-1
/v1/audio/translations | whisper-1
/v1/fine-tunes | davinci, curie, babbage, ada
/v1/embeddings | text-embedding-ada-002, text-search-ada-doc-001
/v1/moderations | text-moderation-stable, text-moderation-latest

You've successfully integrated the ChatGPT API into your PHP website! Now, you can customize the chat interface and enhance the user experience as needed.
## Screenshot
<img src="screenshot.png">

**Please note that this code example is for a basic web application, and you should consider additional security measures please read [ChatGPT Security Best Practices](https://github.com/VolkanSah/ChatGPT-Security-Best-Practices)!**



## Thanks
**"Thank you! Your support is appreciated, and I would be grateful if you could share this project with others, give it a "star" on GitHub, or become a sponsor. Don't forget to follow me for more free ideas and updates!"**

## License
This project is copyright © [VolkanSah](https://github.com/volkansah) and is licensed under the MIT License. You are free to use, modify, and distribute the code and assets, as long as the copyright notice and permission notice are preserved in all copies or substantial portions of the software."

## Credits
Copyright: [S. Volkan Sah Kücükbudak](https://github.com/volkansah)

