document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("examTableBody");
    const pagination = document.getElementById("pagination");
    const paginationInfo = document.getElementById("paginationInfo");

    const ROWS_PER_PAGE = 8;
    let currentPage = 1;
    let examData = [];

    fetch("php/get_schedules_history.php")
        .then(res => res.json())
        .then(data => {

            if (data?.error) {
                tableBody.innerHTML = errorRow(data.error);
                return;
            }

            if (!Array.isArray(data) || data.length === 0) {
                tableBody.innerHTML = emptyRow("No exam history found");
                return;
            }

            examData = data;
            renderTable();
            renderPagination();
        })
        .catch(() => {
            tableBody.innerHTML = errorRow(
                "Failed to load exam history. Please try again later."
            );
        });

    /* ---------- Render Table ---------- */
    function renderTable() {
        tableBody.innerHTML = "";

        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = examData.slice(start, end);

        pageData.forEach((row, index) => {
            const dateObj = new Date(row.scheduled_date);
            const formattedDate = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });
            const dayName = dateObj.toLocaleDateString("en-US", {
                weekday: "long"
            });

            tableBody.innerHTML += `
                <tr>
                    <td>${start + index + 1}</td>
                    <td>
                        <span class="region-pill ${mapRegionClass(row.region)}">
                            ${row.region}
                        </span>
                    </td>
                    <td>${row.venue_name ?? "—"}</td>
                    <td>
                        <div class="fw-semibold">${formattedDate}</div>
                        <div class="text-muted small">${dayName}</div>
                    </td>
                    <td><strong>${row.num_registered ?? 0}</strong> examinees</td>
                    <td><strong>${row.num_completed ?? 0}</strong> attended</td>
                    <td class="text-end">
                        <button 
                            class="btn btn-sm btn-outline-success rounded-pill px-3" 
                            onclick="exportExamHistory(${row.schedule_id})"
                            title=" CSV"
                        >
                            <i class="bx bx-download"></i> CSV
                        </button>
                    </td>
                </tr>
            `;
        });

        const total = examData.length;
        paginationInfo.textContent = 
            `Showing ${start + 1}–${Math.min(end, total)} of ${total} records`;
    }

    /* ---------- Pagination ---------- */
/* ---------- Pagination ---------- */
function renderPagination() {
    pagination.innerHTML = "";
    const totalPages = Math.ceil(examData.length / ROWS_PER_PAGE);

    pagination.appendChild(pageItem("‹", currentPage === 1, () => {
        currentPage--;
        update();
    }));

    // Limit the number of page numbers to 3
    const startPage = Math.max(1, currentPage - 1);
    const endPage = Math.min(totalPages, currentPage + 1);

    // Add page numbers
    for (let i = startPage; i <= endPage; i++) {
        pagination.appendChild(pageItem(i, false, () => {
            currentPage = i;
            update();
        }, i === currentPage));
    }

    pagination.appendChild(pageItem("›", currentPage === totalPages, () => {
        currentPage++;
        update();
    }));
}

    function update() {
        renderTable();
        renderPagination();
    }
});

/* ---------- Helpers ---------- */

function pageItem(label, disabled, onClick, active = false) {
    const li = document.createElement("li");
    li.className = `page-item ${disabled ? "disabled" : ""} ${active ? "active" : ""}`;

    const a = document.createElement("a");
    a.className = "page-link";
    a.href = "#";
    a.innerHTML = label;
    a.setAttribute(
    "aria-label",
    label === "‹" ? "Previous" :
    label === "›" ? "Next" :
    `Page ${label}`
    );

    a.addEventListener("click", e => {
        e.preventDefault();
        if (!disabled) onClick();
    });

    li.appendChild(a);
    return li;
}

function mapRegionClass(region = "") {
    const r = region.toLowerCase();
    if (r.includes("luzon")) return "luzon";
    if (r.includes("visayas")) return "visayas";
    if (r.includes("mindanao")) return "mindanao";
    return "default";
}

function errorRow(message) {
    return `
        <tr>
            <td colspan="7" class="text-center text-danger py-4">${message}</td>
        </tr>
    `;
}

function emptyRow(message) {
    return `
        <tr>
            <td colspan="7" class="text-center text-muted py-4">${message}</td>
        </tr>
    `;
}

/* ---------- Export Function ---------- */
function exportExamHistory(scheduleId) {
    if (!scheduleId) {
        alert('Invalid schedule ID');
        return;
    }

    // Create and trigger download
    const exportUrl = `php/export_exam_history.php?schedule_id=${scheduleId}`;
    window.location.href = exportUrl;
}