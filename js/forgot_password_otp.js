document.addEventListener("DOMContentLoaded", () => {
    const email = sessionStorage.getItem("reset_email");
    const resetFlow = sessionStorage.getItem("reset_flow");

    // Check if we're in the password reset flow
    if (!resetFlow || !email) {
        window.location.href = "login.html";
        return;
    }

    const emailDisplay = document.getElementById("emailDisplay");
    const form = document.getElementById("verifyOtpPasswordForm");
    const verifyBtn = document.getElementById("verifyBtn");
    const resendBtn = document.getElementById("resendBtn");
    const messageDiv = document.getElementById("message");
    const timerDisplay = document.getElementById("timerDisplay");
    const otpInputs = document.querySelectorAll(".otp-input");
    const attemptsInfo = document.getElementById("attemptsInfo");

    let timerInterval;
    let timeLeft = 600; // 10 minutes in seconds
    let isVerifying = false;

    // Display email
    emailDisplay.textContent = `Verification code sent to ${email}`;

    // OTP Auto-advance and validation
    otpInputs.forEach((input, index) => {
        input.addEventListener("keyup", (e) => {
            const value = e.target.value;

            // Only allow digits
            if (!/^\d$/.test(value) && value !== "") {
                input.value = "";
                return;
            }

            // Move to next input on digit entry
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Move to previous input on backspace
            if (e.key === "Backspace" && !value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener("keypress", (e) => {
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener("paste", (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, 6);
            pasteData.split("").forEach((char, i) => {
                if (i < otpInputs.length) {
                    otpInputs[i].value = char;
                }
            });
            otpInputs[Math.min(pasteData.length, otpInputs.length - 1)].focus();
        });
    });

    // Timer countdown
    function startTimer() {
        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                verifyBtn.disabled = true;
                otpInputs.forEach(input => input.disabled = true);
                showMessage("OTP has expired. Please request a new one.", "error");
                resendBtn.disabled = false;
            } else if (timeLeft <= 60) {
                timerDisplay.classList.add("warning");
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
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

    // Verify OTP
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (isVerifying) return;

        const otp = Array.from(otpInputs).map(input => input.value).join("");

        if (otp.length !== 6) {
            showMessage("Please enter all 6 digits", "error");
            return;
        }

        isVerifying = true;
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<span class="spinner-inline"></span>Verifying...';
        hideMessage();

        try {
            const response = await fetch("../php/verify_otp_password.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: email,
                    otp: otp
                })
            });

            const data = await response.json();

            if (data.success) {
                // Store reset data for next step
                sessionStorage.setItem("reset_id", data.data.reset_id);
                showMessage("OTP verified successfully! Redirecting...", "success");
                
                setTimeout(() => {
                    window.location.href = "reset_password.html";
                }, 1500);
            } else {
                showMessage(data.message || "OTP verification failed", "error");
                
                // Reset inputs on error
                otpInputs.forEach(input => input.value = "");
                otpInputs[0].focus();

                // Update attempts info if provided
                if (data.attempts_left !== undefined) {
                    attemptsInfo.textContent = `${data.attempts_left} attempts remaining`;
                }

                // Reset input error state
                otpInputs.forEach(input => input.classList.remove("error"));
            }
        } catch (error) {
            console.error("Error:", error);
            showMessage("An error occurred. Please try again.", "error");
            otpInputs.forEach(input => input.classList.remove("error"));
        } finally {
            isVerifying = false;
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<span id="btnText">Verify OTP</span>';
        }
    });

    // Resend OTP
    resendBtn.addEventListener("click", async () => {
        resendBtn.disabled = true;
        resendBtn.textContent = "Sending...";

        try {
            const response = await fetch("../php/forgot_password.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: email
                })
            });

            const data = await response.json();

            if (data.success) {
                // Reset timer and inputs
                timeLeft = 600;
                clearInterval(timerInterval);
                updateTimerDisplay();
                startTimer();

                otpInputs.forEach(input => {
                    input.value = "";
                    input.disabled = false;
                    input.classList.remove("error");
                });

                verifyBtn.disabled = false;
                resendBtn.disabled = true;
                attemptsInfo.textContent = "";
                timerDisplay.classList.remove("warning");

                showMessage("New OTP sent to your email!", "success");
                setTimeout(() => hideMessage(), 3000);

                otpInputs[0].focus();
            } else {
                showMessage(data.message || "Failed to resend OTP", "error");
                resendBtn.disabled = false;
                resendBtn.textContent = "Resend Code";
            }
        } catch (error) {
            console.error("Error:", error);
            showMessage("An error occurred. Please try again.", "error");
            resendBtn.disabled = false;
            resendBtn.textContent = "Resend Code";
        }
    });

    // Start timer when page loads
    startTimer();
    updateTimerDisplay();
    otpInputs[0].focus();

    // Resend button becomes available after 30 seconds
    setTimeout(() => {
        resendBtn.disabled = false;
    }, 30000);
});
