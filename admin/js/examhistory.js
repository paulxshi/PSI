document.addEventListener("DOMContentLoaded", function () {
    fetch("php/get_schedules_history.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("examTableBody");
            tableBody.innerHTML = "";

            // Check if data is an error object
            if (data.error) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            Error loading data: ${data.error}
                        </td>
                    </tr>
                `;
                return;
            }

            // Check if data is an array
            if (!Array.isArray(data)) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            Invalid data format received
                        </td>
                    </tr>
                `;
                return;
            }

            // Check if data is empty
            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No exam history found
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach((row, index) => {

                let dateObj = new Date(row.scheduled_date);
                let formattedDate = dateObj.toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });

                let dayName = dateObj.toLocaleDateString("en-US", {
                    weekday: "long"
                });

                let regionClass = row.region.toLowerCase();

                let tr = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <span class="region-pill ${regionClass}">
                                ${row.region}
                            </span>
                        </td>
                        <td>${row.venue_name}</td>
                        <td>
                            <div class="fw-semibold">${formattedDate}</div>
                            <div class="text-muted small">${dayName}</div>
                        </td>
                        <td>
                            <strong>${row.num_registered}</strong> examinees
                        </td>
                       
                        <td>
                            <strong>${row.num_completed}</strong> attended
                        </td>
                    </tr>
                `;

                tableBody.innerHTML += tr;
            });
        })
        .catch(error => {
            console.error("Error:", error);
            let tableBody = document.getElementById("examTableBody");
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        Failed to load exam history. Please try again later.
                    </td>
                </tr>
            `;
        });
});
