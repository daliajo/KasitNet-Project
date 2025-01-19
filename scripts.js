//check if the user is logged in
function checkLogin() {
    const isLoggedIn = sessionStorage.getItem('loggedIn');
    if (!isLoggedIn) {
        window.location.href = 'login.php';
    }
}

//log in the user
function loginUser() {
    sessionStorage.setItem('loggedIn', true);
    window.location.href = 'index.php';
}

//log out the user
function logoutUser() {
    window.location.href = 'logout.php';
}

function bookFacility(facilityName) {
    const facilityIdInput = document.querySelector('form#bookingForm select#facility_id');
    const facilityTypeInput = document.querySelector('form#bookingForm select#facility-type');
    const bookingDateInput = document.querySelector('form#bookingForm input#booking-date');
    const bookingTimeInput = document.querySelector('form#bookingForm input#booking-time');
    const descriptionInput = document.querySelector('form#bookingForm input#description');

    //check if the form fields are filled
    if (!bookingDateInput.value || !bookingTimeInput.value) {
        alert("Please select a date and time before booking.");
        return;
    }

    //automatically set the selected facility
    const facilityOption = Array.from(facilityIdInput.options).find(option => option.textContent === facilityName);
    if (facilityOption) {
        facilityIdInput.value = facilityOption.value;
    } else {
        alert("Error: Facility not found in the dropdown.");
        return;
    }

    //submit the form
    const formData = new FormData();
    formData.append('facility_id', facilityIdInput.value);
    formData.append('date', bookingDateInput.value);
    formData.append('time', bookingTimeInput.value);
    formData.append('description', descriptionInput.value || '');

    fetch('book_facility.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(message => {
            alert(message);
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while booking the facility.');
        });
}

function editProfile() {
    const name = document.getElementById('profile-name').value;
    const email = document.getElementById('profile-email').value;
    const password = document.getElementById('profile-password').value;

    if (!name || !email) {
        alert("Name and Email are required.");
        return;
    }

    //create a form data object
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    if (password) {
        formData.append('password', password);
    }

    //send the data to the server via POST
    fetch('edit_profile.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            alert(data); //show success or error message
            if (data.includes("successfully")) {
                window.location.href = "index.php"; //redirect to the home page
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile.');
        });
}


//populate the facility dropdown based on selected type
document.addEventListener("DOMContentLoaded", function() {
    const facilityTypeSelect = document.getElementById('facility-type');
    const facilityIdSelect = document.getElementById('facility_id');

    const facilities = [
        { id: 1, name: 'Classroom 001', type: 'Classroom' },
        { id: 2, name: 'Classroom 101', type: 'Classroom' },
        { id: 3, name: 'Classroom 102', type: 'Classroom' },
        { id: 4, name: 'Classroom 103', type: 'Classroom' },
        { id: 5, name: 'Classroom 105', type: 'Classroom' },
        { id: 6, name: 'Classroom 201', type: 'Classroom' },
        { id: 7, name: 'Classroom 202', type: 'Classroom' },
        { id: 8, name: 'Classroom 203', type: 'Classroom' },
        { id: 9, name: 'Classroom 205', type: 'Classroom' },
        { id: 10, name: 'Classroom 301', type: 'Classroom' },
        { id: 11, name: 'Classroom 302', type: 'Classroom' },
        { id: 12, name: 'Classroom 303', type: 'Classroom' },
        { id: 13, name: 'Lab 001', type: 'Lab' },
        { id: 14, name: 'Lab 002', type: 'Lab' },
        { id: 15, name: 'Lab 101', type: 'Lab' },
        { id: 16, name: 'Lab 102', type: 'Lab' },
        { id: 17, name: 'Lab 103', type: 'Lab' },
        { id: 18, name: 'Lab 104', type: 'Lab' },
        { id: 19, name: 'CISCO Lab 107', type: 'Lab' },
        { id: 20, name: 'SAMSUNG Lab 106', type: 'Lab' },
        { id: 21, name: 'Creative Programming Lab 105', type: 'Lab' },
        { id: 22, name: 'Lab 201', type: 'Lab' },
        { id: 23, name: 'Lab 202', type: 'Lab' },
        { id: 24, name: 'Lab 203', type: 'Lab' },
        { id: 25, name: 'Lab 204', type: 'Lab' },
        { id: 26, name: 'Lab 205', type: 'Lab' },
        { id: 27, name: 'Lab 206', type: 'Lab' },
        { id: 28, name: 'Lab 207', type: 'Lab' },
        { id: 29, name: 'Lab 301', type: 'Lab' },
        { id: 30, name: 'Lab 302', type: 'Lab' },
        { id: 31, name: 'Lab 303', type: 'Lab' },
        { id: 32, name: 'Lab 304', type: 'Lab' },
        { id: 33, name: 'Lab 305', type: 'Lab' },
        { id: 34, name: 'Meeting Room A', type: 'Meeting Room' },
        { id: 35, name: 'Meeting Room B', type: 'Meeting Room' },
        { id: 36, name: 'Meeting Room C', type: 'Meeting Room' },
        { id: 37, name: 'Meeting Room D', type: 'Meeting Room' },
    ];

    function populateFacilities() {
        facilityIdSelect.innerHTML = ''; //clear previous options
        const selectedType = facilityTypeSelect.value;
        const filteredFacilities = selectedType === 'all' ? facilities : facilities.filter(f => f.type === selectedType);

        filteredFacilities.forEach(facility => {
            const option = document.createElement('option');
            option.value = facility.id;
            option.textContent = facility.name;
            facilityIdSelect.appendChild(option);
        });
    }

    facilityTypeSelect.addEventListener('change', populateFacilities);
    populateFacilities(); //initial population
});

//handle booking form submission with AJAX
document.addEventListener("DOMContentLoaded", function() {
    const bookingForm = document.getElementById('bookingForm');

    bookingForm.addEventListener('submit', function(event) {
        event.preventDefault(); //prevent the form from submitting normally

        const formData = new FormData(bookingForm);

        fetch('book_facility.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(message => {
            alert(message); //display the message as an alert
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    });
});

//generate the facility usage report
function generateReport() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const facilityType = document.getElementById('report-facility-type').value;
    const specificRoom = document.getElementById('report-specific-room').value;

    //example data 
    const reportData = [
        { facility: 'Classroom 101', frequency: 12, peakTime: '10:00 AM - 12:00 PM', totalHours: 24 },
        { facility: 'Lab 202', frequency: 15, peakTime: '2:00 PM - 4:00 PM', totalHours: 30 },
        { facility: 'Meeting Room A', frequency: 8, peakTime: '11:00 AM - 1:00 PM', totalHours: 16 }
    ];

    //update the charts
    renderBarChart(reportData);
    renderPieChart(reportData);

    // update the data table
    updateReportTable(reportData);
}

//render Bar Chart
function renderBarChart(data) {
    const ctx = document.getElementById('usageBarChart').getContext('2d');
    const labels = data.map(item => item.facility);
    const frequencies = data.map(item => item.frequency);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Booking Frequency',
                data: frequencies,
                backgroundColor: '#37404A'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

//Render Pie Chart
function renderPieChart(data) {
    const ctx = document.getElementById('usagePieChart').getContext('2d');
    const labels = data.map(item => item.facility);
    const totalHours = data.map(item => item.totalHours);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Hours Booked',
                data: totalHours,
                backgroundColor: ['#818C98', '#6B7787', '#37404A']
            }]
        }
    });
}

function runPredictiveAnalysis() {
    const startDate = document.getElementById('report-start-date').value;
    const endDate = document.getElementById('report-end-date').value;
    const facilityType = document.getElementById('report-facility-type').value;

    if (!startDate || !endDate) {
        alert("Please select a start and end date for predictive analysis.");
        return;
    }

    //simulated predictive analysis data 
    const predictiveData = [
        { facility: 'Classroom 101', predictedUsage: 85 },
        { facility: 'Lab 202', predictedUsage: 90 },
        { facility: 'Meeting Room A', predictedUsage: 75 },
    ];

    //display predictive analysis results
    updatePredictiveResults(predictiveData);
}

function updatePredictiveResults(data) {
    const resultsDiv = document.getElementById('predictive-results');
    resultsDiv.innerHTML = ''; // clear previous results

    const resultsTable = document.createElement('table');
    resultsTable.border = '1';
    resultsTable.cellPadding = '10';
    resultsTable.cellSpacing = '0';

    //create table headers
    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr>
            <th>Facility</th>
            <th>Predicted Usage (%)</th>
        </tr>
    `;
    resultsTable.appendChild(thead);

    //create table body with predictive data
    const tbody = document.createElement('tbody');
    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.facility}</td>
            <td>${item.predictedUsage}%</td>
        `;
        tbody.appendChild(row);
    });

    resultsTable.appendChild(tbody);
    resultsDiv.appendChild(resultsTable);
}

//updatee Report Table
function updateReportTable(data) {
    const tbody = document.getElementById('report-table-body');
    tbody.innerHTML = '';

    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.facility}</td>
            <td>${item.frequency}</td>
            <td>${item.peakTime}</td>
            <td>${item.totalHours}</td>
        `;
        tbody.appendChild(row);
    });
}


function exportReport(type) {
    if (type === 'pdf') {
        const element = document.querySelector('main'); //select the main content to export
        const options = {
            margin: 1,
            filename: 'Facility_Usage_Report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        //use html2pdf to generate the PDF
        html2pdf().set(options).from(element).save();
    } else if (type === 'excel') {
        const table = document.getElementById('report-table'); 
        if (!table) {
            alert('Table not found!');
            return;
        }

        let csvContent = '';
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('th, td');
            const rowContent = Array.from(cells)
                .map(cell => `"${cell.innerText}"`) 
                .join(',');
            csvContent += rowContent + '\n';
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', 'Facility_Usage_Report.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        alert('Invalid export type!');
    }
}