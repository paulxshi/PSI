// Utility: escape HTML to prevent XSS
function escapeHtml(str) {
    if (str == null) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    loadPaymentStatistics();
    loadPaidExaminees();
    loadVenues(); 
    
    setupFilters();
    setupDatePresets();
    
    initializeDatePickers();
});

let currentPage = 1;
let totalPages = 1;
let totalCount = 0;
let currentPageData = [];
const itemsPerPage = 10;

function loadPaymentStatistics(filters = {}) {
    const queryParams = new URLSearchParams(filters).toString();
    const url = `php/get_payment_statistics.php${queryParams ? '?' + queryParams : ''}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRegionCards(data.regions, data.overall);
            } else {
                showToast(data.message || 'Error loading statistics', 'danger');
            }
        })
        .catch(error => {
            console.error('Error fetching statistics:', error);
            showToast('Failed to load statistics.', 'danger');
        });
}

function updateRegionCards(regions, overall) {
    const regionMap = {
        Luzon: { revenue: '.luzon-revenue', sub: '.luzon-sub' },
        Visayas: { revenue: '.visayas-revenue', sub: '.visayas-sub' },
        Mindanao: { revenue: '.mindanao-revenue', sub: '.mindanao-sub' }
    };
    
    for (const [name, selectors] of Object.entries(regionMap)) {
        const revenueEl = document.querySelector(selectors.revenue);
        const subEl = document.querySelector(selectors.sub);
        if (revenueEl) revenueEl.textContent = '₱' + (regions[name]?.revenue || '0.00');
        if (subEl) subEl.innerHTML = `${regions[name]?.examinees || 0} paid examinees &nbsp;•&nbsp; ${regions[name]?.payments || 0} payments`;
    }
    
    // Update total revenue card
    const totalRevenueEl = document.querySelector('.total-revenue');
    const totalSubEl = document.querySelector('.total-sub');
    if (totalRevenueEl && overall) {
        totalRevenueEl.textContent = '₱' + (overall.total_revenue || '0.00');
    }
    if (totalSubEl && overall) {
        totalSubEl.innerHTML = `${overall.total_examinees || 0} paid examinees &nbsp;•&nbsp; ${overall.total_payments || 0} payments`;
    }
}

// Load paid examinees list
function loadPaidExaminees(filters = {}, page = 1) {
    filters.page = page;
    filters.limit = itemsPerPage;
    const queryParams = new URLSearchParams(filters).toString();
    const url = `php/get_paid_examinees.php${queryParams ? '?' + queryParams : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentPage = data.page;
                totalPages = data.totalPages;
                totalCount = data.count;
                currentPageData = data.data;
                const startIndex = (currentPage - 1) * itemsPerPage;
                displayPaidExaminees(data.data, startIndex);
                updateCount(data.count);
                updatePagination();
            } else {
                showToast(data.message || 'Error loading examinees', 'danger');
            }
        })
        .catch(error => {
            console.error('Error fetching examinees:', error);
            showToast('Failed to load examinees. Please try again.', 'danger');
        });
}

// Display paid examinees in a table
function displayPaidExaminees(examinees, startIndex = 0) {
    const tableBody = document.querySelector('#paid-examinees-table tbody');
    const tableContainer = document.querySelector('#tableContainer');
    
    if (!tableBody) return;
    
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
                <td class="fw-semibold">${escapeHtml(examinee.test_permit)}</td>
                <td>${escapeHtml(examinee.full_name)}</td>
                <td class="font-monospace small">${escapeHtml(examinee.external_id || '-')}</td>
                <td>${escapeHtml(examinee.payment_method || 'Online Payment')}</td>
                <td class="fw-semibold">${escapeHtml(examinee.amount_formatted)}</td>
                <td class="text-muted" style="font-size: 0.8rem;">${escapeHtml(examinee.paid_at_formatted)}</td>
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

function updateCount(count) {
    const countDisplay = document.querySelector('#examinees-count');
    if (countDisplay) {
        countDisplay.textContent = count;
    }
}

// Update pagination controls
function updatePagination() {
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    
    // Only show pagination if more than 1 page
    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'block';
    pagination.innerHTML = '';
    
    const maxPagesToShow = 3;
    
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
    
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
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    applyFilters(page);
    
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

function applyFilters(page = 1) {
    const regionFilter = document.querySelector('#region-filter');
    const venueFilter = document.querySelector('#venue-filter');
    const searchInput = document.querySelector('#search-input');
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    
    const filters = {};
    
    if (regionFilter && regionFilter.value) {
        filters.region = regionFilter.value;
    }
    
    if (venueFilter && venueFilter.value) {
        filters.venue = venueFilter.value;
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
    
    loadPaidExaminees(filters, page);
    loadPaymentStatistics(filters);
}

function setupFilters() {
    const regionFilter = document.querySelector('#region-filter');
    const venueFilter = document.querySelector('#venue-filter');
    const searchInput = document.querySelector('#search-input');
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    const applyFilterBtn = document.querySelector('#apply-filter');
    const resetFilterBtn = document.querySelector('#reset-filter');
    
    if (regionFilter) {
        regionFilter.addEventListener('change', function() {
            filterVenuesByRegion(regionFilter.value);
            applyFilters();
        });
    }
    
    // Auto-apply venue filter when changed
    if (venueFilter) {
        venueFilter.addEventListener('change', function() {
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
            if (venueFilter) {
                venueFilter.value = '';
                loadVenues(); // Reload all venues
            }
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
            loadPaymentStatistics();
        });
    }
    
    // Debounced live search
    let searchDebounceTimer = null;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => applyFilters(), 350);
        });
    }
    
    // Search on Enter key
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchDebounceTimer);
                applyFilters();
            }
        });
    }
}

// Export to CSV functionality
function exportToCSV() {
    if (totalCount === 0) {
        showToast('No data to export', 'warning');
        return;
    }
    
    // Build current filter params + exportAll flag
    const filters = getCurrentFilters();
    filters.exportAll = '1';
    const queryParams = new URLSearchParams(filters).toString();
    const url = `php/get_paid_examinees.php?${queryParams}`;
    
    showToast('Preparing export...', 'info');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                downloadCSV(data.data);
                showToast('Export complete!', 'success');
            } else {
                showToast('Export failed.', 'danger');
            }
        })
        .catch(error => {
            console.error('Export error:', error);
            showToast('Export failed. Please try again.', 'danger');
        });
}

// Get current filter values as an object
function getCurrentFilters() {
    const filters = {};
    const regionFilter = document.querySelector('#region-filter');
    const venueFilter = document.querySelector('#venue-filter');
    const searchInput = document.querySelector('#search-input');
    const dateFromInput = document.querySelector('#date-from');
    const dateToInput = document.querySelector('#date-to');
    
    if (regionFilter && regionFilter.value) filters.region = regionFilter.value;
    if (venueFilter && venueFilter.value) filters.venue = venueFilter.value;
    if (searchInput && searchInput.value.trim()) filters.search = searchInput.value.trim();
    if (dateFromInput && dateFromInput.value) filters.dateFrom = dateFromInput.value;
    if (dateToInput && dateToInput.value) filters.dateTo = dateToInput.value;
    
    return filters;
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

// Load venues for filter dropdown
let allVenues = [];

function loadVenues() {
    fetch('php/get_venues.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allVenues = data.venues;
                populateVenueDropdown(allVenues);
            } else {
                showToast(data.message || 'Error loading venues', 'danger');
            }
        })
        .catch(error => {
            console.error('Error loading venues:', error);
            showToast('Failed to load venues.', 'danger');
        });
}

function populateVenueDropdown(venues) {
    const venueFilter = document.querySelector('#venue-filter');
    if (!venueFilter) return;
    
    // Keep "All Venues" option and add venues
    venueFilter.innerHTML = '<option value="">All Venues</option>';
    
    venues.forEach(venue => {
        const option = document.createElement('option');
        option.value = venue.venue_name;
        option.textContent = `${venue.venue_name} (${venue.region})`;
        venueFilter.appendChild(option);
    });
}

function filterVenuesByRegion(region) {
    if (!region) {
        populateVenueDropdown(allVenues);
        return;
    }
    
    const filteredVenues = allVenues.filter(v => v.region === region);
    populateVenueDropdown(filteredVenues);
    
    // Reset venue selection when region changes
    const venueFilter = document.querySelector('#venue-filter');
    if (venueFilter) {
        venueFilter.value = '';
    }
}

// View payment details in modal
window.viewPaymentDetails = function(localIndex) {
    const examinee = currentPageData[localIndex - ((currentPage - 1) * itemsPerPage)];
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

// ── Toast Notification System ──
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const iconMap = {
        success: 'bx-check-circle',
        danger: 'bx-error-circle',
        warning: 'bx-error',
        info: 'bx-info-circle'
    };
    
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class='bx ${iconMap[type] || iconMap.info}'></i>
                ${escapeHtml(message)}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    container.appendChild(toastEl);
    
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// ── Date Range Presets ──
function setupDatePresets() {
    document.querySelectorAll('.date-preset').forEach(btn => {
        btn.addEventListener('click', function() {
            const preset = this.dataset.preset;
            const today = new Date();
            let from, to;
            
            to = formatDate(today);
            
            switch (preset) {
                case 'today':
                    from = to;
                    break;
                case 'week': {
                    const day = today.getDay();
                    const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    from = formatDate(new Date(today.getFullYear(), today.getMonth(), diff));
                    break;
                }
                case 'month':
                    from = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                    break;
                case 'quarter': {
                    const qMonth = Math.floor(today.getMonth() / 3) * 3;
                    from = formatDate(new Date(today.getFullYear(), qMonth, 1));
                    break;
                }
            }
            
            const dateFromInput = document.querySelector('#date-from');
            const dateToInput = document.querySelector('#date-to');
            
            if (dateFromInput) {
                if (dateFromInput._flatpickr) dateFromInput._flatpickr.setDate(from, true);
                else dateFromInput.value = from;
            }
            if (dateToInput) {
                if (dateToInput._flatpickr) dateToInput._flatpickr.setDate(to, true);
                else dateToInput.value = to;
            }
            
            // Highlight active preset
            document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('btn-dark', 'text-white'));
            this.classList.add('btn-dark', 'text-white');
            
            applyFilters();
        });
    });
}

function formatDate(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

// ── PDF Export ──
function exportToPDF() {
    if (totalCount === 0) {
        showToast('No data to export', 'warning');
        return;
    }
    
    const filters = getCurrentFilters();
    filters.exportAll = '1';
    const queryParams = new URLSearchParams(filters).toString();
    const url = `php/get_paid_examinees.php?${queryParams}`;
    
    showToast('Generating PDF...', 'info');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                generatePDF(data.data);
                showToast('PDF export complete!', 'success');
            } else {
                showToast('PDF export failed.', 'danger');
            }
        })
        .catch(error => {
            console.error('PDF export error:', error);
            showToast('PDF export failed. Please try again.', 'danger');
        });
}

function generatePDF(data) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'mm', 'a4');
    
    // Title
    doc.setFontSize(16);
    doc.setFont(undefined, 'bold');
    doc.text('PSI - Paid Examinees Report', 14, 18);
    
    doc.setFontSize(9);
    doc.setFont(undefined, 'normal');
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 24);
    doc.text(`Total Records: ${data.length}`, 14, 29);
    
    // Table
    const headers = [['#', 'Test Permit', 'Full Name', 'Venue', 'Region', 'Transaction ID', 'Method', 'Amount', 'Payment Date']];
    const rows = data.map((item, i) => [
        i + 1,
        item.test_permit || '',
        item.full_name || '',
        item.venue_name || '',
        item.region || '',
        item.external_id || '-',
        item.payment_method || 'Online Payment',
        item.amount_formatted || '',
        item.paid_at_formatted || ''
    ]);
    
    doc.autoTable({
        head: headers,
        body: rows,
        startY: 34,
        styles: { fontSize: 7, cellPadding: 2 },
        headStyles: { fillColor: [30, 41, 59], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 249, 250] },
        margin: { left: 14, right: 14 }
    });
    
    doc.save(`paid_examinees_${new Date().toISOString().split('T')[0]}.pdf`);
}
