// Improved and Optimized JavaScript

// Login Functionality
function handleLogin(event, role) {
    event.preventDefault();

    // Get inputs based on role
    const containerId = role === "student" ? "student" : "staff";
    const usernameInput = document.querySelector(`#${containerId} input[type='text']`).value.trim();
    const passwordInput = document.querySelector(`#${containerId} input[type='password']`).value.trim();

    // Role-based redirection logic
    if (role === "student" && usernameInput === "user" && passwordInput === "user123") {
        window.location.href = "dashboard-student.html";
    } else if (role === "staff") {
        if (usernameInput === "teacher" && passwordInput === "teacher123") {
            window.location.href = "dashboard-teacher.html";
        } else if (usernameInput === "admin" && passwordInput === "admin123") {
            window.location.href = "dashboard-admin.html";
        } else {
            alert("Invalid staff username or password. Please try again.");
        }
    } else {
        alert("Invalid username or password. Please try again.");
    }
}

// Attach event listeners to the "Login" buttons for each form
document.querySelector("#student .submit").addEventListener("click", (event) => handleLogin(event, "student"));
document.querySelector("#staff .submit").addEventListener("click", (event) => handleLogin(event, "staff"));

// Show Login Form Functionality
document.getElementById('login_btn').addEventListener('click', function () {
    document.getElementById('title-section').classList.add('hidden');
    document.getElementById('formBox').classList.add('active');
    document.getElementById('login-section').classList.remove('hidden');
});

// Return Button Functionality
function handleReturnButton(role) {
    document.getElementById('formBox').classList.remove('active');
    document.getElementById('login-section').classList.add('hidden');
    document.getElementById('title-section').classList.remove('hidden');
}

document.getElementById('returnButtonStudent').addEventListener('click', () => handleReturnButton('student'));
document.getElementById('returnButtonStaff').addEventListener('click', () => handleReturnButton('staff'));

// Toggle between Student and Staff forms
function toggleRole(role) {
    const studentForm = document.getElementById('student');
    const staffForm = document.getElementById('staff');

    if (role === 'staff') {
        staffForm.style.left = "4px";
        studentForm.style.right = "-520px";
    } else {
        staffForm.style.left = "-510px";
        studentForm.style.right = "5px";
    }
}

document.querySelector('.ontop a[onclick*="hereStaff"]').addEventListener('click', () => toggleRole('staff'));
document.querySelector('.ontop a[onclick*="hereStudent"]').addEventListener('click', () => toggleRole('student'));

/* // Facial Recognition Popup Functionality
function showFacialScanPopup() {
    document.getElementById("facial-scan-popup").style.display = "flex";
    const video = document.getElementById("video");

    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            video.srcObject = stream;
        })
        .catch((err) => {
            console.error("Error accessing webcam: ", err);
        });
}
 */
/* function closePopup() {
    document.getElementById("facial-scan-popup").style.display = "none";
    const video = document.getElementById("video");
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
    }
} */

function startScan() {
    alert("Starting facial scan...");
    setTimeout(() => {
        closePopup();
        document.getElementById("scan-success-popup").style.display = "flex";
    }, 2000);
}

function closeSuccessPopup() {
    document.getElementById("scan-success-popup").style.display = "none";
}

// Enhanced Select Mode for Table
function toggleSelectMode() {
    const table = document.querySelector('table');
    const buttonsContainer = document.querySelector('.button-container');

    if (!table.classList.contains('select-mode')) {
        const headerRow = table.querySelector('thead tr');
        headerRow.insertAdjacentHTML('beforeend', '<th>Select</th>');

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => row.insertAdjacentHTML('beforeend', '<td><input type="checkbox" class="select-checkbox" onchange="updateSendButtonState()"></td>'));

        buttonsContainer.innerHTML = `
            <button onclick="sendWarning()" class="btn send-warning-btn">Send Warning Letter</button>
            <button onclick="toggleSelectMode()" class="btn return-btn">Return</button>
        `;

        table.classList.add('select-mode');
    } else {
        table.querySelectorAll('thead th:last-child, tbody td:last-child').forEach(el => el.remove());
        buttonsContainer.innerHTML = `
            <button onclick="window.print()" class="btn print-btn">Print</button>
            <a href="view-class-teacher.php" class="btn return-btn">Return</a>
            <button onclick="toggleSelectMode()" class="btn select-btn">Select</button>
        `;
        table.classList.remove('select-mode');
    }
}

function updateSendButtonState() {
    const isAnyChecked = Array.from(document.querySelectorAll('.select-checkbox')).some(checkbox => checkbox.checked);
    const sendButton = document.querySelector('.send-warning-btn');
    sendButton.style.backgroundColor = isAnyChecked ? 'red' : '';
    sendButton.style.color = isAnyChecked ? 'white' : '';
}

function sendWarning() {
    const selectedStudents = Array.from(document.querySelectorAll('.select-checkbox:checked')).map(checkbox => {
        return checkbox.closest('tr').cells[1].textContent.trim();
    });

    if (selectedStudents.length > 0) {
        alert(`Warning letters will be sent to: ${selectedStudents.join(', ')}`);
    } else {
        alert('No students selected!');
    }
}

// Course Registration and Editing
function registerCourse() {
    const fields = ['course-name', 'course-code', 'teacher-name', 'staff-id', 'class'];
    const values = fields.map(id => document.getElementById(id).value);

    if (values.every(value => value.trim())) {
        const newRow = `<tr>${values.map(value => `<td>${value}</td>`).join('')}<td><button class="edit-button" onclick="editCourse(this)"><i class="fa-solid fa-pen-to-square"></i></button></td></tr>`;
        document.getElementById('course-table').querySelector('tbody').insertAdjacentHTML('beforeend', newRow);
        document.getElementById('course-form').reset();
    } else {
        alert('Please fill out all fields before registering.');
    }
}

function editCourse(button) {
    const row = button.closest('tr');
    const fields = ['courseName', 'courseCode', 'teacherName', 'staffId', 'class'];
    const queryString = fields.map((field, i) => `${field}=${encodeURIComponent(row.cells[i].textContent)}`).join('&');
    window.location.href = `view-course-admin.html?${queryString}`;
}




let video = document.getElementById("video");

function showFacialScanPopup() {
    console.log("Facial Scan Popup Triggered");
    document.getElementById("facial-scan-popup").style.display = "block";

    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            console.log("Camera access granted");
            video.srcObject = stream;
        })
        .catch((err) => {
            console.error("Error accessing the camera: ", err);
            alert("Unable to access the camera.");
        });
}

function startFacialRecognition(studentID) {
    console.log("Starting facial recognition for student:", studentID);

    // Stop the camera feed
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
    }

    // Perform facial recognition
    fetch("process_facial_scan.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ studentID }),
    })
        .then((response) => response.json())
        .then((data) => {
            console.log("Response from server:", data); // Log the server's response
            if (data.status === "success") {
                alert("Attendance marked successfully!");
            } else {
                alert("Facial recognition failed: " + data.message);
            }
        })
        .catch((err) => {
            console.error("Error during facial recognition:", err);
            alert("An error occurred while processing facial recognition.");
        })
        .finally(() => {
            document.getElementById("facial-scan-popup").style.display = "none";
        });
}




function closePopup() {
    const video = document.getElementById("video");
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
    }
    document.getElementById("facial-scan-popup").style.display = "none";
}
