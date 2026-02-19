// Examinee Management - Registered Examinees
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const tableContainer = document.getElementById('tableContainer');
    const tableBody = document.getElementById('tableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const statusAlert = document.getElementById('statusAlert');
    const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
    const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    const rescheduleForm = document.getElementById('rescheduleForm');
    const rescheduleScheduleSelect = document.getElementById('rescheduleSchedule');
    const schedulePreview = document.getElementById('schedulePreview');

    let currentPage = 1;
    let currentSearch = '';
    let currentData = {}; // Store for quick access
    let availableSchedules = []; // Store schedules for selection

    // Initialize
    loadSchedules();
    loadExamineeData();

    // Search button
    searchBtn.addEventListener('click', function() {
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        loadExamineeData();
    });

    // Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    // Schedule selection change
    rescheduleScheduleSelect.addEventListener('change', function() {
        const selectedId = this.value;
        if (selectedId) {
            const selected = availableSchedules.find(s => s.schedule_id == selectedId);
            if (selected) {
                document.getElementById('previewVenue').textContent = selected.venue_name;
                document.getElementById('previewRegion').textContent = selected.region;
                document.getElementById('previewDate').textContent = new Date(selected.scheduled_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                schedulePreview.style.display = 'block';
            }
        } else {
            schedulePreview.style.display = 'none';
        }
    });

    // Reschedule form submission
    rescheduleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        rescheduleExam();
    });

    // Load available schedules for modal
    function loadSchedules() {
        fetch('../php/get_schedules_for_rescheduling.php', {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    availableSchedules = data.data;
                    // Clear existing options except the placeholder
                    rescheduleScheduleSelect.innerHTML = '<option value="">-- Choose an available schedule --</option>';
                    
                    // Add each schedule as an option
                    data.data.forEach(schedule => {
                        const dateObj = new Date(schedule.scheduled_date);
                        const dateStr = dateObj.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        const option = document.createElement('option');
                        option.value = schedule.schedule_id;
                        option.textContent = `${schedule.venue_name} (${schedule.region}) - ${dateStr}`;
                        rescheduleScheduleSelect.appendChild(option);
                    });
                } else {
                    rescheduleScheduleSelect.innerHTML = '<option value="">No available schedules</option>';
                }
            })
            .catch(error => {
                console.error('Error loading schedules:', error);
                rescheduleScheduleSelect.innerHTML = '<option value="">Error loading schedules</option>';
            });
    }

    // Load examinee data
    function loadExamineeData() {
        showLoading(true);

        const params = new URLSearchParams();
        params.append('search', currentSearch);
        params.append('page', currentPage);

        fetch(`../php/get_registered_examinees.php?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Examinee Data:', data);
                
                if (data.success) {
                    // Update stats
                    document.getElementById('totalRegistered').textContent = data.summary.total_registered;
                    document.getElementById('totalRegistered').textContent = data.summary.total_registered;
                    document.getElementById('totalCompleted').textContent = data.summary.completed;


                    // Populate table
                    populateTable(data.data);

                    // Store data for modal access
                    data.data.forEach(record => {
                        currentData[record.user_id] = record;
                    });

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
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No registered examinees found</td></tr>';
            return;
        }

        records.forEach(record => {
            const examDate = new Date(record.exam_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const row = `
                <tr class="border-bottom">
                    <td class="fw-semibold">${escapeHtml(record.test_permit)}</td>
                    <td>${escapeHtml(record.full_name)}</td>
                    <td>
                        <a href="mailto:${escapeHtml(record.email)}" class="text-decoration-none">
                            ${escapeHtml(record.email)}
                        </a>
                    </td>
                    <td>${escapeHtml(record.exam_venue || 'Not Set')}</td>
                    <td>${record.exam_date ? examDate : 'Not Set'}</td>
                    <td class="text-end table-actions">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light" onclick="viewProfile(${record.user_id})" title="View Profile">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn btn-light" onclick="openRescheduleModal(${record.user_id})" title="Reschedule">
                                <i class="bx bx-calendar-edit"></i>
                            </button>
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
        loadExamineeData();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // View profile (global function)
    window.viewProfile = function(userId) {
        const record = currentData[userId];
        if (!record) return;

        const birthDate = new Date(record.date_of_birth).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const registrationDate = new Date(record.date_of_registration).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const examDate = record.exam_date ? new Date(record.exam_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : 'Not Set';

        const profileHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>Test Permit:</strong> ${escapeHtml(record.test_permit)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-success-subtle text-success">${escapeHtml(record.status)}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>First Name:</strong> ${escapeHtml(record.first_name)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Last Name:</strong> ${escapeHtml(record.last_name)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> ${escapeHtml(record.email)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Contact:</strong> ${escapeHtml(record.contact_number)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date of Birth:</strong> ${birthDate}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Age:</strong> ${record.age}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Gender:</strong> ${escapeHtml(record.gender)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>School:</strong> ${escapeHtml(record.school)}</p>
                </div>
                <div class="col-12">
                    <p><strong>Region:</strong> ${escapeHtml(record.region)}</p>
                </div>
                <div class="col-12">
                    <p><strong>Exam Venue:</strong> ${escapeHtml(record.exam_venue || 'Not Set')}</p>
                </div>
                <div class="col-12">
                    <p><strong>Exam Date:</strong> ${examDate}</p>
                </div>
                <div class="col-12">
                    <p><strong>Registration Date:</strong> ${registrationDate}</p>
                </div>
            </div>
        `;

        document.getElementById('profileContent').innerHTML = profileHTML;
        profileModal.show();
    };

    // Open reschedule modal (global function)
    window.openRescheduleModal = function(userId) {
        const record = currentData[userId];
        if (!record) return;

        document.getElementById('rescheduleUserId').value = userId;
        rescheduleScheduleSelect.value = ''; // Reset to placeholder
        schedulePreview.style.display = 'none'; // Hide preview initially

        rescheduleModal.show();
    };

    // Reschedule exam
    function rescheduleExam() {
        const userId = document.getElementById('rescheduleUserId').value;
        const scheduleId = rescheduleScheduleSelect.value;

        if (!scheduleId) {
            showStatus('Please select a schedule', 'danger');
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('schedule_id', scheduleId);

        fetch('../php/reschedule_exam.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Reschedule Response:', data);
                
                if (data.success) {
                    showStatus('Exam rescheduled successfully', 'success');
                    rescheduleModal.hide();
                    rescheduleForm.reset();
                    schedulePreview.style.display = 'none';
                    loadExamineeData();
                } else {
                    showStatus(data.message || 'Failed to reschedule exam', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Network error rescheduling exam', 'danger');
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
