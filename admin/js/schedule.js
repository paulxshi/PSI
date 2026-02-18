document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("scheduleForm");
    const publishBtn = document.getElementById("publishBtn");
    const modalElement = document.getElementById("confirmScheduleModal");
    const confirmBtn = document.getElementById("confirmCreateBtn");
    const confirmModal = new bootstrap.Modal(modalElement);

    // Handle Publish Schedule click
    publishBtn.addEventListener("click", function (e) {
        e.preventDefault();

        // Get values
        const region = document.querySelector("[name='exam_region']").value;
        const venue = document.querySelector("[name='exam_area']").value;
        const date = document.querySelector("[name='exam_date']").value;
        const time = document.querySelector("[name='exam_time']").value;
        const limit = document.querySelector("[name='exam_limit']").value;
        const price = document.querySelector("[name='exam_price']").value;

        // Validation
        if (!region || !venue || !date || !time || !limit || !price) {
            alert("Please complete all fields.");
            return;
        }

        // Show confirmation modal
        confirmModal.show();
    });

    // Submit form via AJAX when confirmed
    confirmBtn.addEventListener("click", function () {
        confirmModal.hide();
        
        // Show loading state
        publishBtn.disabled = true;
        publishBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        const formData = new FormData(form);

        fetch('php/save_schedule.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Schedule Creation Response:', data);
            
            if (data.success) {
                // Show success message
                alert(data.message || 'Schedule successfully created!');
                
                // Redirect to dashboard
                window.location.href = 'dashboard.html';
            } else {
                // Show error message
                alert(data.message || 'Failed to create schedule. Please try again.');
                
                // Reset button
                publishBtn.disabled = false;
                publishBtn.textContent = 'Publish Schedule';
            }
        })
        .catch(error => {
            console.error('Error creating schedule:', error);
            alert('Network error. Please try again.');
            
            // Reset button
            publishBtn.disabled = false;
            publishBtn.textContent = 'Publish Schedule';
        });
    });

});

