document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("loginFormElement");
  const msg = document.getElementById("loginMessage");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    msg.style.display = "none";
    msg.textContent = "";

    const formData = new FormData(this);

    fetch("php/login.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {

      msg.style.display = "block";
      msg.textContent = data.message;
      msg.className = "mt-3 text-center " + 
        (data.success ? "text-success" : "text-danger");

      if (data.success) {
        // Redirect based on user role
        setTimeout(() => {
          if (data.role === 'admin') {
            window.location.href = "admin/dashboard.html";
          } else {
            window.location.href = "examiner/dashboard.html";
          }
        }, 800); 
      } else if (data.redirect) {
        // If login failed but redirect is provided (incomplete registration)
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 1500);
      }

    })
    .catch(err => {
      msg.style.display = "block";
      msg.textContent = "Something went wrong. Please try again.";
      msg.className = "mt-3 text-center text-danger";
      console.error(err);
    });
  });

  // FORGOT PASSWORD MODAL HANDLER
  const forgotForm = document.getElementById("forgotPasswordForm");
  const forgotMsg = document.getElementById("forgotPasswordMessage");
  const sendResetBtn = document.getElementById("sendResetBtn");

  forgotForm.addEventListener("submit", function (e) {
    e.preventDefault();

    forgotMsg.style.display = "none";
    forgotMsg.textContent = "";

    const email = document.getElementById("forgotEmail").value.trim();

    if (!email) {
      forgotMsg.style.display = "block";
      forgotMsg.textContent = "Please enter your email address.";
      forgotMsg.className = "mb-3 text-danger small";
      return;
    }

    sendResetBtn.disabled = true;
    sendResetBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

    fetch("php/forgot_password.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ email })
    })
    .then(res => res.json())
    .then(data => {
      forgotMsg.style.display = "block";
      forgotMsg.textContent = data.message;
      forgotMsg.className = "mb-3 " + (data.success ? "text-success small" : "text-danger small");

      if (data.success) {
        // Store email in sessionStorage for OTP verification
        sessionStorage.setItem("reset_email", email);
        sessionStorage.setItem("reset_flow", "true");

        // Close modal after short delay and redirect to OTP verification
        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById("forgotPasswordModal"));
          if (modal) {
            modal.hide();
          }
          window.location.href = "forgot_password_otp.html";
        }, 1500);
      }
    })
    .catch(err => {
      console.error("Error:", err);
      forgotMsg.style.display = "block";
      forgotMsg.textContent = "Something went wrong. Please try again.";
      forgotMsg.className = "mb-3 text-danger small";
    })
    .finally(() => {
      sendResetBtn.disabled = false;
      sendResetBtn.textContent = "Send Reset Code";
    });
  });

});
