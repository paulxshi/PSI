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
          <div class="user-card-header">
            <h5 class="user-name">
              ${user.first_name} ${user.middle_name ? user.middle_name + '. ' : ''}${user.last_name}
            </h5>
            <p class="user-role">${roleDisplay}</p>
            <span class="status-pill ${statusBadgeClass}">
              ${statusDisplay}
            </span>
            <input type="hidden" id="user_id" value="${user.user_id}">
          </div>
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

      // Display profile picture in the qr-card upload area
      const profileImageInput = document.getElementById("profileImageInput");
      const imagePreview = document.getElementById("imagePreview");
      const uploadPlaceholder = document.getElementById("uploadPlaceholder");
      const imageText = document.getElementById("imageText");

      // Check if user has a profile picture and display it
      if (user.profile_picture) {
        imagePreview.src = "../" + user.profile_picture;
        imagePreview.style.display = "block";
        if (uploadPlaceholder) {
          uploadPlaceholder.style.display = "none";
        }
      }

      // Update View Permit button based on profile picture
      const viewPermitBtn = document.getElementById('btnViewPermit');
      if (viewPermitBtn) {
        const hasProfilePicture = user.profile_picture && user.profile_picture.trim() !== '';
        viewPermitBtn.disabled = !hasProfilePicture;
        viewPermitBtn.style.opacity = hasProfilePicture ? '1' : '0.5';
        viewPermitBtn.style.cursor = hasProfilePicture ? 'pointer' : 'not-allowed';
      }

      // Fetch transaction number from PHP
      fetch("php/get_transaction.php?user_id=" + userId)
        .then(response => response.json())
        .then(data => {
          if (data.status !== "success") {
            alert("Unable to retrieve transaction number.");
            return;
          }

          const transactionNo = data.external_id;
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

          // Initially mask the QR value by applying a blur effect (hide it)
          qrText.style.filter = "blur(5px)";  // Mask QR value by default
          maskButton.textContent = "VIEW";  // Set button text to "VIEW" initially

          // Add event listener for the mask/unmask button
          maskButton.addEventListener("click", () => {
            if (qrText.style.filter === "blur(5px)") {
              qrText.style.filter = "none";  // Unblur the QR value
              maskButton.textContent = "HIDE";  // Update button text to "HIDE"
            } else {
              qrText.style.filter = "blur(5px)";  // Blur the QR value
              maskButton.textContent = "VIEW";  // Update button text to "VIEW"
            }
          });

          // Download Function for QR Code
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

      // Fetch exam details
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