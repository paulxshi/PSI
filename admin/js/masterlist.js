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
    updateCardHighlights(); // Highlight Total Uploaded card by default
    loadMasterlistData();

    // Status filter - Click on stat cards
    document.getElementById('totalUploaded').parentElement.parentElement.parentElement.style.cursor = 'pointer';
    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.style.cursor = 'pointer';
    document.getElementById('totalNotRegistered').parentElement.parentElement.parentElement.style.cursor = 'pointer';

    document.getElementById('totalUploaded').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = '';
        currentPage = 1;
        updateCardHighlights();
        loadMasterlistData();
    });

    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === '1' ? '' : '1';
        currentPage = 1;
        updateCardHighlights();
        loadMasterlistData();
    });

    document.getElementById('totalNotRegistered').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === '0' ? '' : '0';
        currentPage = 1;
        updateCardHighlights();
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

    // Update card highlights based on current filter
    function updateCardHighlights() {
        const uploadedCard = document.getElementById('totalUploaded').parentElement.parentElement.parentElement;
        const registeredCard = document.getElementById('totalRegistered').parentElement.parentElement.parentElement;
        const notRegisteredCard = document.getElementById('totalNotRegistered').parentElement.parentElement.parentElement;

        // Remove all highlights
        uploadedCard.classList.remove('border-primary', 'shadow', 'border-3');
        registeredCard.classList.remove('border-success', 'shadow', 'border-3');
        notRegisteredCard.classList.remove('border-warning', 'shadow', 'border-3');

        // Add highlight to active filter
        if (currentStatus === '1') {
            registeredCard.classList.add('border-success', 'border-3', 'shadow');
        } else if (currentStatus === '0') {
            notRegisteredCard.classList.add('border-warning', 'border-3', 'shadow');
        } else {
            // When showing all, highlight Total Uploaded
            uploadedCard.classList.add('border-primary', 'border-3', 'shadow');
        }
    }

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
                ? '<span class="text-success fw-semibold">Registered</span>'
                : '<span class="text-secondary fw-semibold">Not Registered</span>';

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
        const maxPagesToShow = 3;

        // Determine the range of pages to show
        let startPage = Math.max(1, current_page - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(total_pages, startPage + maxPagesToShow - 1);

        // Adjust the start page if the end page is close to total pages
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        // Previous
        pagination.innerHTML += `
            <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${current_page - 1}); return false;">
                    <i class="bx bx-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
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

        // Clear errors (check if elements exist first)
        const errors = ['testPermitError', 'lastNameError', 'firstNameError', 'middleNameError', 'emailError'];
        errors.forEach(errorId => {
            const element = document.getElementById(errorId);
            if (element) element.style.display = 'none';
        });

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
                    // Update modal with created test permit
                    document.getElementById('createdTestPermit').textContent = testPermit;
                    
                    // Close create modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createExamineeModal'));
                    if (modal) modal.hide();
                    
                    // Reset form
                    createExamineeForm.reset();
                    
                    // Show success modal
                    setTimeout(() => {
                        const successModal = new bootstrap.Modal(document.getElementById('createExamineeSuccessModal'));
                        successModal.show();
                    }, 300);
                    
                    // Reload data
                    currentPage = 1;
                    loadMasterlistData();
                } else {
                    console.log('Error detected:', data.message);
                    console.log('Duplicate type:', data.duplicate_type);
                    console.log('Existing data:', data.existing_data);
                    
                    // Check if it's a duplicate error with existing data
                    if (data.duplicate_type && data.existing_data) {
                        console.log('Showing duplicate alert modal...');
                        // Close create modal
                        const createModal = bootstrap.Modal.getInstance(document.getElementById('createExamineeModal'));
                        if (createModal) {
                            createModal.hide();
                        }
                        
                        // Reset form
                        createExamineeForm.reset();
                        
                        // Show duplicate alert modal with a small delay to allow first modal to close
                        setTimeout(() => {
                            let duplicateValue = '';
                            if (data.duplicate_type === 'test_permit') {
                                duplicateValue = testPermit;
                            } else if (data.duplicate_type === 'email') {
                                duplicateValue = email;
                            } else if (data.duplicate_type === 'name') {
                                duplicateValue = firstName + ' ' + lastName;
                            }
                            
                            showDuplicateAlert(
                                data.duplicate_type,
                                duplicateValue,
                                data.existing_data
                            );
                        }, 300);
                    } else if (data.message.includes('Last name')) {
                        const el = document.getElementById('lastNameError');
                        if (el) {
                            el.textContent = data.message;
                            el.style.display = 'block';
                        }
                    } else if (data.message.includes('First name')) {
                        const el = document.getElementById('firstNameError');
                        if (el) {
                            el.textContent = data.message;
                            el.style.display = 'block';
                        }
                    } else if (data.message.includes('Invalid email')) {
                        const el = document.getElementById('emailError');
                        if (el) {
                            el.textContent = data.message;
                            el.style.display = 'block';
                        }
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
                    // Update modal with upload results
                    document.getElementById('csvSuccessCount').textContent = data.successCount || 0;
                    document.getElementById('csvErrorCount').textContent = data.errorCount || 0;
                    
                    // Show success modal
                    const modal = new bootstrap.Modal(document.getElementById('csvSuccessModal'));
                    modal.show();
                    
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

    // Show duplicate alert modal
    function showDuplicateAlert(type, value, existingData = {}) {
        console.log('showDuplicateAlert called with:', { type, value, existingData });
        
        const modal = new bootstrap.Modal(document.getElementById('duplicateAlertModal'));
        const messageEl = document.getElementById('duplicateMessage');
        const detailsEl = document.getElementById('duplicateDetails');
        
        console.log('Modal elements:', { modal, messageEl, detailsEl });
        
        let message = '';
        let details = '';
        
        if (type === 'email') {
            message = `The email address <strong>${escapeHtml(value)}</strong> is already registered in the system.`;
            if (existingData.test_permit || existingData.full_name) {
                details = `
                    <div class="mb-2"><strong>Existing Record:</strong></div>
                    ${existingData.test_permit ? `<div><span class="text-muted">Test Permit:</span> <strong>${escapeHtml(existingData.test_permit)}</strong></div>` : ''}
                    ${existingData.full_name ? `<div><span class="text-muted">Name:</span> ${escapeHtml(existingData.full_name)}</div>` : ''}
                    ${existingData.email ? `<div><span class="text-muted">Email:</span> ${escapeHtml(existingData.email)}</div>` : ''}
                `;
            }
        } else if (type === 'test_permit') {
            message = `The test permit <strong>${escapeHtml(value)}</strong> is already registered in the system.`;
            if (existingData.email || existingData.full_name) {
                details = `
                    <div class="mb-2"><strong>Existing Record:</strong></div>
                    ${existingData.test_permit ? `<div><span class="text-muted">Test Permit:</span> <strong>${escapeHtml(existingData.test_permit)}</strong></div>` : ''}
                    ${existingData.full_name ? `<div><span class="text-muted">Name:</span> ${escapeHtml(existingData.full_name)}</div>` : ''}
                    ${existingData.email ? `<div><span class="text-muted">Email:</span> ${escapeHtml(existingData.email)}</div>` : ''}
                `;
            }
        } else if (type === 'name') {
            message = `An examinee with the name <strong>${escapeHtml(existingData.full_name || value)}</strong> is already registered in the system.`;
            if (existingData.test_permit || existingData.email) {
                details = `
                    <div class="mb-2"><strong>Existing Record:</strong></div>
                    ${existingData.test_permit ? `<div><span class="text-muted">Test Permit:</span> <strong>${escapeHtml(existingData.test_permit)}</strong></div>` : ''}
                    ${existingData.full_name ? `<div><span class="text-muted">Name:</span> ${escapeHtml(existingData.full_name)}</div>` : ''}
                    ${existingData.email ? `<div><span class="text-muted">Email:</span> ${escapeHtml(existingData.email)}</div>` : ''}
                `;
            }
        } else if (type === 'both') {
            message = `Both the email and test permit are already registered in the system.`;
            if (existingData.email || existingData.test_permit) {
                details = `
                    <div class="mb-2"><strong>Existing Record:</strong></div>
                    ${existingData.test_permit ? `<div><span class="text-muted">Test Permit:</span> <strong>${escapeHtml(existingData.test_permit)}</strong></div>` : ''}
                    ${existingData.full_name ? `<div><span class="text-muted">Name:</span> ${escapeHtml(existingData.full_name)}</div>` : ''}
                    ${existingData.email ? `<div><span class="text-muted">Email:</span> ${escapeHtml(existingData.email)}</div>` : ''}
                `;
            }
        }
        
        console.log('Populated message and details:', { message, details });
        
        messageEl.innerHTML = message;
        detailsEl.innerHTML = details || '<div class="text-muted text-center">No additional details available</div>';
        
        console.log('About to show modal...');
        modal.show();
        console.log('Modal.show() called');
    }
});
