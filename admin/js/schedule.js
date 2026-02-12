
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const publishBtn = document.getElementById("publishBtn");
    const confirmBtn = document.getElementById("confirmCreateBtn");
    const modalElement = document.getElementById("confirmScheduleModal");
    const createSchedBtn = document.getElementById("createSchedBtn");
    const confirmModal = new bootstrap.Modal(modalElement);

    // Handle Create Schedule click
    publishBtn.addEventListener("click", function () {

        // Get values
        const region = document.querySelector("[name='exam_region']").value;
        const venue = document.querySelector("[name='exam_area']").value;
        const month = document.querySelector("[name='exam_month']").value;
        const day = document.querySelector("[name='exam_day']").value;
        const year = document.querySelector("[name='exam_year']").value;
        const limit = document.querySelector("[name='exam_limit']").value;

        // Validation
        if (!region || !venue || !month || !day || !year || !limit) {
            alert("Please complete all fields.");
            return;
        }

        // Format exam date
        const examDate = `${month} ${day}, ${year}`;

        // Inject preview values
        document.getElementById("previewRegion").textContent = region;
        document.getElementById("previewVenue").textContent = venue;
        document.getElementById("previewDate").textContent = examDate;
        document.getElementById("previewLimit").textContent = `${limit} Examinees`;

        document.getElementById("publishBtn").disabled = false;

        // Show confirmation modal ONLY if validation passed
        confirmModal.show();
    });

    // Submit normally when confirmed
    confirmBtn.addEventListener("click", function () {
        confirmModal.hide();
        form.requestSubmit(); // ✅ triggers normal submit + validation
    });


    

createSchedBtn.addEventListener("click", function () {

            // Get values
            const region = document.querySelector("[name='exam_region']").value;
            const venue = document.querySelector("[name='exam_area']").value;
            const month = document.querySelector("[name='exam_month']").value;
            const day = document.querySelector("[name='exam_day']").value;
            const year = document.querySelector("[name='exam_year']").value;
            const limit = document.querySelector("[name='exam_limit']").value;

            if (!region || !venue || !month || !day || !year || !limit) {
                alert("Please complete all fields.");
                return;
            }

            // Format exam date
            const examDate = `${month} ${day}, ${year}`;

            // Inject preview values
            document.getElementById("previewRegion").textContent = region;
            document.getElementById("previewVenue").textContent = venue;
            document.getElementById("previewDate").textContent = examDate;
            document.getElementById("previewLimit").textContent = `${limit} Examinees`;

            // Enable publish button
            document.getElementById("publishBtn").disabled = false;
        });

});

