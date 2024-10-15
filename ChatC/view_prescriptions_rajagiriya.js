document.getElementById('viewBtn').addEventListener('click', function() {
    fetch('get_prescriptions_rajagiriya.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('prescriptionTableContainer');
            if (data.length === 0) {
                container.innerHTML = '<p>No prescriptions found for Rajagiriya.</p>';
                return;
            }

            let tableHTML = '<table>';
            tableHTML += '<thead><tr><th>Prescription ID</th><th>Name</th><th>Address</th><th>Phone Number</th><th>Email</th><th>Pharmacy</th><th>Picture</th><th>Action</th></tr></thead>';
            tableHTML += '<tbody>';

            data.forEach(row => {
                tableHTML += '<tr>';
                tableHTML += `<td>${row.PrescriptionID}</td>`;
                tableHTML += `<td>${row.Name}</td>`;
                tableHTML += `<td>${row.Address}</td>`;
                tableHTML += `<td>${row.PhoneNumber}</td>`;
                tableHTML += `<td>${row.Email}</td>`;
                tableHTML += `<td>${row.PharmacyName}</td>`;
                tableHTML += `<td><img src="${row.PicturePath}" alt="Picture" style="width: 100px; cursor: pointer;" onclick="window.open('${row.PicturePath}', '_blank');"></td>`;
                tableHTML += `<td><button onclick="viewPrescription(${row.PrescriptionID})">View</button></td>`;
                tableHTML += '</tr>';
            });

            tableHTML += '</tbody></table>';
            container.innerHTML = tableHTML;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});

function viewPrescription(prescriptionId) {
    window.location.href = `prescription_view.html?prescriptionId=${prescriptionId}`;
}
