document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("adminLoginForm");
  const msg = document.getElementById("adminLoginMessage");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    msg.style.display = "none";
    msg.textContent = "";

    const email = document.querySelector('input[name="email"]').value.trim();
    const password = document.querySelector('input[name="password"]').value;

    // Basic validation
    if (!email || !password) {
      msg.style.display = "block";
      msg.textContent = "Please enter both email and password";
      msg.className = "mb-3 text-center text-danger";
      return;
    }

    // Create FormData for POST
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    // Disable button during submission
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';

    fetch("php/admin_login.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      msg.style.display = "block";
      msg.textContent = data.message;
      msg.className = "mb-3 text-center " + 
        (data.success ? "text-success" : "text-danger");

      if (data.success) {
        // Redirect to admin dashboard after successful login
        setTimeout(() => {
          window.location.href = "admin/dashboard.html";
        }, 800);
      } else {
        // Re-enable button on failure
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    })
    .catch(err => {
      console.error("Error:", err);
      msg.style.display = "block";
      msg.textContent = "Something went wrong. Please try again.";
      msg.className = "mb-3 text-center text-danger";
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });
  });
});
