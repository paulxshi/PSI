// Examinee Masterlist Management
document.addEventListener('DOMContentLoaded', function() {
    const uploadCsvBtn = document.getElementById('uploadCsvBtn');
    const csvFileInput = document.getElementById('csvFileInput');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
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

    // Initialize
    loadMasterlistData();

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
        currentStatus = statusFilter.value;
        currentPage = 1;
        loadMasterlistData();
    });

    // Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    // Status filter change
    statusFilter.addEventListener('change', function() {
        currentSearch = searchInput.value.trim();
        currentStatus = this.value;
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

            const deleteBtn = record.used == 0
                ? `<button class="btn btn-light text-danger" onclick="deleteRecord(${record.id})" title="Delete">
                        <i class="bx bx-trash"></i>
                      </button>`
                : '';

            const row = `
                <tr class="border-bottom">
                    <td class="fw-semibold">${escapeHtml(record.test_permit)}</td>
                    <td>${escapeHtml(record.full_name)}</td>
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

        // Previous button
        if (current_page > 1) {
            pagination.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="goToPage(${current_page - 1}); return false;">
                        <i class="bx bx-left-arrow"></i>
                    </a>
                </li>
            `;
        }

        // Page numbers
        for (let i = 1; i <= total_pages; i++) {
            if (i === current_page) {
                pagination.innerHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                pagination.innerHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
                    </li>
                `;
            }
        }

        // Next button
        if (current_page < total_pages) {
            pagination.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="goToPage(${current_page + 1}); return false;">
                        <i class="bx bx-right-arrow"></i>
                    </a>
                </li>
            `;
        }
    }

    // Go to page (global function)
    window.goToPage = function(page) {
        currentPage = page;
        loadMasterlistData();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Create examinee
    function createExaminee() {
        const testPermit = document.getElementById('testPermitInput').value.trim();
        const fullName = document.getElementById('fullNameInput').value.trim();
        const email = document.getElementById('emailInput').value.trim();

        // Clear errors
        document.getElementById('testPermitError').style.display = 'none';
        document.getElementById('fullNameError').style.display = 'none';
        document.getElementById('emailError').style.display = 'none';

        const formData = new FormData();
        formData.append('test_permit', testPermit);
        formData.append('full_name', fullName);
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
                    } else if (data.message.includes('Full name')) {
                        document.getElementById('fullNameError').textContent = data.message;
                        document.getElementById('fullNameError').style.display = 'block';
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
        if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
            return;
        }

        const formData = new FormData();
        formData.append('id', id);

        fetch('../php/delete_examinee_masterlist.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Delete Response:', data);
                
                if (data.success) {
                    showStatus('Record deleted successfully', 'success');
                    loadMasterlistData();
                } else {
                    showStatus(data.message || 'Failed to delete record', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error deleting record', 'danger');
            });
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
