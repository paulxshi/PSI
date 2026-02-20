// Examinee Masterlist Management
document.addEventListener('DOMContentLoaded', function() {
    const uploadCsvBtn = document.getElementById('uploadCsvBtn');
    const csvFileInput = document.getElementById('csvFileInput');
    const searchInput = document.getElementById('searchInput');
    const dateFilter = document.getElementById('dateFilter');
    const searchBtn = document.getElementById('searchBtn');
    const createExamineeForm = document.getElementById('createExamineeForm');
    const tableContainer = document.getElementById('tableContainer');
    const tableBody = document.getElementById('tableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const statusAlert = document.getElementById('statusAlert');

    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let currentDate = '';
    let deleteTargetId = null;
    let deleteTargetName = '';




document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!deleteTargetId) return;

    this.disabled = true;
    this.textContent = 'Deleting...';

    const formData = new FormData();
    formData.append('id', deleteTargetId);

    fetch('../php/delete_examinee_masterlist.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showStatus('Examinee record deleted successfully', 'success');
                loadMasterlistData();
            } else {
                showStatus(data.message || 'Failed to delete record', 'danger');
            }
        })
        .catch(() => {
            showStatus('Network error deleting record', 'danger');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'Delete Examinee';
            deleteTargetId = null;

            bootstrap.Modal.getInstance(
                document.getElementById('deleteExamineeModal')
            ).hide();
        });
});

    // Initialize
    loadMasterlistData();

    // Status filter - Click on stat cards
    document.getElementById('totalUploaded').parentElement.parentElement.parentElement.style.cursor = 'pointer';
    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.style.cursor = 'pointer';
    document.getElementById('totalNotRegistered').parentElement.parentElement.parentElement.style.cursor = 'pointer';

    document.getElementById('totalUploaded').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === '' ? '' : '';
        currentPage = 1;
        loadMasterlistData();
    });

    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === '1' ? '' : '1';
        currentPage = 1;
        loadMasterlistData();
    });

    document.getElementById('totalNotRegistered').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === '0' ? '' : '0';
        currentPage = 1;
        loadMasterlistData();
    });

    // CSV Upload button
    uploadCsvBtn.addEventListener('click', function() {
        csvFileInput.click();
    });

    csvFileInput.addEventListener('change', function() {
        uploadCsvFile(this.files[0]);
    });

    // Search button
    searchBtn.addEventListener('click', function() {
        currentSearch = searchInput.value.trim();
        currentDate = dateFilter.value;
        currentPage = 1;
        loadMasterlistData();
    });

    // Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    // Date filter change
    dateFilter.addEventListener('change', function() {
        currentSearch = searchInput.value.trim();
        currentDate = this.value;
        currentPage = 1;
        loadMasterlistData();
    });

    // Create examinee form submission
    createExamineeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        createExaminee();
    });

    // Load masterlist data
    function loadMasterlistData() {
        showLoading(true);

        const params = new URLSearchParams();
        params.append('search', currentSearch);
        if (currentStatus) {
            params.append('status', currentStatus);
        }
        if (currentDate) {
            params.append('date', currentDate);
        }
        params.append('page', currentPage);

        fetch(`../php/get_examinee_masterlist.php?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Masterlist Data:', data);
                
                if (data.success) {
                    // Update stats
                    document.getElementById('totalUploaded').textContent = data.counts.total_uploaded;
                    document.getElementById('totalRegistered').textContent = data.counts.total_registered;
                    document.getElementById('totalNotRegistered').textContent = data.counts.total_not_registered;

                    // Populate table
                    populateTable(data.data);

                    // Populate pagination
                    populatePagination(data.pagination);

                    tableContainer.style.display = 'block';
                    paginationContainer.style.display = data.pagination.total_pages > 1 ? 'block' : 'none';
                } else {
                    showStatus(data.message || 'Failed to load data', 'danger');
                    tableContainer.style.display = 'none';
                    paginationContainer.style.display = 'none';
                }
                
                showLoading(false);
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error loading data', 'danger');
                tableContainer.style.display = 'none';
                paginationContainer.style.display = 'none';
                showLoading(false);
            });
            
            tableContainer.classList.remove('table-fade');
            void tableContainer.offsetWidth; // reflow
            tableContainer.classList.add('table-fade');

    }

    // Populate table rows
    function populateTable(records) {
        tableBody.innerHTML = '';

        if (records.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No records found</td></tr>';
            return;
        }

        records.forEach(record => {
            const statusBadge = record.used == 1
                ? '<span class="badge bg-success-subtle text-success">Registered</span>'
                : '<span class="badge bg-secondary-subtle text-secondary">Not Registered</span>';

            const uploadedDate = new Date(record.uploaded_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Use full_name if available (from CONCAT in query), otherwise build from parts
            const fullName = record.full_name || escapeHtml(record.first_name + ' ' + record.last_name);

            const deleteBtn = record.used == 0
                ? `<button class="btn btn-light text-danger" onclick="deleteRecord(${record.id})" title="Delete">
                        <i class="bx bx-trash"></i>
                      </button>`
                : '';

            const row = `
                <tr class="border-bottom">
                    <td class="fw-semibold">${escapeHtml(record.test_permit)}</td>
                    <td>${fullName}</td>
                    <td>
                        <a href="mailto:${escapeHtml(record.email)}" class="text-decoration-none">
                            ${escapeHtml(record.email)}
                        </a>
                    </td>
                    <td>${statusBadge}</td>
                    <td>${uploadedDate}</td>
                    <td class="text-end table-actions">
                        <div class="btn-group btn-group-sm">
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>
            `;

            tableBody.innerHTML += row;
        });
    }

    // Populate pagination
 function populatePagination(paginationData) {
    pagination.innerHTML = '';

    const { current_page, total_pages } = paginationData;

    // Previous
    pagination.innerHTML += `
        <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${current_page - 1}); return false;">
                <i class="bx bx-chevron-left"></i>
            </a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= total_pages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${i === current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i}); return false;">
                    ${i}
                </a>
            </li>
        `;
    }

    // Next
    pagination.innerHTML += `
        <li class="page-item ${current_page === total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${current_page + 1}); return false;">
                <i class="bx bx-chevron-right"></i>
            </a>
        </li>
    `;
}


    // Go to page (global function)
        window.goToPage = function(page) {
            currentPage = page;
            loadMasterlistData();
        };


    // Create examinee
    function createExaminee() {
        const testPermit = document.getElementById('testPermitInput').value.trim();
        const lastName = document.getElementById('lastNameInput').value.trim();
        const firstName = document.getElementById('firstNameInput').value.trim();
        const middleName = document.getElementById('middleNameInput').value.trim();
        const email = document.getElementById('emailInput').value.trim();

        // Clear errors
        document.getElementById('testPermitError').style.display = 'none';
        document.getElementById('lastNameError').style.display = 'none';
        document.getElementById('firstNameError').style.display = 'none';
        document.getElementById('middleNameError').style.display = 'none';
        document.getElementById('emailError').style.display = 'none';

        const formData = new FormData();
        formData.append('test_permit', testPermit);
        formData.append('last_name', lastName);
        formData.append('first_name', firstName);
        formData.append('middle_name', middleName);
        formData.append('email', email);

        fetch('../php/create_examinee_masterlist.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Create Examinee Response:', data);
                
                if (data.success) {
                    showStatus('Examinee record created successfully', 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createExamineeModal'));
                    if (modal) modal.hide();
                    
                    // Reset form
                    createExamineeForm.reset();
                    document.getElementById('testPermitInput').focus();
                    
                    // Reload data
                    currentPage = 1;
                    loadMasterlistData();
                } else {
                    // Show field-specific errors
                    if (data.message.includes('Test permit')) {
                        document.getElementById('testPermitError').textContent = data.message;
                        document.getElementById('testPermitError').style.display = 'block';
                    } else if (data.message.includes('Last name')) {
                        document.getElementById('lastNameError').textContent = data.message;
                        document.getElementById('lastNameError').style.display = 'block';
                    } else if (data.message.includes('First name')) {
                        document.getElementById('firstNameError').textContent = data.message;
                        document.getElementById('firstNameError').style.display = 'block';
                    } else if (data.message.includes('Email')) {
                        document.getElementById('emailError').textContent = data.message;
                        document.getElementById('emailError').style.display = 'block';
                    } else {
                        showStatus(data.message || 'Failed to create record', 'danger');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error creating record', 'danger');
            });
    }

    // Delete record (global function)
        window.deleteRecord = function(id) {
            deleteTargetId = id;

            // Get name from row (safe + simple)
            const row = event.target.closest('tr');
            deleteTargetName = row.children[1].textContent;

            document.getElementById('deleteExamineeName').textContent = deleteTargetName;

            new bootstrap.Modal(
                document.getElementById('deleteExamineeModal')
            ).show();
        };

    // Upload CSV file
    function uploadCsvFile(file) {
        if (!file) return;

        if (!file.name.endsWith('.csv')) {
            showStatus('Please upload a CSV file only', 'danger');
            csvFileInput.value = '';
            return;
        }

        showLoading(true);

        const formData = new FormData();
        formData.append('csvFile', file);

        fetch('../php/upload_examinee_csv.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('CSV Upload Response:', data);
                
                if (data.success) {
                    const message = `CSV uploaded successfully! Processed: ${data.successCount} records. Errors: ${data.errorCount}`;
                    showStatus(message, 'success');
                    
                    // Reset file input
                    csvFileInput.value = '';
                    
                    // Reload data
                    currentPage = 1;
                    loadMasterlistData();
                } else {
                    let errorMsg = data.message || 'Failed to upload CSV';
                    if (data.errors && data.errors.length > 0) {
                        errorMsg += '\n\nErrors:\n' + data.errors.slice(0, 5).join('\n');
                    }
                    showStatus(errorMsg, 'danger');
                    csvFileInput.value = '';
                    showLoading(false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error uploading CSV', 'danger');
                csvFileInput.value = '';
                showLoading(false);
            });
    }

    // Show loading spinner
    function showLoading(show) {
        loadingSpinner.style.display = show ? 'block' : 'none';
    }

    // Show status message
    function showStatus(message, type) {
        statusAlert.textContent = message;
        statusAlert.className = 'alert alert-' + type;
        statusAlert.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            statusAlert.style.display = 'none';
        }, 5000);
    }

    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});
