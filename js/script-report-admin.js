function toggleSelectMode() {
    const table = document.querySelector('table');
    const buttonsContainer = document.querySelector('.button-container');

    if (!table.classList.contains('select-mode')) {
        // Add "Select" column
        const headerRow = table.querySelector('thead tr');
        headerRow.innerHTML += '<th>Select</th>';

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.innerHTML += '<td><input type="checkbox" class="select-checkbox" onchange="updateSendButtonState()"></td>';
        });

        // Update buttons
        buttonsContainer.innerHTML = `
            <button onclick="sendWarning()" class="btn send-warning-btn">Send Warning Letter</button>
            <button onclick="toggleSelectMode()" class="btn return-btn">Return</button>
        `;

        table.classList.add('select-mode');
    } else {
        // Remove "Select" column
        const headerRow = table.querySelector('thead tr');
        headerRow.lastChild.remove();

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => row.lastChild.remove());

        // Restore original buttons
        buttonsContainer.innerHTML = `
            <button onclick="window.print()" class="btn print-btn">Print</button>
            <a href="view-class-admin.html" class="btn return-btn">Return</a>
            <button onclick="toggleSelectMode()" class="btn select-btn">Select</button>
        `;

        table.classList.remove('select-mode');
    }
}

function updateSendButtonState() {
    const checkboxes = document.querySelectorAll('.select-checkbox');
    const sendButton = document.querySelector('.send-warning-btn');

    // Check if at least one checkbox is checked
    const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (isAnyChecked) {
        sendButton.style.backgroundColor = 'red';
        sendButton.style.color = 'white';
    } else {
        sendButton.style.backgroundColor = '';
        sendButton.style.color = '';
    }
}

function sendWarning() {
    const selectedStudents = [];
    const checkboxes = document.querySelectorAll('.select-checkbox:checked');

    checkboxes.forEach(checkbox => {
        const studentRow = checkbox.closest('tr');
        const studentName = studentRow.cells[1].textContent.trim(); // Assuming Name is in the second column
        selectedStudents.push(studentName);
    });

    if (selectedStudents.length === 0) {
        alert('No students selected!');
    } else {
        alert(`Warning letters will be sent to: ${selectedStudents.join(', ')}`);
    }
}