let scanLocked = false;



function onScanSuccess(decodedText) {

    if (scanLocked) return;
    scanLocked = true;

    // Check if a schedule is selected
    if (!window.selectedRegion || !window.selectedVenue || !window.selectedDate) {
        alert("Please select a schedule first before scanning.");
        scanLocked = false;
        return;
    }

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
    const cancelBtn1 = document.getElementById("cancelBtn1");

    // Reset styles
    completeBtn.disabled = false;
   

    completeBtn.style.display = "inline-block";
    cancelBtn1.style.display = "inline-block";

    completeBtn.classList.remove("btn-success", "btn-secondary", "btn-outline-success");
    cancelBtn1.classList.remove("btn-danger", "btn-secondary", "btn-outline-danger");

    if (state === "completed") {
        completeBtn.textContent = "Accepted";
        cancelBtn1.textContent = "Cancel";

        completeBtn.disabled = true;
        cancelBtn1.disabled = true;

        completeBtn.classList.add("btn-outline-secondary");
        cancelBtn1.classList.add("btn-outline-secondary");
    }
  
    else if (state === "hidden") {
        completeBtn.style.display = "none";
        cancelBtn1.style.display = "none";
    }
    else {
        // Default usable state
        completeBtn.textContent = "Accept";
        cancelBtn1.textContent = "Cancel";

        completeBtn.classList.add("btn-success");
        cancelBtn1.classList.add("btn-danger");
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

    /* -----------------------------
       Helper: Normalize DATE safely
       (NO timezone conversion!)
    ------------------------------*/
    const normalizeDate = (dateStr) => {
        if (!dateStr) return "";

        // If format is "YYYY-MM-DD" or "YYYY-MM-DDTHH:mm:ss"
        return dateStr.toString().split("T")[0].trim();
    };

    /* -----------------------------
       Helper: Normalize TEXT safely
       Removes hidden chars, double spaces, etc.
    ------------------------------*/
    const normalizeText = (text) => {
        if (!text) return "";
        return text
            .toString()
            .trim()
            .replace(/\s+/g, " ")   // collapse multiple spaces
            .toLowerCase();
    };

    /* -----------------------------
       Get selected schedule (dashboard)
    ------------------------------*/
    const isScheduleSelected =
        window.selectedRegion &&
        window.selectedVenue &&
        window.selectedDate;

    const scannedVenue = normalizeText(data.venue);
    const selectedVenue = normalizeText(window.selectedVenue);

    const scannedDate = normalizeDate(data.exam_date);
    const selectedDate = normalizeDate(window.selectedDate);

    const isMatchingSchedule =
        isScheduleSelected &&
        scannedVenue === selectedVenue &&
        scannedDate === selectedDate;

    /* -----------------------------
       DEBUG LOG (keep for now)
    ------------------------------*/
    console.log("==== MATCH DEBUG ====");
    console.log("Selected Venue:", selectedVenue);
    console.log("Scanned Venue :", scannedVenue);
    console.log("Selected Date :", selectedDate);
    console.log("Scanned Date  :", scannedDate);
    console.log("Is Matching   :", isMatchingSchedule);

    /* -----------------------------
       Determine status override
    ------------------------------*/
    let statusClass = data.status_class;
    let statusMessage = data.status_message;

    if (isScheduleSelected && !isMatchingSchedule && data.status_class === "valid") {
        document.getElementById("exam_sched").style.display = "block";
        statusClass = "warning";
        statusMessage = "Schedule Mismatch";
    }

    /* -----------------------------
       Only allow action if VALID or WARNING (schedule mismatch)
    ------------------------------*/

window.currentExamineeId =
    (statusClass === "valid" || statusClass === "warning")
        ? data.examinee_id
        : null;

    // DEBUG: Log the examinee ID
    console.log("DEBUG: Setting currentExamineeId =", window.currentExamineeId);
    console.log("DEBUG: statusClass =", statusClass);
    console.log("DEBUG: data.examinee_id =", data.examinee_id);
    /* -----------------------------
       Fill modal fields
    ------------------------------*/
    setText("name", data.name);
    setText("test_permit", data.test_permit);
    setText("examination_date", data.exam_date_display ?? data.exam_date);
    setText("examination_venue", data.venue);
    setText("invoice_no", data.invoice_no);
    setText("status", data.payment_status);
    setText("payment_date", data.payment_date);
    setText("payment_amount", data.amount);

    /* -----------------------------
       Update verification status box
    ------------------------------*/
    const statusBox = document.getElementById("verificationStatus");
    statusBox.className = "status-box text-center mb-4 " + statusClass;
    statusBox.textContent = statusMessage;


    const icon = document.getElementById("statusIcon");
    icon.className = "fa-solid";

    if (statusClass === "valid") icon.classList.add("fa-circle-check");
    else if (statusClass === "warning") icon.classList.add("fa-triangle-exclamation");
    else icon.classList.add("fa-circle-xmark");

    /* -----------------------------
       Update modal color class
    ------------------------------*/
    const modalElement = document.getElementById("qrResultModal");
    modalElement.classList.remove(
        "modal-valid",
        "modal-invalid",
        "modal-warning",
        "modal-already_used",
        "modal-rejected"
    );
    modalElement.classList.add("modal-" + statusClass);

    /* -----------------------------
       Apply correct button state
    ------------------------------*/
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
    document.getElementById("completeBtn").style.display = "none";
    document.getElementById("cancelBtn1").style.display = "none";
    applyActionState("default");
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
    /* -----------------------------
       Handle auto-complete for valid scans
    ------------------------------*/
    const modal = new bootstrap.Modal(modalElement);
    if (statusClass === "valid") {
        // Capture the examinee ID BEFORE starting auto-complete
        const examineeId = window.currentExamineeId;
        console.log("DEBUG: Captured examineeId for auto-complete =", examineeId);
        startAutoComplete(modal, examineeId);
    } else {
        document.getElementById("autoCompleteTimer").style.display = "none";
    }

    /* -----------------------------
       Show modal
    ------------------------------*/
    modal.show();
}
function startAutoComplete(modal, examineeId) {
    const timerDiv = document.getElementById("autoCompleteTimer");
    const timerCount = document.getElementById("timerCount");


    // DEBUG: Log the captured examinee ID
    console.log("DEBUG: startAutoComplete called with examineeId =", examineeId);
    const completeBtn = document.getElementById("completeBtn");
    const cancelBtn = document.getElementById("cancelAutoComplete");

    // Create a UNIQUE run id (kills previous timers automatically)
    const runId = Date.now();
    window.currentAutoCompleteRun = runId;

    timerDiv.style.display = "block";
    completeBtn.style.display = "none";

    const startTime = Date.now();
    const duration = 5000;

    if (window.autoCompleteInterval) clearInterval(window.autoCompleteInterval);
    if (window.autoCompleteTimeout) clearTimeout(window.autoCompleteTimeout);

    window.autoCompleteInterval = setInterval(() => {

        // 🔴 If this is NOT the latest run, abort immediately
        if (window.currentAutoCompleteRun !== runId) {
            clearInterval(window.autoCompleteInterval);
            return;
        }

        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, Math.ceil((duration - elapsed) / 1000));
        timerCount.textContent = remaining;

        if (elapsed >= duration) {

            clearInterval(window.autoCompleteInterval);

            // 🔴 FINAL protection check
            if (window.currentAutoCompleteRun !== runId) return;

            timerDiv.innerHTML =
                '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i> Accepted</span>';

            // ✅ Only executes if NOT cancelled
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

    // Cancel button (INVALIDATES the run instantly)
    cancelBtn.onclick = () => {

        // 🔥 This kills ALL running logic immediately
        window.currentAutoCompleteRun = null;

        clearInterval(window.autoCompleteInterval);
        clearTimeout(window.autoCompleteTimeout);

        timerDiv.style.display = "none";
        completeBtn.style.display = "inline-block";
    };
}
document.addEventListener("DOMContentLoaded", function () {

    const completeBtn = document.getElementById("completeBtn");
    const cancelBtn1 = document.getElementById("cancelBtn1");

    if (completeBtn) {
        completeBtn.addEventListener("click", function () {

            if (!window.currentExamineeId) {
                alert("No examinee selected.");
                return;
            }

            applyActionState("completed");
            updateStatus("complete");
                setTimeout(() => {
                const modalElement = document.getElementById("qrResultModal");
                const activeModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                activeModal.hide();
            }, 1000);
        });
    }

 if (cancelBtn1) {
    cancelBtn1.addEventListener("click", function () {

        const modalElement = document.getElementById("qrResultModal");

        if (!modalElement) {
            console.log("Modal not found");
            return;
        }

        const modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

    // Keyboard shortcuts for modal buttons
    let lastEnterTime = 0;
    const doubleClickDelay = 300; // milliseconds

    document.addEventListener("keydown", function (event) {
        // Only listen for Enter key
        if (event.key !== "Enter") return;

        const completeBtn = document.getElementById("completeBtn");
        const timerDiv = document.getElementById("autoCompleteTimer");
        
        // Check if timer is still running
        const isTimerRunning = timerDiv && timerDiv.style.display === "block";
        
        // Check if Complete button is done (disabled or hidden)
        const isCompleteButtonDone = !completeBtn || completeBtn.disabled || completeBtn.style.display === "none";

        const currentTime = Date.now();
        const timeDiff = currentTime - lastEnterTime;

        if (isTimerRunning) {
            // Timer running → Single Enter closes modal (completion is automatic)
            const scanNextBtn = document.querySelector('[data-bs-dismiss="modal"]');
            if (scanNextBtn) {
                scanNextBtn.click();
            }
            lastEnterTime = 0;
        } else if (isCompleteButtonDone) {
            // Complete button already clicked/done → Single Enter closes modal
            const scanNextBtn = document.querySelector('[data-bs-dismiss="modal"]');
            if (scanNextBtn) {
                scanNextBtn.click();
            }
            lastEnterTime = 0;
        } else {
            // Complete button still available → Double Enter to close, Single to complete
            if (timeDiff < doubleClickDelay) {
                // Double Enter: Click SCAN NEXT button
                const scanNextBtn = document.querySelector('[data-bs-dismiss="modal"]');
                if (scanNextBtn) {
                    scanNextBtn.click();
                }
                lastEnterTime = 0; // Reset
            } else {
                // Single Enter: Click MARK AS COMPLETED button
                if (completeBtn && !completeBtn.disabled) {
                    completeBtn.click();
                }
                lastEnterTime = currentTime;
            }
        }
    });

});



function updateStatus(action) {

    // Validate data before sending
    if (!window.currentExamineeId) {
        console.error("Error: No examinee ID available. Cannot update status.");
        alert("Error: Missing examinee data. Please rescan.");
        applyActionState("default");
        return;
    }

    updateStatusWithId(action, window.currentExamineeId);
}

function updateStatusWithId(action, examineeId) {

    // Validate data before sending
    if (!examineeId) {
        console.error("Error: No examinee ID provided. Cannot update status.");
        alert("Error: Missing examinee data. Please rescan.");
        applyActionState("default");
        return;
    }

    console.log("DEBUG: updateStatusWithId called with action =", action, ", examineeId =", examineeId);

    fetch("php/update_examinee_status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
body: JSON.stringify({
    examinee_id: examineeId,
    action: action,
    attended_schedule_id: window.selectedScheduleId
})
    })
    .then(res => res.json())
    .then(data => {
        console.log("DEBUG: Response from update_examinee_status.php =", data);

        if (!data.success) {
            alert("Error: " + data.message);

            // Re-enable if server failed
            applyActionState("default");
        }

    })
    .catch(err => {
        console.error(err);
        alert("Error updating status: " + err);
        applyActionState("default");
    });
}

function closeModal() {
    // Hide or remove modal
    document.getElementById('qrResultModal').style.display = 'none';
}
