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
          <div class="user-card">

            <!-- Header -->
            <div class="user-card-header">
              <div class="avatar" style="background: ${user.profile_picture ? 'transparent' : ''}; padding: 0;">
                ${user.profile_picture ? 
                  `<img src="../${user.profile_picture}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">` :
                  `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`
                }
              </div>

              <h5 class="user-name">
                ${user.first_name} ${user.middle_name ? user.middle_name + '. ' : ''}${user.last_name}
              </h5>

              <p class="user-role">${roleDisplay}</p>

              <span class="status-pill ${statusBadgeClass}">
                ${statusDisplay}
              </span>

              <input type="hidden" id="user_id" value="${user.user_id}">
            </div>

            <!-- Body -->
            <div class="user-card-body">

              <div class="info-row">
                <span class="info-label">Test Permit</span>
                <span class="info-value">${user.test_permit ?? "N/A"}</span>
              </div>

              <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">${user.email}</span>
              </div>

              <div class="info-row">
                <span class="info-label">Contact</span>
                <span class="info-value">${user.contact_number}</span>
              </div>

              <div class="info-grid">
                <div>
                  <span class="info-label">Birthday</span>
                  <span class="info-value">${dobFormatted}</span>
                </div>
                <div>
                  <span class="info-label">Age</span>
                  <span class="info-value">${user.age}</span>
                </div>
              </div>

              ${user.school ? `
              <div class="info-row">
                <span class="info-label">School</span>
                <span class="info-value">${user.school}</span>
              </div>
              ` : ''}

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

    const transactionNo = data.external_id;


    // QR Value (what gets scanned)
    const qrValue = transactionNo;

    document.getElementById("qrText").innerText = qrValue;

    // Generate QR Code
    const qr = new QRCode(document.getElementById("qrContainer"), {
      text: qrValue,
      width: 180,
      height: 180,
    });

 const maskButton = document.getElementById("maskButton");
    const qrText = document.getElementById("qrText");

    maskButton.addEventListener("click", () => {
      if (qrText.style.filter === "blur(5px)") {
        qrText.style.filter = "none";  // Unblur the QR value
        maskButton.textContent = "Mask QR Value";  // Update button text
      } else {
        qrText.style.filter = "blur(5px)";  // Blur the QR value
        maskButton.textContent = "Unmask QR Value";  // Update button text
      }
    });

    // Initially, don't apply blur to the QR value
    qrText.style.filter = "none";
    
    // Download Function
document.getElementById("downloadQR").addEventListener("click", () => {
  const canvas = document.querySelector("#qrContainer canvas");
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  const logo = new Image();
  logo.src = "../imgs/PSI.png";

  logo.onload = () => {
    const size = canvas.width * 0.22;
    const x = (canvas.width - size) / 2;
    const y = (canvas.height - size) / 2;

    // White background circle
    ctx.fillStyle = "#ffffff";
    ctx.beginPath();
    ctx.arc(
      x + size / 2,
      y + size / 2,
      size / 2 + 4,
      0,
      Math.PI * 2
    );
    ctx.fill();

    // Draw logo
    ctx.drawImage(logo, x, y, size, size);

    const link = document.createElement("a");
    link.download = transactionNo + "_QR.png";
    link.href = canvas.toDataURL("image/png");
    link.click();
  };
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

document.querySelectorAll('.faq-toggle').forEach(toggle => {
  toggle.addEventListener('click', () => {
    const row = toggle.closest('.faq-row');
    const content = row.querySelector('.faq-content-wrapper');

    // Close all others
    document.querySelectorAll('.faq-row').forEach(r => {
      if (r !== row) {
        r.classList.remove('active');
        r.querySelector('.faq-content-wrapper').classList.remove('open');
      }
    });

    // Toggle current
    row.classList.toggle('active');
    content.classList.toggle('open');
  });
});

// Listen for profile picture updates
window.addEventListener('storage', (e) => {
  if (e.key === 'profilePictureUpdated') {
    // Reload the page to show updated profile picture
    location.reload();
  }
});

// Check on focus (when returning from account settings page)
let lastPictureCheck = localStorage.getItem('profilePictureUpdated');
window.addEventListener('focus', () => {
  const currentCheck = localStorage.getItem('profilePictureUpdated');
  if (currentCheck && currentCheck !== lastPictureCheck) {
    lastPictureCheck = currentCheck;
    location.reload();
  }
});