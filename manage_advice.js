function loadAdvice() {
    fetch('fetch_advice.php')
        .then(response => response.json())
        .then(data => {
            const adviceList = document.getElementById('adviceList');
            adviceList.innerHTML = '';

            data.forEach(advice => {
                const div = document.createElement('div');
                div.className = 'advice-item';
                div.innerHTML = `
                    <h3>${advice.title}</h3>
                    <p>${advice.description}</p>
                    <img src="uploads/${advice.image}" alt="${advice.title}">
                    <button onclick="editAdvice(${advice.id}, '${advice.title}', '${advice.description}')">Edit</button>
                    <button onclick="deleteAdvice(${advice.id})">Delete</button>
                `;
                adviceList.appendChild(div);
            });
        });
}

function addAdvice() {
    const form = document.getElementById('addForm');
    const formData = new FormData(form);

    fetch('add_advice.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        loadAdvice();
        form.reset();
    });
}

function editAdvice(id, title, description) {
    document.getElementById('updateForm').style.display = 'block';
    document.getElementById('addForm').style.display = 'none';

    document.getElementById('updateId').value = id;
    document.getElementById('updateTitle').value = title;
    document.getElementById('updateDescription').value = description;
}

function updateAdvice() {
    const form = document.getElementById('updateForm');
    const formData = new FormData(form);

    fetch('update_advice.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        loadAdvice();
        form.reset();
        form.style.display = 'none';
        document.getElementById('addForm').style.display = 'block';
    });
}

function cancelUpdate() {
    const form = document.getElementById('updateForm');
    form.reset();
    form.style.display = 'none';
    document.getElementById('addForm').style.display = 'block';
}

function deleteAdvice(id) {
    if (confirm('Are you sure you want to delete this advice?')) {
        fetch('delete_advice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            loadAdvice();
        });
    }
}

document.addEventListener('DOMContentLoaded', loadAdvice);
