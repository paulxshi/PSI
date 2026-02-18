
function onScanSuccess(decodedText) {

    console.log("Scanned:", decodedText);

    // Example decodedText = "TXN1771223060"
    const transactionNo = decodedText;

    fetch("php/verify_qr.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ transaction_no: transactionNo })
    })
    .then(res => res.json())
    .then(data => {
        showQRResult(data); // this opens your modal
    })
    .catch(err => console.error(err));
}



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



function showQRResult(data) {

    // Only allow action if valid
    window.currentExamineeId = (data.status_class === "valid") ? data.examinee_id : null;

    // Fill fields
    document.getElementById("name").textContent = data.name ?? "-";
    document.getElementById("test_permit").textContent = data.test_permit ?? "-";
    document.getElementById("examination_date").textContent = data.exam_date ?? "-";
    document.getElementById("examination_venue").textContent = data.venue ?? "-";
    document.getElementById("transaction_no").textContent = data.transaction_no ?? "-";
    document.getElementById("status").textContent = data.payment_status ?? "-";
    document.getElementById("payment_date").textContent = data.payment_date ?? "-";
    document.getElementById("payment_amount").textContent = data.amount ?? "-";

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


document.getElementById("completeBtn").addEventListener("click", function () {

    if (!window.currentExamineeId) {
        alert("No examinee selected.");
        return;
    }

    // 🔥 Instantly reflect change in UI
    applyActionState("completed");

    updateStatus("complete");
});


document.getElementById("rejectBtn").addEventListener("click", function () {

    if (!window.currentExamineeId) {
        alert("No examinee selected.");
        return;
    }

    // 🔥 Instantly reflect change in UI
    applyActionState("rejected");

    updateStatus("reject");
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
