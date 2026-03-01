document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("userCard");

  fetch("php/get_user.php")
    .then(res => {
      console.log("Response status:", res.status);
      if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
      return res.json();
    })
    .then(data => {
      console.log("Response data:", data);

      if (!data.success) {
        console.error("API Error:", data.message);
        
        // Handle redirect if provided (e.g., incomplete registration)
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
        }
        
        container.innerHTML = `<p class="text-danger p-4">${data.message || "Failed to load user data."}</p>`;
        return;
      }

      const user = data.user;
      console.log("User data:", user);

      // Format date of birth
      const dob = new Date(user.date_of_birth);
      const dobFormatted = dob.toLocaleDateString('en-US', {
        month: 'long', day: 'numeric', year: 'numeric'
      });

      const purposeEl = document.getElementById("purpose");
      if (purposeEl) purposeEl.textContent = user.purpose || "N/A";

      // Format role / status
      const roleDisplay    = user.role === 'admin' ? 'Administrator' : 'Examinee';
      const statusBadge    = user.status === 'active' ? 'bg-success' : 'bg-warning';
      const statusDisplay  = user.status
        ? user.status.charAt(0).toUpperCase() + user.status.slice(1)
        : 'Inactive';

      const firstInitial  = (user.first_name  || '').charAt(0).toUpperCase();
      const lastInitial   = (user.last_name   || '').charAt(0).toUpperCase();
      const monogram      = firstInitial + lastInitial;

      const card = `
        <div class="user-card">

          <!-- ── HEADER ── -->
          <div class="user-card-header">
            <div class="user-card-header-monogram">
              <span class="monogram-initials">${monogram}</span>
            </div>

            <div class="user-card-header-info">
              <h5 class="user-name">
                ${user.first_name}${user.middle_name ? ' ' + user.middle_name + '.' : ''} ${user.last_name}
              </h5>
              <p class="user-role">${roleDisplay}</p>
              <span class="status-pill ${statusBadge}">${statusDisplay}</span>

              <div class="user-card-meta">
                ${user.email ? `
                <span class="meta-tag">
                  <i class='bx bx-envelope'></i>
                  ${user.email}
                </span>` : ''}
                ${user.contact_number ? `
                <span class="meta-tag">
                  <i class='bx bx-phone'></i>
                  ${user.contact_number}
                </span>` : ''}
              </div>
            </div>

            <input type="hidden" id="user_id" value="${user.user_id}">
          </div>

          <!-- ── BODY ── -->
          <div class="user-card-body">
              <div class="info-row">
                <span class="info-label">Test Permit No.</span>
                <span class="info-value">
                  ${user.test_permit
                    ? `<span><i class='bx bx-id-card'></i> ${user.test_permit}</span>`
                    : '<span style="color:var(--ink-40);font-size:0.85rem;">Not yet assigned</span>'}
                </span>
              </div>

            <div class="info-section-title">Personal Details</div>

            <div class="info-grid-2col">
              <div class="info-row">
                <span class="info-label">Birthday</span>
                <span class="info-value">${dobFormatted}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Age</span>
                <span class="info-value">${user.age}</span>
              </div>
              ${user.school ? `
              <div class="info-row">
                <span class="info-label">School</span>
                <span class="info-value">${user.school}</span>
              </div>` : ''}
            </div>

            <div class="info-section-title">Contact Details</div>

            <div class="info-grid-2col">
              <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">${user.email}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Contact</span>
                <span class="info-value">${user.contact_number}</span>
              </div>
            </div>

          </div>
        </div>
      `;

      container.insertAdjacentHTML("beforeend", card);

      const userId = document.getElementById("user_id").value;

      const imagePreview      = document.getElementById("imagePreview");
      const uploadPlaceholder = document.getElementById("uploadPlaceholder");

      if (user.profile_picture) {
        imagePreview.src = "../" + user.profile_picture;
        imagePreview.style.display = "block";
        if (uploadPlaceholder) uploadPlaceholder.style.display = "none";
      }

      const viewPermitBtn = document.getElementById('btnViewPermit');
      if (viewPermitBtn) {
        const hasPhoto = user.profile_picture && user.profile_picture.trim() !== '';
        viewPermitBtn.disabled       = !hasPhoto;
        viewPermitBtn.style.opacity  = hasPhoto ? '1' : '0.5';
        viewPermitBtn.style.cursor   = hasPhoto ? 'pointer' : 'not-allowed';
      }

      Promise.all([
        fetch("php/get_transaction.php?user_id=" + userId).then(r => r.json()),
        fetch("php/get_exam_details.php?user_id="  + userId).then(r => {
          if (!r.ok) throw new Error("Network response was not ok");
          return r.json();
        })
      ])
      .then(([txData, examData]) => {

        if (txData.status === "success") {
          const transactionNo = txData.external_id;
          const qrTextEl      = document.getElementById("qrText");
          const qrContainerEl = document.getElementById("qrContainer");
          const maskButton    = document.getElementById("maskButton");
          const downloadBtn   = document.getElementById("downloadQR");

          if (qrTextEl)      qrTextEl.innerText = transactionNo;

          if (qrContainerEl) {
            const qr = new QRCode(qrContainerEl, {
              text: transactionNo, width: 180, height: 180,
            });
          }

          if (qrTextEl) {
            qrTextEl.style.filter = "blur(5px)";
          }

          if (maskButton) {
            maskButton.textContent = "VIEW";
            maskButton.addEventListener("click", () => {
              if (qrTextEl.style.filter === "blur(5px)") {
                qrTextEl.style.filter = "none";
                maskButton.textContent = "HIDE";
              } else {
                qrTextEl.style.filter = "blur(5px)";
                maskButton.textContent = "VIEW";
              }
            });
          }

          if (downloadBtn) {
            downloadBtn.addEventListener("click", () => {
              const canvas = document.querySelector("#qrContainer canvas");
              if (!canvas) return;
              const ctx  = canvas.getContext("2d");
              const logo = new Image();
              logo.src   = "../imgs/PSI.png";
              logo.onload = () => {
                const size = canvas.width * 0.22;
                const x = (canvas.width  - size) / 2;
                const y = (canvas.height - size) / 2;
                ctx.fillStyle = "#ffffff";
                ctx.beginPath();
                ctx.arc(x + size / 2, y + size / 2, size / 2 + 4, 0, Math.PI * 2);
                ctx.fill();
                ctx.drawImage(logo, x, y, size, size);
                const link   = document.createElement("a");
                link.download = transactionNo + "_QR.png";
                link.href     = canvas.toDataURL("image/png");
                link.click();
              };
            });
          }
        }

        if (examData.status === "success") {
          const fmt = (dateStr) => dateStr
            ? new Date(dateStr).toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" })
            : "N/A";

          const regEl   = document.getElementById("registration-date");
          const examEl  = document.getElementById("examination-date");
          const venueEl = document.getElementById("examination-venue");

          if (regEl)   regEl.textContent   = fmt(examData.date_of_registration);
          if (examEl)  examEl.textContent  = fmt(examData.date_of_test);
          if (venueEl) venueEl.textContent =
            (examData.venue_name ? examData.venue_name + ", " : "") + (examData.region || "N/A");
        } else {
          console.warn(examData.message || "No exam details found.");
        }

      })
      .catch(err => console.error("Parallel fetch error:", err));

    })
    .catch(err => {
      console.error("Fetch error:", err);
      container.innerHTML = `<p class="text-danger p-4">Error loading user data: ${err.message}</p>`;
    });
});
