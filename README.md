# GPT API Integration (in HTML/CSS with JS/PHP)
###### Updated: 02.06.2024 modules/endpoints

![Screenshot](chatgpt.png)

> [!IMPORTANT]
> Please note that this code example is for a basic web application to show "How to do it?", not for production use! 

### Other Important sources for you! Please read before!
- [Secure Implementation of Artificial Intelligence (AI)](https://github.com/VolkanSah/Implementing-AI-Systems-Whitepaper)
- [GPT Security Best Practices (with GPT3.5-Turbo Example)](https://github.com/VolkanSah/GPT-Security-Best-Practices)
###### GPT is interesting, let's use it to create a better world.

This README.md file will guide you through the process of integrating the GPT-4 API into your PHP-based website, enabling chat functionality using OpenAI's API.

## Prerequisites
- PHP 7.4 or higher installed on your web server
- A web server with support for PHP (e.g., Apache or Nginx)
- Knowledge of HTML, CSS, and JavaScript (for designing the chat interface)
- An API key for the GPT API from Openai.com

## API Key
- Register for an account or log in to the OpenAI platform.
- Navigate to the API Keys section and generate a new API key.
- Store the API key safely, as you will need it in the next step.

## Setting Up
1. Clone or download this repository.
2. Replace `YOUR_API_KEY` in `index.php` with your actual API key.
3. Upload `index.php`, `style.css`, `script.js`, and the `templates` folder to your web server.
4. Ensure the `uploads` folder exists and is writable by the web server.
5. Access `index.php` in your browser (e.g., https://yourdomain.com/index.php).
6. Type a message in the chat input field and press Enter or click the Send button to send the message.
7. The OpenAI API should respond with a message from the language model.
8. Be cautious and set limits on your OpenAI Dashboard.

## OpenAI API Endpoints
Ensure to adjust the API endpoint URL in `index.php` if needed. Here is an example of the API endpoint used for GPT-4:
- `https://api.openai.com/v1/engines/gpt-4/completions`

### File Descriptions
- **index.php**: Main PHP file that handles the chat logic, communication with the OpenAI API, and file uploads.
- **style.css**: CSS file for styling the chat interface.
- **script.js**: JavaScript file for handling form submission and dynamically updating the chat interface.
- **templates/chat.html**: HTML template for the chat interface.
- **uploads/**: Directory to store uploaded images.

### Example Usage
1. Open `index.php` in your browser.
2. Enter a message in the chat input field.
3. (Optional) Upload an image.
4. Click "Send" to communicate with the GPT-4 API and receive a response.
5. The response will be displayed in the chat interface.

Be sure to customize the application as needed to fit your requirements.
