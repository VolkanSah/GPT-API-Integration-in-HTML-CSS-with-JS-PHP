

const chatForm = document.getElementById("chat-form");
const chatInput = document.getElementById("chat-input");
const chatnameInput = document.getElementById("chatname-input");
const chatOutput = document.getElementById("chat-output");
const chatnameButton = document.getElementById("chatname-button");
const chatnameText = document.getElementById("chatname-text");
var select = document.getElementById('mySelect');
completeHistory = []
completeHistory.push({"role": "system", "content": "You are a helpful assistant." })
test = 'Here\'s a simple Python code to manually convert a string into a number (integer or float) without using built-in functions like `int()` or `float()`:\n\n```python\ndef string_to_number(s):\n    is_negative = False\n    if s[0] == \'-\':\n        is_negative = True\n        s = s[1:]\n\n    num = 0\n    decimal_found = False\n    decimal_multiplier = 1\n\n    for char in s:\n        if char == \'.\':\n            decimal_found = True\n            continue\n\n        if not decimal_found:\n            num = num * 10 + ord(char) - ord(\'0\')\n        else:\n            decimal_multiplier /= 10\n            num += (ord(char) - ord(\'0\')) * decimal_multiplier\n\n    if is_negative:\n        num = -num\n\n    return num\n\n# Test the function\nstring_number = "123"\nprint(string_to_number(string_number))  # Output: 123\n\nstring_number = "-123.45"\nprint(string_to_number(string_number))  # Output: -123.45\n```\n\nThis code handles both integers and floating-point numbers, as well as negative numbers. Note that it doesn\'t include error handling for invalid input strings.'
testMode = false
test2= null
CHATNAME = "temp"
// const regex = /```python([\s\S]*?)```/g;
// // const inputString = "Some text before\n```python\nprint('Hello, world!')\n``` Some text after";

// const matches = test.match(regex);
// if (matches) {
//   const pythonCodeSnippet = matches[0].replace(/^```python\n|\n```$/g, "");
//   console.log(pythonCodeSnippet);
//     test2 = pythonCodeSnippet
// }


// const regex = /```(\w+)([\s\S]+?)```|`([^`]+)`|([^`]+)/gs;
// let match;
// let blocks = [];

// while ((match = regex.exec(test)) !== null) {
//   const [, language, codeBlock, inlineCode, stringBlock] = match;

//   if (language && codeBlock) {
//     blocks.push({ label: `Code Block (${language})`, content: codeBlock });
//   } else if (inlineCode) {
//     blocks.push({ label: 'Inline Code', content: inlineCode });
//   } else if (stringBlock) {
//     blocks.push({ label: 'String Block', content: stringBlock });
//   }
// }

// console.log(blocks);

// console.log(blocks);

document.addEventListener("DOMContentLoaded", async (e)=>{
    const response = await fetch('getchatnames', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
        })
        .then(response => response.json())
        .then(data => {
            console.log("here");
        console.log(data);
        // t = JSON.parse(data)
        t = data
        console.log(t)
        for (var i = 0; i<t.length; i++){
            var opt = document.createElement('option');
            opt.value = i;
            opt.innerHTML = t[i];
            select.appendChild(opt);
        }
        });
    

    
});

chatnameButton.addEventListener("click", async (e) => {
    e.preventDefault();
    CHATNAME = chatnameInput.value.trim();
    console.log("here")
    console.log(CHATNAME)
    chatnameText.innerHTML = CHATNAME
});
chatForm.addEventListener("submit", async (e) => {
    
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    chatOutput.innerHTML += `<p class="user-message">${message}</p>`;
    chatInput.value = "";
    chatOutput.scrollTop = chatOutput.scrollHeight;
    completeHistory.push({"role" :"user", "content": message })
    // const response = await fetch("gptchat.php", {
    //     method: "POST",
    //     headers: {
    //         "Content-Type": "application/json",
    //     },
    //     body: JSON.stringify({ completeHistory }),
    // });
    if (testMode){
        chatOutput.innerHTML += `<pre class="bot-message"><code  class="python">`+test+ `</code></pre>`;
        document.querySelectorAll('pre code').forEach((block) => {
            console.log("yea")
            hljs.highlightElement(block);
          });
        chatOutput.scrollTop = chatOutput.scrollHeight;
        return
    }
    responseData = null;
    const response = await fetch('https://api.openai.com/v1/chat/completions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer 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`
        },
        body: JSON.stringify({
            model:"gpt-4",
            messages: completeHistory,
            max_tokens: 5000,
            temperature: .1,
        })
        })
        .then(response => response.json())
        .then(data => {
        console.log(data);
        responseData =data;
        });
    if (responseData) {
        
        if (responseData.choices && responseData.choices[0] && responseData.choices[0].message) {
            chatOutput.innerHTML += `<pre class="bot-message">`+responseData.choices[0].message.content+ `</pre>`;
            completeHistory.push({"role" :"assistant", "content": responseData.choices[0].message.content })
            const response = await fetch('savetofile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer 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`
                },
                body: JSON.stringify({
                    filename:CHATNAME,
                    contents:completeHistory
                })
                })
                .then(response => response.json())
                .then(data => {
                console.log(data);
                });
        
        
        } else {
            console.error("Error: Unexpected response format", responseData);
        }
        chatOutput.scrollTop = chatOutput.scrollHeight;
        
    } else {
        console.error("Error communicating with GPTChat API");
    }
});