let scanLocked = false;



function onScanSuccess(decodedText) {

    if (scanLocked) return;
    scanLocked = true;

    console.log("Scanned:", decodedText);

    fetch("php/verify_qr.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ external_id: decodedText })
    })
    .then(res => res.json())
    .then(data => {
        showQRResult(data);
    })
    .catch(err => {
        console.error(err);
        scanLocked = false;
    });
}


document.getElementById('qrResultModal')
    .addEventListener('hidden.bs.modal', function () {
        scanLocked = false;
        window.currentExamineeId = null;
    });


function applyActionState(state) {
    const completeBtn = document.getElementById("completeBtn");
    const rejectBtn = document.getElementById("rejectBtn");

    // Reset styles
    completeBtn.disabled = false;
    rejectBtn.disabled = false;

    completeBtn.classList.remove("btn-success", "btn-secondary");
    rejectBtn.classList.remove("btn-danger", "btn-secondary");

    if (state === "completed") {
        completeBtn.textContent = "Completed";
        rejectBtn.textContent = "Reject";

        completeBtn.disabled = true;
        rejectBtn.disabled = true;

        completeBtn.classList.add("btn-secondary");
        rejectBtn.classList.add("btn-secondary");
    }
    else if (state === "rejected") {
        completeBtn.textContent = "Complete";
        rejectBtn.textContent = "Rejected";

        completeBtn.disabled = true;
        rejectBtn.disabled = true;

        completeBtn.classList.add("btn-secondary");
        rejectBtn.classList.add("btn-secondary");
    }
    else {
        // Default usable state
        completeBtn.textContent = "Mark as Complete";
        rejectBtn.textContent = "Reject Entry";

        completeBtn.classList.add("btn-success");
        rejectBtn.classList.add("btn-danger");
    }
}


function setText(id, value) {
    const el = document.getElementById(id);
    if (!el) {
        console.warn("Element not found:", id);
        return;
    }
    el.textContent = value ?? "-";
}

function showQRResult(data) {

    // Only allow action if valid
    window.currentExamineeId = (data.status_class === "valid") ? data.examinee_id : null;

    // Fill fields
setText("name", data.name);
setText("test_permit", data.test_permit);
setText("examination_date", data.exam_date);
setText("examination_venue", data.venue);
setText("invoice_no", data.invoice_no);
setText("status", data.payment_status);
setText("payment_date", data.payment_date);
setText("payment_amount", data.amount);


    // Change status text
    const statusBox = document.getElementById("verificationStatus");
    statusBox.className = "status text-center mb-4 " + data.status_class;
    statusBox.textContent = data.status_message;

    // Change modal color
    const modalElement = document.getElementById("qrResultModal");
    modalElement.classList.remove("modal-valid", "modal-invalid", "modal-warning", "modal-already_used");
    modalElement.classList.add("modal-" + data.status_class);

    // Apply correct button state based on backend status
    if (data.status_class === "already_used") {
        applyActionState("completed");
    }
    else if (data.status_class === "rejected") {
        applyActionState("rejected");
    }
    else if (data.status_class === "valid") {
        applyActionState("default");
    }
    else {
        applyActionState("completed"); // lock if invalid
    }

    // Show modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

document.addEventListener("DOMContentLoaded", function () {

    const completeBtn = document.getElementById("completeBtn");
    const rejectBtn = document.getElementById("rejectBtn");

    if (completeBtn) {
        completeBtn.addEventListener("click", function () {

            if (!window.currentExamineeId) {
                alert("No examinee selected.");
                return;
            }

            applyActionState("completed");
            updateStatus("complete");
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener("click", function () {

            if (!window.currentExamineeId) {
                alert("No examinee selected.");
                return;
            }

            applyActionState("rejected");
            updateStatus("reject");
        });
    }

});



function updateStatus(action) {

    fetch("php/update_examinee_status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            examinee_id: window.currentExamineeId,
            action: action
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert("Error: " + data.message);

            // Re-enable if server failed
            applyActionState("default");
        }

    })
    .catch(err => {
        console.error(err);
        applyActionState("default");
    });
}
