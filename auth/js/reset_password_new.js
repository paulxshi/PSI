document.addEventListener("DOMContentLoaded", () => {
    const resetId = sessionStorage.getItem("reset_id");
    const email = sessionStorage.getItem("reset_email");

    // Check if we have necessary data
    if (!resetId || !email) {
        window.location.href = "login.html";
        return;
    }

    const form = document.getElementById("resetPasswordForm");
    const newPasswordInput = document.getElementById("newPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const resetBtn = document.getElementById("resetBtn");
    const messageDiv = document.getElementById("message");
    const toggleNewPassword = document.getElementById("toggleNewPassword");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

    // Password requirement elements
    const reqLength = document.getElementById("req-length");
    const reqUpper = document.getElementById("req-upper");
    const reqLower = document.getElementById("req-lower");
    const reqNumber = document.getElementById("req-number");
    const reqMatch = document.getElementById("req-match");
    const strengthBar = document.getElementById("strengthBar");
    const strengthText = document.getElementById("strengthText");

    let isSubmitting = false;

    // Password toggle functionality
    toggleNewPassword.addEventListener("click", (e) => {
        e.preventDefault();
        const type = newPasswordInput.type === "password" ? "text" : "password";
        newPasswordInput.type = type;
        toggleNewPassword.innerHTML = type === "password" ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    toggleConfirmPassword.addEventListener("click", (e) => {
        e.preventDefault();
        const type = confirmPasswordInput.type === "password" ? "text" : "password";
        confirmPasswordInput.type = type;
        toggleConfirmPassword.innerHTML = type === "password" ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    // Real-time password validation
    newPasswordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validatePassword);

    function validatePassword() {
        const password = newPasswordInput.value;
        const confirm = confirmPasswordInput.value;

        // Check length (min 8 characters)
        const hasLength = password.length >= 8;
        updateRequirement(reqLength, hasLength);

        // Check uppercase
        const hasUpper = /[A-Z]/.test(password);
        updateRequirement(reqUpper, hasUpper);

        // Check lowercase
        const hasLower = /[a-z]/.test(password);
        updateRequirement(reqLower, hasLower);

        // Check number
        const hasNumber = /\d/.test(password);
        updateRequirement(reqNumber, hasNumber);

        // Check match
        const matches = password && confirm && password === confirm && password.length > 0;
        updateRequirement(reqMatch, matches);

        // Update password strength
        updatePasswordStrength(password);

        // Enable/disable submit button
        const isValid = hasLength && hasUpper && hasLower && hasNumber && matches;
        resetBtn.disabled = !isValid;
    }

    function updateRequirement(element, isValid) {
        if (isValid) {
            element.classList.remove("unchecked");
            element.classList.add("checked");
        } else {
            element.classList.remove("checked");
            element.classList.add("unchecked");
        }
    }

    function updatePasswordStrength(password) {
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        // Update strength display
        if (password.length === 0) {
            strengthBar.className = "strength-bar";
            strengthText.textContent = "";
            strengthText.className = "strength-text";
        } else if (strength <= 2) {
            strengthBar.className = "strength-bar weak";
            strengthText.textContent = "Weak";
            strengthText.className = "strength-text weak";
        } else if (strength <= 3) {
            strengthBar.className = "strength-bar fair";
            strengthText.textContent = "Fair";
            strengthText.className = "strength-text fair";
        } else {
            strengthBar.className = "strength-bar strong";
            strengthText.textContent = "Strong";
            strengthText.className = "strength-text strong";
        }
    }

    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = `message ${type}`;
        messageDiv.style.display = "block";
    }

    function hideMessage() {
        messageDiv.style.display = "none";
        messageDiv.textContent = "";
    }

    // Form submission
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (isSubmitting) return;

        const password = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Final validation
        if (password !== confirmPassword) {
            showMessage("Passwords do not match", "error");
            return;
        }

        if (password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/\d/.test(password)) {
            showMessage("Password does not meet requirements", "error");
            return;
        }

        isSubmitting = true;
        resetBtn.disabled = true;
        resetBtn.textContent = "Resetting Password...";
        hideMessage();

        try {
            const response = await fetch("../php/reset_password.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    reset_id: resetId,
                    email: email,
                    password: password,
                    confirm_password: confirmPassword
                })
            });

            const data = await response.json();

            if (data.success) {
                showMessage("✓ Password reset successfully! Redirecting to login...", "success");
                
                // Clear stored data
                sessionStorage.removeItem("reset_id");
                sessionStorage.removeItem("reset_email");
                sessionStorage.removeItem("reset_flow");

                setTimeout(() => {
                    window.location.href = "login.html";
                }, 2000);
            } else {
                showMessage(data.message || "Failed to reset password", "error");
                resetBtn.disabled = false;
                resetBtn.textContent = "Reset Password";
            }
        } catch (error) {
            console.error("Error:", error);
            showMessage("An error occurred. Please try again.", "error");
            resetBtn.disabled = false;
            resetBtn.textContent = "Reset Password";
        } finally {
            isSubmitting = false;
        }
    });

    // Initial validation check
    validatePassword();
});
