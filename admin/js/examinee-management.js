// Examinee Management - Registered Examinees
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const regionFilter = document.getElementById('regionFilter');
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
    const filterRegion = document.getElementById('filterRegion');

    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = ''; 
    let currentRegion = ''; 
    let currentData = {}; 
    let availableSchedules = []; 
    let searchTimeout = null;


    // Initialize
    loadSchedules();
    loadSummaryStats(); 
    updateCardHighlights(); 
    loadExamineeData(); 

    filterRegion.addEventListener('change', renderFilteredSchedules);

    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.style.cursor = 'pointer';
    document.getElementById('totalCompleted').parentElement.parentElement.parentElement.style.cursor = 'pointer';

    // Total Registered: Show examinees with status='Scheduled' EXCLUDING completed (can be rescheduled)
    document.getElementById('totalRegistered').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = ''; // Shows registered examinees (excludes completed)
        currentPage = 1;
        updateCardHighlights();
        loadExamineeData();
    });

    // Completed: Show only examinees with examinee_status='Completed' (cannot be rescheduled)
    document.getElementById('totalCompleted').parentElement.parentElement.parentElement.addEventListener('click', function() {
        currentStatus = currentStatus === 'Completed' ? '' : 'Completed';
        currentPage = 1;
        updateCardHighlights();
        loadExamineeData();
    });

    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadExamineeData();
        }, 500); // Wait 500ms after user stops typing
    });

    // Enter key in search input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadExamineeData();
        }
    });

    // Region filter change
    regionFilter.addEventListener('change', function() {
        currentRegion = this.value;
        currentPage = 1;
        loadExamineeData();
    });

    // Schedule selection change
    rescheduleScheduleSelect.addEventListener('change', function() {
        const selectedId = this.value;
        if (selectedId) {
            const selected = availableSchedules.find(s => s.schedule_id == selectedId);
            if (selected) {
                document.getElementById('previewVenue').textContent = selected.venue_name;
                document.getElementById('previewRegion').textContent = selected.region;
                document.getElementById('previewDate').textContent = new Date(selected.scheduled_date + 'T00:00:00').toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('previewCapacity').textContent = selected.num_of_examinees;
                document.getElementById('previewSlots').textContent = selected.available_slots || 0;
                schedulePreview.style.display = 'block';
            }
        } else {
            schedulePreview.style.display = 'none';
        }
    });

        function populateScheduleFilters() {
            const regions = [...new Set(availableSchedules.map(s => s.region))];

            filterRegion.innerHTML = '<option value="">All Regions</option>';

            regions.forEach(r => {
                filterRegion.innerHTML += `<option value="${r}">${r}</option>`;
            });
        }
        function renderFilteredSchedules() {
            const region = filterRegion.value;

            const filtered = availableSchedules.filter(s => {
                if (region && s.region !== region) return false;
                return true;
            });

            rescheduleScheduleSelect.innerHTML =
                '<option value="">-- Choose a schedule --</option>';

            filtered.forEach(s => {
                const dateStr = new Date(s.scheduled_date + 'T00:00:00')
                    .toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                rescheduleScheduleSelect.innerHTML += `
                    <option value="${s.schedule_id}">
                        ${s.venue_name} (${s.region}) - ${dateStr} [${s.available_slots || 0} slots]
                    </option>
                `;
            });
        }

    // Reschedule form submission
    rescheduleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        rescheduleExam();
    });

    // Load available schedules for modal
    function loadSchedules() {
        fetch('php/get_schedules_for_rescheduling.php', {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    availableSchedules = data.data;
                    
                    // Populate filter dropdowns with unique regions and venues
                    populateScheduleFilters();
                    
                    renderFilteredSchedules();
                } else {
                    availableSchedules = [];
                    rescheduleScheduleSelect.innerHTML = '<option value="">No available schedules</option>';
                }
            })
            .catch(error => {
                console.error('Error loading schedules:', error);
                availableSchedules = [];
                rescheduleScheduleSelect.innerHTML = '<option value="">Error loading schedules</option>';
            });
    }

    // Update card highlights based on active filter
    function updateCardHighlights() {
        const registeredCard = document.getElementById('totalRegistered').parentElement.parentElement.parentElement;
        const completedCard = document.getElementById('totalCompleted').parentElement.parentElement.parentElement;

        // Remove all highlights
        registeredCard.classList.remove('border-primary', 'shadow', 'border-3');
        completedCard.classList.remove('border-success', 'shadow', 'border-3');

        // Add highlight to active filter
        if (currentStatus === 'Completed') {
            completedCard.classList.add('border-success', 'border-3', 'shadow');
        } else if (currentStatus === '' || !currentStatus) {
            // When showing all, highlight Total Registered
            registeredCard.classList.add('border-primary', 'border-3', 'shadow');
        }
    }

    function updateFilterBadge() {
    }

    function loadSummaryStats() {
        fetch('php/get_registered_examinees.php?page=1&limit=0', {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.summary) {
                    document.getElementById('totalRegistered').textContent = data.summary.total_registered;
                    document.getElementById('totalCompleted').textContent = data.summary.completed;
                }
            })
            .catch(error => {
                console.error('Error loading summary stats:', error);
                document.getElementById('totalRegistered').textContent = '0';
                document.getElementById('totalCompleted').textContent = '0';
            });
    }

    function loadExamineeData() {
        showLoading(true);

        const params = new URLSearchParams();
        params.append('search', currentSearch);
        params.append('page', currentPage);
        if (currentStatus) {
            params.append('status', currentStatus);
        }
        if (currentRegion) {
            params.append('region', currentRegion);
        }

        fetch(`php/get_registered_examinees.php?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Examinee Data:', data);
                
                if (data.success) {
                    document.getElementById('totalRegistered').textContent = data.summary.total_registered;
                    document.getElementById('totalRegistered').textContent = data.summary.total_registered;
                    document.getElementById('totalCompleted').textContent = data.summary.completed;


                    populateTable(data.data);

                    data.data.forEach(record => {
                        currentData[record.user_id] = record;
                    });

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

    function updateTableHeader() {
        const dateHeader = document.getElementById('dateColumnHeader');
        if (dateHeader) {
            dateHeader.textContent = currentStatus === 'Completed' ? 'Scanned Date' : 'Exam Date';
        }
    }

    function populateTable(records) {
        tableBody.innerHTML = '';
        updateTableHeader(); 

        if (records.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No registered examinees found</td></tr>';
            return;
        }

        records.forEach(record => {
            let displayDate;
            if (currentStatus === 'Completed' && record.completed_date) {
                const completedDateTime = new Date(record.completed_date);
                displayDate = completedDateTime.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                displayDate = record.exam_date ? new Date(record.exam_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) : 'Not Set';
            }

            // Show reschedule button only if examinee_status is NOT 'Completed'
            const isCompleted = record.examinee_status === 'Completed';
            const rescheduleBtn = !isCompleted 
                ? `<button class="btn btn-light" onclick="openRescheduleModal(${record.user_id})" title="Reschedule">
                        <i class="bx bx-calendar-edit"></i>
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
                    <td>${escapeHtml(record.exam_venue || 'Not Set')}</td>
                    <td>${displayDate}</td>
                    <td class="text-end table-actions">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-light" onclick="viewProfile(${record.user_id})" title="View Profile">
                                <i class="bx bx-show"></i>
                            </button>
                            ${rescheduleBtn}
                        </div>
                    </td>
                </tr>
            `;

            tableBody.innerHTML += row;
        });
    }

    function populatePagination(paginationData) {
        pagination.innerHTML = '';

        const { current_page, total_pages } = paginationData;

        pagination.innerHTML += `
            <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${current_page - 1}); return false;">
                    <i class="bx bx-chevron-left"></i>
                </a>
            </li>
        `;

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

    window.goToPage = function(page) {
        currentPage = page;
        loadExamineeData();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

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

        const isCompleted = record.examinee_status === 'Completed';
        const statusClass = isCompleted ? 'completed' : 'registered';
        const statusLabel = isCompleted ? 'Completed' : record.status;

        const modalSubtitle = document.getElementById('profileModalSubtitle');
        if (modalSubtitle) {
            modalSubtitle.textContent = isCompleted ? 'Completed Examinee Information' : 'Registered Examinee Information';
        }

        const profileHTML = `
        <div class="compact-profile">

        <!-- Header -->
        <div class="compact-header">
            <div class="avatar">
            ${escapeHtml(record.first_name.charAt(0))}
            </div>

            <div class="header-text">
            <div class="name">${escapeHtml(record.full_name)}</div>
            <div class="meta">${escapeHtml(record.test_permit)} · ${escapeHtml(record.email)}</div>
            </div>

            <span class="status-pill ${statusClass}">
            ${escapeHtml(statusLabel)}
            </span>
        </div>

        <!-- Info Grid -->
        <div class="info-card">
            <div class="info-grid">

            <div>
                <label>Exam</label>
                <span>${escapeHtml(record.exam_venue || 'Not Set')}</span>
            </div>

            <div>
                <label>Date</label>
                <span>${examDate}</span>
            </div>

            <div>
                <label>Region</label>
                <span>${escapeHtml(record.region)}</span>
            </div>

            <div>
                <label>Registered</label>
                <span>${registrationDate}</span>
            </div>

            <div>
                <label>DOB</label>
                <span>${birthDate}</span>
            </div>

            <div>
                <label>Age</label>
                <span>${record.age}</span>
            </div>

            <div>
                <label>Gender</label>
                <span>${escapeHtml(record.gender)}</span>
            </div>

            <div>
                <label>Contact</label>
                <span>${escapeHtml(record.contact_number)}</span>
            </div>

            <div>
                <label>School</label>
                <span>${escapeHtml(record.school || '—')}</span>
            </div>

            </div>
        </div>

        </div>
        `;

        document.getElementById('profileContent').innerHTML = profileHTML;
        profileModal.show();
    };

        window.openRescheduleModal = function(userId) {
            document.getElementById('rescheduleUserId').value = userId;

            filterRegion.value = '';

            schedulePreview.style.display = 'none';
            renderFilteredSchedules();

            rescheduleModal.show();
        };


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

        fetch('php/reschedule_exam.php', {
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

    function showLoading(show) {
        loadingSpinner.style.display = show ? 'block' : 'none';
    }

    function showInitialMessage() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bx bx-info-circle fs-1 mb-3 d-block"></i>
                        <h5 class="mb-2">Select a Status to View Examinees</h5>
                        <p class="mb-0">Click on "Total Registered" or "Completed" above to view examinees</p>
                    </div>
                </td>
            </tr>
        `;
        tableContainer.style.display = 'block';
        paginationContainer.style.display = 'none';
    }

    function showStatus(message, type) {
        statusAlert.textContent = message;
        statusAlert.className = 'alert alert-' + type;
        statusAlert.style.display = 'block';
        
        setTimeout(() => {
            statusAlert.style.display = 'none';
        }, 5000);
    }

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
