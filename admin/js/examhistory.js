document.addEventListener("DOMContentLoaded", function () {
    fetch("php/get_schedules_history.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("examTableBody");
            tableBody.innerHTML = "";

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
        .catch(error => console.error("Error:", error));
});
