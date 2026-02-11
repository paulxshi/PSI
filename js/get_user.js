document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("userCard");

  fetch("php/get_user.php")
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        container.innerHTML = `<p class="text-danger">${data.message || "Failed to load user data."}</p>`;
        return;
      }

      const user = data.user;

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

      // Build user card
      const card = `
        <div class="col-12 col-lg-6 col-xl-4">
          <div class="card border-1 rounded-3 h-100">
            <div class="card-body text-center border-bottom py-4">
              <h5 class="fw-bold mb-1">${user.first_name} ${user.middle_name}. ${user.last_name}</h5>
              <p class="text-muted mb-2">Student</p>
              <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-success">${user.test_permit ? "Active" : "Inactive"}</span>
              </div>
            </div>
            <div class="card-body px-4">
              <div class="mb-3">
                <small class="text-muted text-uppercase">Test Permit</small>
                <div class="fw-semibold">${user.test_permit ?? "-"}</div>
              </div>
              <div class="mb-3">
                <small class="text-muted text-uppercase">Email Address</small>
                <div class="fw-semibold">${user.email}</div>
              </div>
              <div class="mb-3">
                <small class="text-muted text-uppercase">Contact Number</small>
                <div class="fw-semibold">+63 ${user.contact_number}</div>
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
            </div>
          </div>
        </div>
      `;

      container.insertAdjacentHTML("beforeend", card);

    })
    .catch(err => {
      container.innerHTML = `<p class="text-danger">Error loading user data</p>`;
      console.error(err);
    });
});
