// Schedule Management JavaScript
let allSchedules = [];
let filteredSchedules = [];
let currentPage = 1;
const rowsPerPage = 8;

// Load schedules on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSchedules();
    
    // Setup event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterSchedules);
    }
    
    // Filter functionality
    const regionFilter = document.getElementById('regionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    if (regionFilter) regionFilter.addEventListener('change', filterSchedules);
    if (statusFilter) statusFilter.addEventListener('change', filterSchedules);
    if (sortFilter) sortFilter.addEventListener('change', filterSchedules);
}

// Load all schedules from database
async function loadSchedules() {
    try {
        const response = await fetch('php/get_all_schedules.php');
        const data = await response.json();
        
        if (data.success) {
            allSchedules = data.schedules;
            filteredSchedules = [...allSchedules];
            displaySchedules();
            updateTotalCount();
        } else {
            showAlert('Error loading schedules: ' + data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error loading schedules. Please refresh the page.', 'danger');
    }
}

// Display schedules in table
function displaySchedules() {
    const tbody = document.getElementById('scheduleTableBody');
    if (!tbody) return;

    // Pagination calculation
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedSchedules = filteredSchedules.slice(start, end);

    if (paginatedSchedules.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class='bx bx-calendar-x fs-1 text-muted'></i>
                    <p class="text-muted mt-2">No schedules found</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = paginatedSchedules.map((schedule, index) => `
        <tr class="border-bottom">
            <td data-label="#">${start + index + 1}</td>
            <td data-label="Region">${schedule.region}</td>
            <td data-label="Venue">${schedule.venue_name}</td>
            <td data-label="Date">${schedule.scheduled_date}</td>
            <td data-label="Capacity">
                ${schedule.num_registered} / ${schedule.capacity}
            </td>
            <td data-label="Exam Fee">₱${schedule.price}</td>
            <td data-label="Status">
                <span class="badge ${getStatusBadge(schedule.status)}">
                    ${schedule.status}
                </span>
            </td>
            <td data-label="Actions" class="text-end">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-light"
                        onclick="editSchedule(${schedule.schedule_id})">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-light text-danger"
                        onclick="confirmDelete(${schedule.schedule_id}, '${schedule.venue_name.replace(/'/g, "\\'")}')">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    renderPagination();
}
function renderPagination() {
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;

    const totalPages = Math.ceil(filteredSchedules.length / rowsPerPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let buttons = `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <button class="page-link" onclick="changePage(${currentPage - 1})">
                <i class="bx bx-chevron-left"></i>
            </button>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        buttons += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <button class="page-link" onclick="changePage(${i})">
                    ${i}
                </button>
            </li>
        `;
    }

    buttons += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <button class="page-link" onclick="changePage(${currentPage + 1})">
               <i class="bx bx-chevron-right"></i>
            </button>
        </li>
    `;

    paginationContainer.innerHTML = `
        <nav class="mt-4">
            <ul class="pagination justify-content-center custom-pagination">
                ${buttons}
            </ul>
        </nav>
    `;
}
function changePage(page) {
    const totalPages = Math.ceil(filteredSchedules.length / rowsPerPage);
    if (page < 1 || page > totalPages) return;

    currentPage = page;
    displaySchedules();
}

function getStatusBadge(status) {
    switch (status.toLowerCase()) {
        case 'incoming': return 'text-primary fs-7 fw-bold';
        case 'closed': return 'text-danger fs-7 fw-bold';  
        case 'completed': return 'text-success fs-7 fw-bold';
        default: return 'bg-secondary fs-7 fw-bold'; 
    }
}


// Filter schedules
function filterSchedules() {
    currentPage = 1; // RESET PAGE

    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const regionFilter = document.getElementById('regionFilter')?.value || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    const sortFilter = document.getElementById('sortFilter')?.value || '';

    filteredSchedules = allSchedules.filter(schedule => {
        return (
            schedule.venue_name.toLowerCase().includes(searchTerm) ||
            schedule.scheduled_date.toLowerCase().includes(searchTerm) ||
            schedule.region.toLowerCase().includes(searchTerm)
        ) &&
        (!regionFilter || schedule.region === regionFilter) &&
        (!statusFilter || schedule.status.toLowerCase() === statusFilter.toLowerCase());
    });

    if (sortFilter === 'newest') {
        filteredSchedules.sort((a, b) => new Date(b.scheduled_date) - new Date(a.scheduled_date));
    } else if (sortFilter === 'oldest') {
        filteredSchedules.sort((a, b) => new Date(a.scheduled_date) - new Date(b.scheduled_date));
    }

    displaySchedules();
    updateTotalCount();
}


// Update total count
function updateTotalCount() {
    const countElement = document.getElementById('totalCount');
    if (countElement) {
        countElement.textContent = filteredSchedules.length;
    }
}

// Edit schedule
function editSchedule(scheduleId) {
    const schedule = allSchedules.find(s => s.schedule_id === scheduleId);
    if (!schedule) return;
    
    // Extract date from scheduled_date
    const date = schedule.scheduled_date;
    
    // Populate modal
    document.getElementById('editScheduleId').value = schedule.schedule_id;
    document.getElementById('editRegion').value = schedule.region;
    document.getElementById('editVenue').value = schedule.venue_name;
    document.getElementById('editDate').value = date;
    document.getElementById('editCapacity').value = schedule.capacity;
    document.getElementById('editPrice').value = parseFloat(schedule.price);
    document.getElementById('editStatus').value = schedule.status;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
    modal.show();
}

// Save schedule changes
async function saveScheduleChanges() {
    const form = document.getElementById('editScheduleForm');
    const formData = new FormData(form);
    
    // Disable save button
    const saveBtn = document.getElementById('saveScheduleBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    try {
        const response = await fetch('php/update_schedule.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editScheduleModal'));
            modal.hide();
            
            // Reload schedules
            await loadSchedules();
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error updating schedule. Please try again.', 'danger');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = 'Save Changes';
    }
}

// Confirm delete
function confirmDelete(scheduleId, venueName) {
    document.getElementById('deleteScheduleId').value = scheduleId;
    document.getElementById('deleteVenueName').textContent = venueName;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

// Delete schedule
async function deleteSchedule() {
    const scheduleId = document.getElementById('deleteScheduleId').value;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    
    try {
        const formData = new FormData();
        formData.append('schedule_id', scheduleId);
        
        const response = await fetch('php/delete_schedule.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
            modal.hide();
            
            // Reload schedules
            await loadSchedules();
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error deleting schedule. Please try again.', 'danger');
    } finally {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = 'Delete Schedule';
    }
}

// Show alert
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
