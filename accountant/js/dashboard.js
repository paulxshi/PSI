// Dashboard functionality for Accountant Portal
document.addEventListener('DOMContentLoaded', function() {
    // Load statistics and paid examinees on page load
    loadPaymentStatistics();
    loadPaidExaminees();
    
    // Setup filter event listeners
    setupFilters();
    
    // Initialize Flatpickr for date inputs
    initializeDatePickers();
});

// Global variables for pagination
let currentPage = 1;
let allExaminees = []; // Store all examinees data
const itemsPerPage = 10;

// Load payment statistics (region cards and overall stats)
function loadPaymentStatistics() {
    fetch('php/get_payment_statistics.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRegionCards(data.regions);
            } else {
                console.error('Error loading statistics:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching statistics:', error);
        });
}

// Update region cards with real data
function updateRegionCards(regions) {
    // Update Luzon
    const luzonCard = document.querySelector('.luzon-revenue');
    if (luzonCard) {
        luzonCard.textContent = '₱' + regions.Luzon.revenue;
    }
    const luzonSub = document.querySelector('.luzon-sub');
    if (luzonSub) {
        luzonSub.innerHTML = `${regions.Luzon.examinees} paid examinees &nbsp;•&nbsp; ${regions.Luzon.payments} payments`;
    }
    
    // Update Visayas
    const visayasCard = document.querySelector('.visayas-revenue');
    if (visayasCard) {
        visayasCard.textContent = '₱' + regions.Visayas.revenue;
    }
    const visayasSub = document.querySelector('.visayas-sub');
    if (visayasSub) {
        visayasSub.innerHTML = `${regions.Visayas.examinees} paid examinees &nbsp;•&nbsp; ${regions.Visayas.payments} payments`;
    }
    
    // Update Mindanao
    const mindanaoCard = document.querySelector('.mindanao-revenue');
    if (mindanaoCard) {
        mindanaoCard.textContent = '₱' + regions.Mindanao.revenue;
    }
    const mindanaoSub = document.querySelector('.mindanao-sub');
    if (mindanaoSub) {
        mindanaoSub.innerHTML = `${regions.Mindanao.examinees} paid examinees &nbsp;•&nbsp; ${regions.Mindanao.payments} payments`;
    }
}

// Load paid examinees list
function loadPaidExaminees(filters = {}) {
    const queryParams = new URLSearchParams(filters).toString();
    const url = `php/get_paid_examinees.php${queryParams ? '?' + queryParams : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allExaminees = data.data; // Store all data
                currentPage = 1; // Reset to first page
                displayCurrentPage();
                updateCount(data.count);
                updatePagination();
            } else {
                console.error('Error loading examinees:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching examinees:', error);
        });
}

// Display current page of examinees
function displayCurrentPage() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = allExaminees.slice(startIndex, endIndex);
    
    displayPaidExaminees(pageData, startIndex);
}

// Display paid examinees in a table
function displayPaidExaminees(examinees, startIndex = 0) {
    const tableBody = document.querySelector('#paid-examinees-table tbody');
    const tableContainer = document.querySelector('#tableContainer');
    
    if (!tableBody) return;
    
    // Add fade effect
    if (tableContainer) {
        tableContainer.classList.add('table-fade');
        setTimeout(() => tableContainer.classList.remove('table-fade'), 200);
    }
    
    if (examinees.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No paid examinees found</td></tr>';
        return;
    }
    
    let html = '';
    examinees.forEach((examinee, index) => {
        html += `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td class="fw-semibold">${examinee.test_permit}</td>
                <td>${examinee.full_name}</td>
                <td class="font-monospace small">${examinee.external_id || '-'}</td>
                <td>${examinee.payment_method || 'Online Payment'}</td>
                <td class="fw-semibold">${examinee.amount_formatted}</td>
                <td class="text-muted" style="font-size: 0.8rem;">${examinee.paid_at_formatted}</td>
                <td class="text-center table-actions">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light" onclick="viewPaymentDetails(${startIndex + index})" title="View Payment Details">
                            <i class='bx bx-show'></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

// Update count display
function updateCount(count) {
    const countDisplay = document.querySelector('#examinees-count');
    if (countDisplay) {
        countDisplay.textContent = count;
    }
}

// Update pagination controls
function updatePagination() {
    const totalPages = Math.ceil(allExaminees.length / itemsPerPage);
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    
    // Only show pagination if more than 10 records
    if (allExaminees.length <= 10) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'block';
    pagination.innerHTML = '';
    
    const maxPagesToShow = 3;
    
    // Determine the range of pages to show
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
    
    // Adjust the start page if the end page is close to total pages
    if (endPage - startPage + 1 < maxPagesToShow) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }
    
    // Previous button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;">
                <i class="bx bx-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        pagination.innerHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i}); return false;">
                    ${i}
                </a>
            </li>
        `;
    }
    
    // Next button
    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;">
                <i class="bx bx-chevron-right"></i>
            </a>
        </li>
    `;
}

// Go to page function (global)
window.goToPage = function(page) {
    const totalPages = Math.ceil(allExaminees.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    displayCurrentPage();
    updatePagination();
    
    // Scroll to table (minimal scrolling)
    document.getElementById('tableContainer').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
};

// Initialize Flatpickr date pickers
function initializeDatePickers() {
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    
    if (dateFromInput) {
        flatpickr(dateFromInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                applyFilters();
            }
        });
    }
    
    if (dateToInput) {
        flatpickr(dateToInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                applyFilters();
            }
        });
    }
}

// Apply filters function (can be called from multiple places)
function applyFilters() {
    const regionFilter = document.querySelector('#region-filter');
    const searchInput = document.querySelector('#search-input');
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    
    const filters = {};
    
    if (regionFilter && regionFilter.value) {
        filters.region = regionFilter.value;
    }
    
    if (searchInput && searchInput.value.trim()) {
        filters.search = searchInput.value.trim();
    }
    
    if (dateFromInput && dateFromInput.value) {
        filters.dateFrom = dateFromInput.value;
    }
    
    if (dateToInput && dateToInput.value) {
        filters.dateTo = dateToInput.value;
    }
    
    loadPaidExaminees(filters);
}

// Setup filter event listeners
function setupFilters() {
    const regionFilter = document.querySelector('#region-filter');
    const searchInput = document.querySelector('#search-input');
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    const applyFilterBtn = document.querySelector('#apply-filter');
    const resetFilterBtn = document.querySelector('#reset-filter');
    
    // Auto-apply region filter when changed
    if (regionFilter) {
        regionFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    // Apply filters button
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    // Reset filters button
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            if (regionFilter) regionFilter.value = '';
            if (searchInput) searchInput.value = '';
            if (dateFromInput) {
                dateFromInput.value = '';
                // Clear flatpickr instance
                if (dateFromInput._flatpickr) {
                    dateFromInput._flatpickr.clear();
                }
            }
            if (dateToInput) {
                dateToInput.value = '';
                // Clear flatpickr instance
                if (dateToInput._flatpickr) {
                    dateToInput._flatpickr.clear();
                }
            }
            
            loadPaidExaminees();
        });
    }
    
    // Search on Enter key
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }
}

// Export to CSV functionality
function exportToCSV() {
    // Export all examinees data (not just current page)
    if (allExaminees.length === 0) {
        alert('No data to export');
        return;
    }
    
    downloadCSV(allExaminees);
}

function downloadCSV(data) {
    // Prepare CSV headers
    const headers = ['Test Permit', 'Full Name', 'Email', 'Venue', 'Region', 'Scheduled Date', 'Transaction ID', 'Payment Method', 'Amount', 'Payment Date'];
    
    // Prepare CSV rows
    const rows = data.map(item => [
        item.test_permit,
        item.full_name,
        item.email,
        item.venue_name,
        item.region,
        item.scheduled_date_formatted,
        item.external_id || '',
        item.payment_method || 'Online Payment',
        item.amount_formatted,
        item.paid_at_formatted
    ]);
    
    // Combine headers and rows
    let csvContent = headers.join(',') + '\n';
    rows.forEach(row => {
        csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `paid_examinees_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// View payment details in modal
window.viewPaymentDetails = function(index) {
    const examinee = allExaminees[index];
    if (!examinee) return;
    
    // Populate modal fields
    document.getElementById('modal-test-permit').textContent = examinee.test_permit || '-';
    document.getElementById('modal-full-name').textContent = examinee.full_name || '-';
    document.getElementById('modal-email').textContent = examinee.email || '-';
    document.getElementById('modal-venue').textContent = examinee.venue_name || '-';
    document.getElementById('modal-region').textContent = examinee.region || '-';
    document.getElementById('modal-scheduled-date').textContent = examinee.scheduled_date_formatted || '-';
    document.getElementById('modal-external-id').textContent = examinee.external_id || '-';
    document.getElementById('modal-amount').textContent = examinee.amount_formatted || '-';
    document.getElementById('modal-payment-date').textContent = examinee.paid_at_formatted || '-';
    document.getElementById('modal-payment-method').textContent = examinee.xendit_invoice_id || '-';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
    modal.show();
};
