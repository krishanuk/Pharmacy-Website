document.getElementById('submitBtn').addEventListener('click', function() {
    const form = document.getElementById('prescriptionForm');
    const formData = new FormData(form);

    // Prepare the AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'submit_prescription.php', true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Data added successfully!');
            form.reset();
        } else {
            alert('Error adding data.');
        }
    };
    
    xhr.send(formData);
});
