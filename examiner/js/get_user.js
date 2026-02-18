document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("userCard");

  fetch("php/get_user.php")
    .then(res => {
      console.log("Response status:", res.status);
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then(data => {
      console.log("Response data:", data);
      
      if (!data.success) {
        console.error("API Error:", data.message);
        container.innerHTML = `<p class="text-danger">${data.message || "Failed to load user data."}</p>`;
        return;
      }

      const user = data.user;
      console.log("User data:", user);

      // Format date of birth
      const dob = new Date(user.date_of_birth);
      const dobFormatted = dob.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      });

 
      const purposeEl = document.getElementById("purpose");
      if (purposeEl) purposeEl.textContent = user.purpose || "N/A";

      // Format role display
      const roleDisplay = user.role === 'admin' ? 'Administrator' : 'Examinee';
      
      // Format status display
      const statusBadgeClass = user.status === 'active' ? 'bg-success' : 'bg-warning';
      const statusDisplay = user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Inactive';

      // Build user card
      const card = `
        <div>
          <div>
            <div class="card-body text-center border-bottom py-4">
            <input type="hidden" id="user_id" name="user_id" value="${user.user_id}">
              <h5 class="fw-bold mb-1">${user.first_name} ${user.middle_name ? user.middle_name + '. ' : ''}${user.last_name}</h5>
              <p class="text-muted mb-2">${roleDisplay}</p>
              <div class="d-flex justify-content-center gap-2">
                <span class="badge ${statusBadgeClass}">${statusDisplay}</span>
              </div>
            </div>
            <div class="card-body px-4">
              <div class="mb-3">
                <small class="text-muted text-uppercase">Test Permit</small>
                <div class="fw-semibold">${user.test_permit ?? "N/A"}</div>
              </div>
              <div class="mb-3">
                <small class="text-muted text-uppercase">Email Address</small>
                <div class="fw-semibold">${user.email}</div>
              </div>
              <div class="mb-3">
                <small class="text-muted text-uppercase">Contact Number</small>
                <div class="fw-semibold">${user.contact_number}</div>
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <small class="text-muted text-uppercase">Birthday</small>
                  <div class="fw-semibold">${dobFormatted}</div>
                </div>
                <div class="col-6 mb-3">
                  <small class="text-muted text-uppercase">Age</small>
                  <div class="fw-semibold">${user.age}</div>
                </div>
              </div>
              ${user.school ? `
              <div class="mb-3">
                <small class="text-muted text-uppercase">School</small>
                <div class="fw-semibold">${user.school}</div>
              </div>
              ` : ''}
              ${user.region ? `
              <div class="mb-3">
                <small class="text-muted text-uppercase">Region</small>
                <div class="fw-semibold">${user.region}</div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
      `;

      container.insertAdjacentHTML("beforeend", card);


          
const userId = document.getElementById("user_id").value;

// 🔥 Fetch transaction number from PHP
fetch("php/get_transaction.php?user_id=" + userId)
  .then(response => response.json())
  .then(data => {

    if (data.status !== "success") {
      alert("Unable to retrieve transaction number.");
      return;
    }

    const transactionNo = data.transaction_no;


    // QR Value (what gets scanned)
    const qrValue = transactionNo;

    document.getElementById("qrText").innerText = qrValue;

    // Generate QR Code
    const qr = new QRCode(document.getElementById("qrContainer"), {
      text: qrValue,
      width: 180,
      height: 180,
    });

    // Download Function
    document.getElementById("downloadQR").addEventListener("click", function () {
      const img = document.querySelector("#qrContainer img");
      const link = document.createElement("a");
      link.href = img.src;
      link.download = transactionNo + "_QR.png";
      link.click();
    });

  })
  .catch(error => console.error(error));


fetch("php/get_exam_details.php?user_id=" + userId)
  .then(response => {
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return response.json();
  })
  .then(data => {

    if (data.status !== "success") {
      console.warn(data.message || "No exam details found.");
      return;
    }

    // Format registration date
    const registrationDate = data.date_of_registration
      ? new Date(data.date_of_registration).toLocaleDateString("en-US", {
          month: "long",
          day: "numeric",
          year: "numeric"
        })
      : "N/A";

          const examinationDate = data.date_of_test
      ? new Date(data.date_of_test).toLocaleDateString("en-US", {
          month: "long",
          day: "numeric",
          year: "numeric"
        })
      : "N/A";

    const registrationEl = document.getElementById("registration-date");
    if (registrationEl) {
      registrationEl.textContent = registrationDate || "N/A";
    }

    const examDateEl = document.getElementById("examination-date");
    if (examDateEl) {
      examDateEl.textContent = examinationDate || "N/A";
    }

    const venueNameEl = document.getElementById("examination-venue");
    if (venueNameEl) {
      venueNameEl.textContent =
        (data.venue_name ? data.venue_name + ", " : "") +
        (data.region || "N/A");
    }

  })
  .catch(error => {
    console.error("Error fetching exam details:", error);
  });


    })
    .catch(err => {
      console.error("Fetch error:", err);
      container.innerHTML = `<p class="text-danger">Error loading user data: ${err.message}</p>`;
    });




});
