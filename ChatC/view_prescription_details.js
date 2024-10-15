function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

const prescriptionId = getQueryParam('prescriptionId');

if (prescriptionId) {
    fetch(`get_prescription_by_id.php?prescriptionId=${prescriptionId}`)
        .then(response => response.json())
        .then(data => {
            if (!data) {
                alert('No prescription details found.');
                return;
            }

            // Populate the form fields with the data
            document.getElementById('prescriptionId').value = data.PrescriptionID;
            document.getElementById('name').value = data.Name;
            document.getElementById('address').value = data.Address;
            document.getElementById('phone').value = data.PhoneNumber;
            document.getElementById('email').value = data.Email;
            document.getElementById('pharmacy').value = data.PharmacyName;
            document.getElementById('picture').src = data.PicturePath;
            document.getElementById('picturePath').value = data.PicturePath;  // Set hidden picturePath field
        })
        .catch(error => {
            console.error('Error fetching prescription details:', error);
        });
}
