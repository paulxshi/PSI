// Activity Log Management
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const activityTypeFilter = document.getElementById('activityTypeFilter');
    const roleFilter = document.getElementById('roleFilter');
    const severityFilter = document.getElementById('severityFilter');
    const dateFilter = document.getElementById('dateFilter');
    const refreshBtn = document.getElementById('refreshBtn');
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const tableContainer = document.getElementById('tableContainer');
    const tableBody = document.getElementById('tableBody');
    const paginationContainer = document.getElementById('paginationContainer');
    const pagination = document.getElementById('pagination');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const statusAlert = document.getElementById('statusAlert');

    let currentPage = 1;
    let currentSearch = '';
    let currentActivityType = '';
    let currentRole = '';
    let currentSeverity = '';
    let currentDateFilter = 'week';
    let searchTimeout = null;

    // Initialize - Load data immediately
    loadActivityData();

    // Refresh button
    refreshBtn.addEventListener('click', function() {
        currentPage = 1;
        loadActivityData();
    });

    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadActivityData();
        }, 500);
    });

    // Activity type filter change
    activityTypeFilter.addEventListener('change', function() {
        currentActivityType = this.value;
        currentPage = 1;
        loadActivityData();
    });

    // Role filter change
    roleFilter.addEventListener('change', function() {
        currentRole = this.value;
        currentPage = 1;
        loadActivityData();
    });

    // Severity filter change
    severityFilter.addEventListener('change', function() {
        currentSeverity = this.value;
        currentPage = 1;
        loadActivityData();
    });

    // Date filter change
    dateFilter.addEventListener('change', function() {
        currentDateFilter = this.value;
        currentPage = 1;
        loadActivityData();
    });

    // Export CSV button
    exportCsvBtn.addEventListener('click', function() {
        exportData('csv');
    });

    // Export PDF button
    exportPdfBtn.addEventListener('click', function() {
        exportData('pdf');
    });

    // Load activity log data
    function loadActivityData() {
        showLoading(true);

        const params = new URLSearchParams();
        params.append('search', currentSearch);
        params.append('page', currentPage);
        params.append('limit', 10);
        params.append('date_filter', currentDateFilter);

        
        if (currentActivityType) {
            params.append('activity_type', currentActivityType);
        }
        if (currentRole) {
            params.append('role', currentRole);
        }
        if (currentSeverity) {
            params.append('severity', currentSeverity);
        }

        fetch(`php/get_activity_logs.php?${params.toString()}`, {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Get as text first to check for errors
            })
            .then(text => {
                // Try to parse as JSON
                try {
                    const data = JSON.parse(text);
                    console.log('Activity Log Data:', data);
                    
                    if (data.success) {
                        // Update stats
                        document.getElementById('todayTotal').textContent = data.summary.today_total;
                        document.getElementById('successLogins').textContent = data.summary.success_logins;
                        document.getElementById('failedLogins').textContent = data.summary.failed_logins;
                        document.getElementById('lockouts').textContent = data.summary.lockouts;

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
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Response text:', text);
                    showStatus('Error: Invalid server response. Check console for details.', 'danger');
                    tableContainer.style.display = 'none';
                    paginationContainer.style.display = 'none';
                    showLoading(false);
                }
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
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No activity logs found</td></tr>';
            return;
        }

        records.forEach(record => {
            const timestamp = new Date(record.created_at).toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });

            const activityText = getActivityText(record.activity_type);
            const roleText = getRoleText(record.role);
            
            // Display full name (First Middle Last) if available, otherwise fallback to username or email
            let userDisplay = 'Unknown';
            if (record.first_name && record.last_name) {
                const middleName = record.middle_name ? ` ${record.middle_name} ` : ' ';
                userDisplay = `${record.first_name}${middleName}${record.last_name}`;
            } else if (record.username) {
                userDisplay = record.username;
            } else if (record.email) {
                userDisplay = record.email;
            }

            const row = `
                <tr class="border-bottom">
                    <td class="fw-semibold">${escapeHtml(timestamp)}</td>
                    <td>${escapeHtml(userDisplay)}</td>
                    <td>${roleText}</td>
                    <td>${activityText}</td>
                    <td>
                        ${record.description ? escapeHtml(record.description) : '<span class="text-muted">No description</span>'}
                    </td>
                </tr>
            `;
            
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    // Get activity text (plain text without badges)
    function getActivityText(activityType) {
        const labels = {
            'login_success': 'Login Success',
            'login_failed': 'Login Failed',
            'logout': 'Logout',
            'password_change': 'Password Change',
            'password_reset': 'Password Reset',
            'account_lockout': 'Account Lockout',
            'otp_sent': 'OTP Sent',
            'otp_verified': 'OTP Verified',
            'otp_failed': 'OTP Failed',
            'registration_completed': 'Registration',
            'payment_created': 'Payment Created',
            'payment_completed': 'Payment Success',
            'payment_failed': 'Payment Failed',
            'schedule_changed': 'Schedule Changed',
            'admin_schedule_created': 'Schedule Created',
            'admin_schedule_edited': 'Schedule Edited',
            'admin_schedule_deleted': 'Schedule Deleted',
            'admin_examinee_updated': 'Examinee Updated'
        };
        
        return escapeHtml(labels[activityType] || activityType);
    }

    // Get role text (plain text without badges)
    function getRoleText(role) {
        const labels = {
            'admin': 'Admin',
            'examinee': 'Examinee',
            'system': 'System'
        };
        return escapeHtml(labels[role] || 'Unknown');
    }

    // Get severity badge HTML
    function getSeverityBadge(severity) {
        const badges = {
            'info': '<span class="badge bg-info badge-activity">Info</span>',
            'warning': '<span class="badge bg-warning badge-activity">Warning</span>',
            'error': '<span class="badge bg-danger badge-activity">Error</span>',
            'critical': '<span class="badge bg-danger badge-activity text-white">Critical</span>'
        };
        return badges[severity] || '<span class="badge bg-secondary badge-activity">Unknown</span>';
    }

    // Export data function
    function exportData(format) {
        const params = new URLSearchParams();
        params.append('search', currentSearch);
        params.append('date_filter', currentDateFilter);
        params.append('format', format);
        if (currentActivityType) {
            params.append('activity_type', currentActivityType);
        }
        if (currentRole) {
            params.append('role', currentRole);
        }
        if (currentSeverity) {
            params.append('severity', currentSeverity);
        }

        // Open in new window to trigger download
        window.open(`php/export_activity_logs.php?${params.toString()}`, '_blank');
    }

    // Populate pagination
    function populatePagination(paginationData) {
        pagination.innerHTML = '';

        const { current_page, total_pages } = paginationData;

        if (total_pages <= 1) return;

        // Previous button
            const prevDisabled = current_page === 1 ? 'disabled' : '';
            pagination.insertAdjacentHTML('beforeend', `
                <li class="page-item ${prevDisabled}">
                    <a class="page-link" href="#" data-page="${current_page - 1}" aria-label="Previous">&lt;</a>
                </li>
            `);

        // Page numbers
        let startPage = Math.max(1, current_page - 2);
        let endPage = Math.min(total_pages, current_page + 2);

        if (startPage > 1) {
            pagination.insertAdjacentHTML('beforeend', `
                <li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>
            `);
            if (startPage > 2) {
                pagination.insertAdjacentHTML('beforeend', `<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const active = i === current_page ? 'active' : '';
            pagination.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        if (endPage < total_pages) {
            if (endPage < total_pages - 1) {
                pagination.insertAdjacentHTML('beforeend', `<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
            pagination.insertAdjacentHTML('beforeend', `
                <li class="page-item"><a class="page-link" href="#" data-page="${total_pages}">${total_pages}</a></li>
            `);
        }

        // Next button
            const nextDisabled = current_page === total_pages ? 'disabled' : '';
            pagination.insertAdjacentHTML('beforeend', `
                <li class="page-item ${nextDisabled}">
                    <a class="page-link" href="#" data-page="${current_page + 1}" aria-label="Next">&gt;</a>
                </li>
            `);

        // Add click handlers
        pagination.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadActivityData();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
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
        
        setTimeout(() => {
            statusAlert.style.display = 'none';
        }, 5000);
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
});
