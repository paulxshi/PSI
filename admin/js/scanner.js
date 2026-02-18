
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


function showQRResult(data) {

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

    // 🔴🟢 Change modal color
    const modalElement = document.getElementById("qrResultModal");

    // Remove previous state
    modalElement.classList.remove("modal-valid", "modal-invalid", "modal-warning");

    // Add new state
    modalElement.classList.add("modal-" + data.status_class);

    // Show modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
