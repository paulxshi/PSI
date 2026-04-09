// Professional scan state management
let scanLocked = false;
let autoCloseTimeout = null;

function clearAllTimers() {
    if (autoCloseTimeout) {
        clearTimeout(autoCloseTimeout);
        autoCloseTimeout = null;
    }
}

function unlockScan() {
    scanLocked = false;
}

function hideModal() {
    clearAllTimers();
    const modalElement = document.getElementById("qrResultModal");
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) modalInstance.hide();
    unlockScan();
}

function showError(message) {
    // Professional error feedback
    const manualQR = document.getElementById("manualQR");
    if (manualQR) manualQR.value = "";
    alert(message);
    unlockScan();
}


function isAnyModalOpen() {
    return document.querySelectorAll(".modal.show").length > 0;
}


function onScanSuccess(decodedText) {
    // Prevent scanning if modal is open or scan is locked
    if (scanLocked || isAnyModalOpen()) {
        // Optionally clear input if scan attempted while modal is open
        const manualQR = document.getElementById("manualQR");
        if (manualQR) manualQR.value = "";
        return;
    }
    scanLocked = true;

    // Validate schedule selection
    if (!window.selectedRegion || !window.selectedVenue || !window.selectedDate || !window.selectedScheduleId) {
        alert("Please select a schedule before scanning.");
            const manualQR = document.getElementById("manualQR");
            if (manualQR) manualQR.value = "";
        return;
    }

    // Validate QR value (allow tab and full name)
    if (!decodedText || typeof decodedText !== "string" || decodedText.trim() === "") {
        showError("Invalid or empty QR value scanned.");
        return;
    }

// Normalize QR value (keep tab for Excel but handle safely)
let qrValue = decodedText.replace(/\r?\n/g, "").trim();

// If scanner sends actual TAB key, make sure it stays in the string
qrValue = qrValue.replace(/\s{2,}/g, "\t");

    // Accept at least 8 chars for transactionNo, and optionally tab/full name
    if (qrValue.length < 8) {
        showError("Invalid or incomplete QR value.");
        return;
    }

    // Send the full QR value (with tab/full name if present) to the backend
    fetch("php/verify_qr.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ external_id: qrValue })
    })
    .then(res => res.json())
    .then(data => {
        console.log("Server response:", data);
        if (data.debug) console.warn("Debug info:", data.debug);
        showQRResult(data);
    })
    .catch(err => {
        console.error(err);
        showError("Network error. Please try again.");
    });
}

document.getElementById('qrResultModal')
    .addEventListener('hidden.bs.modal', function () {
        clearAllTimers();
        unlockScan();
        window.currentExamineeId = null;
        // Always clear and refocus input after modal closes
        const manualQR = document.getElementById("manualQR");
        if (manualQR) {
            manualQR.value = "";
            manualQR.disabled = false;
            setTimeout(() => manualQR.focus(), 100); // Ensure modal is fully closed before focusing
        }
    });

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? "-";
}

function showQRResult(data) {
    clearAllTimers();
    // Clear input immediately so it's ready for the next scan
    const manualQR = document.getElementById("manualQR");
    if (manualQR) manualQR.value = "";

    // Normalize helpers
    const normalizeDate = (dateStr) => {
        if (!dateStr) return "";
        return new Date(dateStr).toISOString().split("T")[0];
    };
    const normalizeText = (text) => {
        if (!text) return "";
        return text.toString().trim().replace(/\s+/g, " ").toLowerCase();
    };

    // Schedule matching
    const isScheduleSelected = window.selectedRegion && window.selectedVenue && window.selectedDate && window.selectedScheduleId;
    const scannedVenue = normalizeText(data.venue);
    const selectedVenue = normalizeText(window.selectedVenue);
    const scannedDate = normalizeDate(data.exam_date);
    const selectedDate = normalizeDate(window.selectedDate);
    const isMatchingSchedule = isScheduleSelected && scannedVenue === selectedVenue && scannedDate === selectedDate;

    let statusClass = data.status_class;
    let statusMessage = data.status_message;

    // Professional schedule mismatch handling
    if (isScheduleSelected && !isMatchingSchedule && data.status_class === "valid") {
        document.getElementById("exam_sched").style.display = "block";
        statusClass = "warning";
        statusMessage = "Schedule Mismatch";
    }

    // Capture examinee_id for valid/warning statuses
    window.currentExamineeId = (statusClass === "valid" || statusClass === "warning") ? data.examinee_id : null;

    // Populate modal content
    setText("name", data.name);
    setText("test_permit", data.test_permit);
    setText("examination_date", data.exam_date_display ?? data.exam_date);
    setText("examination_venue", data.venue);
    setText("invoice_no", data.invoice_no);
    setText("status", data.payment_status);
    setText("payment_date", data.payment_date);
    setText("payment_amount", data.amount);

    const statusBox = document.getElementById("verificationStatus");
    statusBox.className = "status-box text-center mb-4 " + statusClass;
    statusBox.textContent = statusMessage;

    const icon = document.getElementById("statusIcon");
    icon.className = "fa-solid";
    if (statusClass === "valid") icon.classList.add("fa-circle-check");
    else if (statusClass === "warning") icon.classList.add("fa-triangle-exclamation");
    else icon.classList.add("fa-circle-xmark");

    const modalElement = document.getElementById("qrResultModal");
    modalElement.classList.remove("modal-valid", "modal-invalid", "modal-warning", "modal-already_used", "modal-rejected");
    modalElement.classList.add("modal-" + statusClass);

<<<<<<< HEAD
=======
if (statusClass === "already_used") {
    document.getElementById("exam_sched").style.display = "none";
    applyActionState("completed");
    document.getElementById("autoCompleteTimer").style.display = "none";
    // Hide action buttons for already used
    document.getElementById("completeBtn").style.display = "none";
    document.getElementById("cancelBtn1").style.display = "none";
}
else if (statusClass === "rejected") {
    applyActionState("rejected");
    document.getElementById("autoCompleteTimer").style.display = "none";
    // Hide action buttons for rejected
    document.getElementById("completeBtn").style.display = "none";
    document.getElementById("cancelBtn1").style.display = "none";
}
else if (statusClass === "valid") {
    // For verified examinees - hide buttons, auto accept only
    applyActionState("hidden");
    document.getElementById("autoCompleteTimer").style.display = "none";
}
else if (statusClass === "warning") {
    // For schedule mismatch - show buttons for manual decision
    applyActionState("default");
    document.getElementById("autoCompleteTimer").style.display = "none";
}
else {
    applyActionState("completed"); // truly invalid only
    document.getElementById("autoCompleteTimer").style.display = "none";
    // Hide action buttons for invalid
    document.getElementById("completeBtn").style.display = "none";
    document.getElementById("cancelBtn1").style.display = "none";
}
    const modal = new bootstrap.Modal(modalElement);
    if (statusClass === "valid") {
        // Capture the examinee ID BEFORE starting auto-complete
        const examineeId = window.currentExamineeId;
        console.log("DEBUG: Captured examineeId for auto-complete =", examineeId);
        startAutoComplete(modal, examineeId);
    } else {
        document.getElementById("autoCompleteTimer").style.display = "none";
    }

    modal.show();
}
function startAutoComplete(modal, examineeId) {
    const timerDiv = document.getElementById("autoCompleteTimer");
    const timerCount = document.getElementById("timerCount");


    console.log("DEBUG: startAutoComplete called with examineeId =", examineeId);
    const completeBtn = document.getElementById("completeBtn");

    const runId = Date.now();
    window.currentAutoCompleteRun = runId;

    timerDiv.style.display = "block";
    completeBtn.style.display = "none";

    const startTime = Date.now();
    const duration = 3000;

    if (window.autoCompleteInterval) clearInterval(window.autoCompleteInterval);
    if (window.autoCompleteTimeout) clearTimeout(window.autoCompleteTimeout);

    window.autoCompleteInterval = setInterval(() => {

        if (window.currentAutoCompleteRun !== runId) {
            clearInterval(window.autoCompleteInterval);
            return;
        }

        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, Math.ceil((duration - elapsed) / 1000));
        timerCount.textContent = remaining;

        if (elapsed >= duration) {

            clearInterval(window.autoCompleteInterval);

            if (window.currentAutoCompleteRun !== runId) return;

            timerDiv.innerHTML =
                '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Accepted</span>';

            updateStatusWithId("complete", examineeId);
            applyActionState("completed");

            window.autoCompleteTimeout = setTimeout(() => {
                if (window.currentAutoCompleteRun !== runId) return;

                const modalElement = document.getElementById("qrResultModal");
                const activeModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                activeModal.hide();
            }, 1000);
        }

    }, 100);

}
document.addEventListener("DOMContentLoaded", function () {

>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    const completeBtn = document.getElementById("completeBtn");
    const cancelBtn1 = document.getElementById("cancelBtn1");
    const timerDiv = document.getElementById("autoCompleteTimer");
    timerDiv.style.display = "none";
    timerDiv.innerHTML = '<span class="auto-timer"><i class="fa-solid fa-clock"></i> Auto accepting in <strong id="timerCount">3</strong>s</span>';

    if (statusClass === "valid") {
        completeBtn.style.display = "none";
        cancelBtn1.style.display = "none";
        updateStatusWithId("complete", window.currentExamineeId);
        timerDiv.style.display = "block";
        timerDiv.innerHTML = '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Accepted</span>';
        autoCloseTimeout = setTimeout(hideModal, 2000);
    } else if (statusClass === "warning") {
        completeBtn.style.display = "inline-block";
        cancelBtn1.style.display = "inline-block";
        completeBtn.textContent = "Accept";
        cancelBtn1.textContent = "Cancel";
        completeBtn.disabled = false;
        cancelBtn1.disabled = false;
        completeBtn.className = "btn btn-success btn-sm px-4";
        cancelBtn1.className = "btn btn-danger btn-sm px-4";
        document.getElementById("exam_sched").style.display = "block";
    } else {
        completeBtn.style.display = "none";
        cancelBtn1.style.display = "none";
        document.getElementById("exam_sched").style.display = "none";
        autoCloseTimeout = setTimeout(hideModal, 2000);
    }

    const modal = new bootstrap.Modal(modalElement);
    scanLocked = true;
    modal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    const completeBtn = document.getElementById("completeBtn");
    const cancelBtn1 = document.getElementById("cancelBtn1");
    const scanBtn = document.getElementById("scanBtn");
    const manualQR = document.getElementById("manualQR");

    // Helper to check schedule selection
    function isScheduleSelected() {
        return window.selectedRegion && window.selectedVenue && window.selectedDate && window.selectedScheduleId;
    }

    // Disable scan button and manual QR input if no schedule selected
    function updateScanControls() {
        if (scanBtn) scanBtn.disabled = !isScheduleSelected();
        if (manualQR) manualQR.disabled = !isScheduleSelected();
    }

    // Initial state
    updateScanControls();

    // Listen for schedule selection changes
    document.addEventListener("scheduleSelectionChanged", function() {
        updateScanControls();
        // Focus manual QR input if schedule is selected
        if (isScheduleSelected() && manualQR) {
            manualQR.disabled = false;
            manualQR.focus();
        }
    });

    // Patch selectSchedule to dispatch event
    window._originalSelectSchedule = window.selectSchedule;
    window.selectSchedule = function(region, venue, date, element) {
        if (window._originalSelectSchedule) {
            window._originalSelectSchedule(region, venue, date, element);
        }
        const event = new Event("scheduleSelectionChanged");
        document.dispatchEvent(event);
    };

    if (completeBtn) {
        completeBtn.addEventListener("click", function () {
            if (!window.currentExamineeId) return;
            clearAllTimers();
            updateStatusWithId("complete", window.currentExamineeId);
            completeBtn.style.display = "none";
            cancelBtn1.style.display = "none";
            const timerDiv = document.getElementById("autoCompleteTimer");
            timerDiv.style.display = "block";
            timerDiv.innerHTML = '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Accepted</span>';
            autoCloseTimeout = setTimeout(hideModal, 1000);
        });
    }
    if (cancelBtn1) {
        cancelBtn1.addEventListener("click", function () {
            hideModal();
        });
    }
});

function updateStatusWithId(action, examineeId) {
    if (!examineeId) {
        console.error("Error: No examinee ID provided.");
        return;
    }
    fetch("php/update_examinee_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            examinee_id: examineeId,
            action: action,
            attended_schedule_id: window.selectedScheduleId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
<<<<<<< HEAD
            console.error("Status update failed:", data.message);
        } else {
=======
            alert("Error: " + data.message);

            // Re-enable if server failed
            applyActionState("default");
        } else {
            // Clear the manual QR input on successful completion
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
            const manualQR = document.getElementById("manualQR");
            if (manualQR) manualQR.value = "";
        }
    })
    .catch(err => {
        console.error("Error updating status:", err);
    });
}
