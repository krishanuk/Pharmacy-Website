function loadContent(query = '') {
    fetch(`fetch_advice.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('adviceContent');
            content.innerHTML = '';

            data.forEach(advice => {
                const div = document.createElement('div');
                div.className = 'advice-item';
                div.innerHTML = `
                    <h3>${advice.title}</h3>
                    <p>${advice.description}</p>
                    <img src="uploads/${advice.image}" alt="${advice.title}">
                `;
                content.appendChild(div);
            });
        });
}

function searchAdvice() {
    const query = document.getElementById('searchInput').value.trim();

    if (query === '') {
        alert("Please enter a search term.");
        return;
    }

    loadContent(query);
}


const responses = {
    hydration: "It's important to drink at least 8 glasses of water a day to stay hydrated.",
    exercise: "Aim for at least 30 minutes of moderate exercise most days of the week.",
    diet: "Include a variety of fruits, vegetables, whole grains, and lean proteins in your diet.",
    sleep: "Adults should aim for 7-9 hours of sleep each night to support overall health.",
    stress: "Practice relaxation techniques like deep breathing or meditation to manage stress."
};

const patterns = {
    hydration: [/drink water/i, /hydration/i, /how much water/i, /water intake/i, /stay hydrated/i],
    exercise: [/exercise/i, /workout/i, /physical activity/i, /how often should I exercise/i],
    diet: [/diet/i, /nutrition/i, /eat healthy/i, /healthy foods/i, /food intake/i],
    sleep: [/sleep/i, /how much sleep/i, /rest/i, /how long should I sleep/i],
    stress: [/stress/i, /relaxation/i, /calm down/i, /manage stress/i, /stress relief/i]
};

function sendMessage() {
    const userInput = document.getElementById('userInput').value.trim();

    // Validation: Check if the input is empty
    if (userInput === '') {
        alert("Please enter a message.");
        return;
    }

    if (userInput) {
        clearChatbox();  
        displayMessage(userInput, 'user-message');
        respondToUser(userInput);
        document.getElementById('userInput').value = '';
    }
}

function clearChatbox() {
    const chatbox = document.getElementById('chatbox');
    chatbox.innerHTML = '';  
}

function respondToUser(userInput) {
    let response = "Sorry, I don't understand that. Can you please rephrase?";

    for (const key in patterns) {
        if (patterns[key].some(pattern => pattern.test(userInput))) {
            response = responses[key];
            break;
        }
    }

    displayMessage(response, 'bot-message');
}

function displayMessage(message, className) {
    const chatbox = document.getElementById('chatbox');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${className}`;
    messageDiv.textContent = message;
    chatbox.appendChild(messageDiv);
    chatbox.scrollTop = chatbox.scrollHeight;
}

function enterKey(event) {
    if (event.key === 'Enter') {
        sendMessage();
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', () => loadContent());
