document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("examineeTableBody");
    let existingIds = new Set(); // Track displayed examinees

    // Function to fetch and update table
    function fetchCompletedExaminees() {
        fetch("php/get_completed_examinees.php")
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    if (existingIds.size === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        `;
                    }
                    return;
                }

                // Sort by updated_at descending so latest appear first
                data.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

                data.forEach(examinee => {
                    if (!existingIds.has(examinee.examinee_id)) {
                        existingIds.add(examinee.examinee_id);

                        const formattedDate = examinee.scheduled_date
                            ? new Date(examinee.scheduled_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })
                            : "Not Scheduled";
                            
                        const formattedTime = examinee.scheduled_time
                        ? new Date(`1970-01-01T${examinee.scheduled_time}`).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                            })
                        : "—";

                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${examinee.test_permit}</td>
                            <td>${examinee.full_name}</td>
                            <td>${examinee.email}</td>
                            <td>${formattedDate}</td>
                            <td>${formattedTime}</td>
                            <td>
                                <span class="status-badge completed">
                                ${examinee.examinee_status}
                                </span>
                            </td>
                        `;

                        // Insert at the top
                        tableBody.prepend(row);
                    }
                });

                 updateCompletedCount();
            })
            .catch(error => console.error("Error:", error));
    }

    // Initial fetch
    fetchCompletedExaminees();

    // Poll server every 5 seconds for new completed examinees
    setInterval(fetchCompletedExaminees, 500);



    function updateCompletedCount() {
    const tableBody = document.getElementById('examineeTableBody');
    const rows = tableBody.querySelectorAll('tr');
    let completedCount = 0;

    rows.forEach(row => {
        const statusCell = row.cells[5];// Assuming Status is the 5th column (index 4)
        if (statusCell && statusCell.textContent.trim().toLowerCase() === 'completed') {
            completedCount++;
        }
    });

    document.getElementById('completedCount').textContent = completedCount;
}




const currentDateEl = document.getElementById('currentDate');
const today = new Date();
const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

// Format the date
currentDateEl.textContent = today.toLocaleDateString(undefined, options);

});
