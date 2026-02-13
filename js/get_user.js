document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("userCard");

  fetch("../php/get_user.php")
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

      // Format date of registration
      const regDate = user.date_of_registration 
        ? new Date(user.date_of_registration).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
          })
        : "N/A";

      // Populate exam-info section if exists
      const regDateEl = document.getElementById("registration-date");
      const purposeEl = document.getElementById("purpose");
      if (regDateEl) regDateEl.textContent = regDate;
      if (purposeEl) purposeEl.textContent = user.purpose || "N/A";

      // Format role display
      const roleDisplay = user.role === 'admin' ? 'Administrator' : 'Examinee';
      
      // Format status display
      const statusBadgeClass = user.status === 'active' ? 'bg-success' : 'bg-warning';
      const statusDisplay = user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Inactive';

      // Build user card
      const card = `
        <div class="col-12 col-lg-6 col-xl-4">
          <div class="card border-1 rounded-3 h-100">
            <div class="card-body text-center border-bottom py-4">
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

    })
    .catch(err => {
      console.error("Fetch error:", err);
      container.innerHTML = `<p class="text-danger">Error loading user data: ${err.message}</p>`;
    });
});
