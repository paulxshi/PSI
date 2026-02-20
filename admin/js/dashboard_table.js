document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("examineeTableBody");
    const completedCountEl = document.getElementById("completedCount");
    const currentDateEl = document.getElementById("currentDate");

    // Helper: Safely parse MySQL DATETIME
    function parseMySQLDateTime(dateTimeString) {
        if (!dateTimeString) return null;
        return new Date(dateTimeString.replace(' ', 'T'));
    }


    

    function timeAgo(dateTimeString) {
    if (!dateTimeString) return "—";

    const now = new Date();
    const past = new Date(dateTimeString.replace(' ', 'T')); // safe parsing
    const diffInSeconds = Math.floor((now - past) / 1000);

    if (diffInSeconds < 60) {
        return `${diffInSeconds} second${diffInSeconds !== 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 3600) { // less than 1 hour
        const mins = Math.floor(diffInSeconds / 60);
        return `${mins} minute${mins !== 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) { // less than 1 day
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days !== 1 ? 's' : ''} ago`;
    }
}
    // Fetch and render examinees
    function fetchCompletedExaminees() {
        fetch("php/get_completed_examinees.php")
            .then(response => response.json())
            .then(data => {

                // Clear table every refresh (full sync)
                tableBody.innerHTML = "";

                if (!data || data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center">No records found</td>
                        </tr>
                    `;
                    completedCountEl.textContent = 0;
                    return;
                }

                // Sort newest first (safe parsing)
// Sort newest first by scanned_at (latest scanned first)
data.sort((a, b) => {
    const timeA = a.scanned_at ? new Date(a.scanned_at.replace(' ', 'T')) : new Date(0);
    const timeB = b.scanned_at ? new Date(b.scanned_at.replace(' ', 'T')) : new Date(0);
    return timeB - timeA; // newest first
});

                let completedCount = 0;

                data.forEach(examinee => {

                    const scheduledDate = parseMySQLDateTime(examinee.scheduled_date);
                    const scheduledTime = parseMySQLDateTime(examinee.scheduled_at);

                    const formattedDate = scheduledDate
                        ? scheduledDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })
                        : "Not Scheduled";

const formattedTime = examinee.scanned_at
    ? `${new Date(examinee.scanned_at.replace(' ', 'T')).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      })} (${timeAgo(examinee.scanned_at)})`
    : "—";
                    if (examinee.examinee_status.toLowerCase() === "completed") {
                        completedCount++;
                    }

                    const row = document.createElement("tr");

                    function createCell(text) {
                        const td = document.createElement("td");
                        td.textContent = text;
                        return td;
                    }

                    row.appendChild(createCell(examinee.test_permit));
                    row.appendChild(createCell(examinee.full_name));
                    row.appendChild(createCell(examinee.email));
                    row.appendChild(createCell(formattedDate));
                    row.appendChild(createCell(formattedTime));

                    // Status cell
                    const statusTd = document.createElement("td");
                    const statusSpan = document.createElement("span");
                    statusSpan.className = "status-badge completed";
                    statusSpan.textContent = examinee.examinee_status;
                    statusTd.appendChild(statusSpan);
                    row.appendChild(statusTd);

                    tableBody.appendChild(row);
                });

                completedCountEl.textContent = completedCount;
            })
            .catch(error => console.error("Error fetching examinees:", error));
    }

    // Initial fetch
    fetchCompletedExaminees();

    // Poll every 5 seconds (5000ms)
    setInterval(fetchCompletedExaminees, 5000);

    // Display current date
    const today = new Date();
    currentDateEl.textContent = today.toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });



});