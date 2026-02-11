// OTP Email Verification System
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for date of birth
    flatpickr('#dateOfBirth', {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        defaultDate: null,
        allowInput: true,
        placeholder: 'Select date of birth',
        onReady: function(selectedDates, dateStr, instance) {
            instance.input.setAttribute('data-date', dateStr);
        }
    });

    // Elements
    const emailInput = document.getElementById('email');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const otpContainer = document.getElementById('otpContainer');
    const otpInput = document.getElementById('otpInput');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpStatus = document.getElementById('otpStatus');
    const otpVerifiedBadge = document.getElementById('otpVerifiedBadge');
    const passwordSection = document.getElementById('passwordSection');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const registerBtn = document.getElementById('registerBtn');
    const passwordStrength = document.getElementById('passwordStrength');
    const registrationForm = document.getElementById('registrationForm');
    const allInputs = registrationForm.querySelectorAll('input:not(#otpInput)');

    // Constants
    const COOLDOWN_SECONDS = 300; // 5 minutes
    const N8N_WEBHOOK_URL = 'https://n8n.srv1069938.hstgr.cloud/webhook/otp-email';

    // State
    let cooldownTimer = null;
    let isCooldownActive = false;
    let isOtpVerified = false;
    let currentEmail = '';
    let currentOtp = '';

    // Show/hide Send OTP button based on email validation
    emailInput.addEventListener('input', function() {
        if (emailInput.validity.valid && !isOtpVerified) {
            sendOtpBtn.style.display = 'inline';
        } else {
            sendOtpBtn.style.display = 'none';
        }
        validateForm();
    });

    // Send OTP button click
    sendOtpBtn.addEventListener('click', function() {
        currentEmail = emailInput.value.trim();
        sendOtp(currentEmail);
    });

    // Resend OTP button click
    resendOtpBtn.addEventListener('click', function() {
        if (!isCooldownActive) {
            currentEmail = emailInput.value.trim();
            sendOtp(currentEmail);
        }
    });

    // Verify OTP button click
    verifyOtpBtn.addEventListener('click', function() {
        verifyOtp(currentEmail, otpInput.value.trim());
    });

    // OTP input - auto-validate form
    otpInput.addEventListener('input', function() {
        // Only allow numbers
        otpInput.value = otpInput.value.replace(/\D/g, '');
        validateForm();
    });

    // Password validation
    passwordInput.addEventListener('input', function() {
        validatePassword();
        validateForm();
    });

    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        validateForm();
    });

    // All other inputs - validate form
    allInputs.forEach(function(input) {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });

    // Form submission
    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!isOtpVerified) {
            showOtpStatus('Please verify your email with OTP first.', 'danger');
            return;
        }

        submitRegistration();
    });

    // Send OTP function
    function sendOtp(email) {
        // Generate 6-digit OTP
        currentOtp = generateOTP();

        // Debug logging - check browser console (F12)
        console.log('=== OTP DEBUG ===');
        console.log('Email:', email);
        console.log('Generated OTP:', currentOtp);
        console.log('=================');

        showOtpStatus('Sending OTP...', 'info');

        fetch(N8N_WEBHOOK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                otp_code: currentOtp
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('N8N Response:', data);
            if (data.success) {
                // Store OTP in sessionStorage for verification
                const storageKey = 'otp_' + email.toLowerCase().replace(/[^a-z0-9]/g, '');
                sessionStorage.setItem(storageKey, currentOtp);
                sessionStorage.setItem('otp_email', email.toLowerCase());

                showOtpStatus(data.message, 'success');
                
                // Show OTP input and verify button
                otpContainer.style.display = 'block';
                sendOtpBtn.style.display = 'none';
                
                // Start cooldown
                startCooldown();
                
                // Focus on OTP input
                otpInput.focus();
            } else {
                showOtpStatus(data.message || 'Failed to send OTP. Please try again.', 'danger');
                resetOtpState();
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            showOtpStatus('Network error. Please try again.', 'danger');
            resetOtpState();
        });
    }

    // Verify OTP function
    function verifyOtp(email, otp) {
        if (!otp || otp.length !== 6) {
            showOtpStatus('Please enter a valid 6-digit OTP.', 'danger');
            return;
        }

        console.log('=== VERIFY DEBUG ===');
        console.log('Email:', email);
        console.log('User entered:', otp);
        console.log('===================');

        showOtpStatus('Verifying OTP...', 'info');
        verifyOtpBtn.disabled = true;

        // Get stored OTP using normalized email key
        const storageKey = 'otp_' + email.toLowerCase().replace(/[^a-z0-9]/g, '');
        const storedOtp = sessionStorage.getItem(storageKey);

        console.log('Storage key:', storageKey);
        console.log('Stored OTP:', storedOtp);
        console.log('Match:', storedOtp === otp);

        // Verify OTP
        setTimeout(function() {
            if (storedOtp === otp) {
                // Success
                isOtpVerified = true;
                showOtpStatus('OTP verified successfully!', 'success');
                
                // Hide OTP section and show success
                otpContainer.style.display = 'none';
                sendOtpBtn.style.display = 'none';
                resendOtpBtn.style.display = 'none';
                otpVerifiedBadge.style.display = 'inline';
                
                // Stop cooldown timer
                if (cooldownTimer) {
                    clearInterval(cooldownTimer);
                }
                
                // Show password section
                passwordSection.style.display = 'block';
                
                // Focus on password input
                passwordInput.focus();
                
                // Validate form
                validateForm();
            } else {
                showOtpStatus('Invalid OTP. Please try again.', 'danger');
                console.log('Verification failed - OTPs do not match');
                otpInput.value = '';
                otpInput.focus();
            }
            verifyOtpBtn.disabled = false;
        }, 500);
    }

    // Start cooldown timer
    function startCooldown() {
        let remaining = COOLDOWN_SECONDS;
        isCooldownActive = true;
        
        updateCooldownText(remaining);
        resendOtpBtn.style.display = 'inline';
        resendOtpBtn.style.pointerEvents = 'none';
        resendOtpBtn.style.cursor = 'not-allowed';
        resendOtpBtn.classList.remove('text-primary');
        resendOtpBtn.classList.add('text-muted');

        cooldownTimer = setInterval(function() {
            remaining--;
            updateCooldownText(remaining);

            if (remaining <= 0) {
                clearInterval(cooldownTimer);
                isCooldownActive = false;
                resendOtpBtn.style.pointerEvents = 'auto';
                resendOtpBtn.style.cursor = 'pointer';
                resendOtpBtn.textContent = 'Resend OTP';
                resendOtpBtn.classList.remove('text-muted');
                resendOtpBtn.classList.add('text-primary');
            }
        }, 1000);
    }

    // Update cooldown text
    function updateCooldownText(seconds) {
        const min = String(Math.floor(seconds / 60)).padStart(2, '0');
        const sec = String(seconds % 60).padStart(2, '0');
        resendOtpBtn.textContent = 'Resend OTP (' + min + ':' + sec + ')';
    }

    // Reset OTP state
    function resetOtpState() {
        isCooldownActive = false;
        if (cooldownTimer) {
            clearInterval(cooldownTimer);
        }
        resendOtpBtn.style.display = 'none';
    }

    // Show OTP status message
    function showOtpStatus(message, type) {
        otpStatus.textContent = message;
        otpStatus.className = 'mb-2 mt-1 small fw-semibold text-' + type;
        otpStatus.style.display = 'block';
    }

    // Generate random 6-digit OTP
    function generateOTP() {
        let otp = '';
        for (let i = 0; i < 6; i++) {
            otp += Math.floor(Math.random() * 10);
        }
        return otp;
    }

    // Validate password strength
    function validatePassword() {
        const password = passwordInput.value;
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) {
            strength++;
        } else {
            feedback.push('at least 8 characters');
        }

        if (/[A-Z]/.test(password)) {
            strength++;
        } else {
            feedback.push('an uppercase letter');
        }

        if (/[a-z]/.test(password)) {
            strength++;
        } else {
            feedback.push('a lowercase letter');
        }

        if (/[0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('a number');
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('a special character');
        }

        // Update strength indicator
        let strengthClass = '';
        let strengthText = '';
        
        switch (strength) {
            case 0:
            case 1:
                strengthClass = 'bg-danger';
                strengthText = 'Very Weak';
                break;
            case 2:
                strengthClass = 'bg-warning';
                strengthText = 'Weak';
                break;
            case 3:
                strengthClass = 'bg-info';
                strengthText = 'Medium';
                break;
            case 4:
                strengthClass = 'bg-primary';
                strengthText = 'Strong';
                break;
            case 5:
                strengthClass = 'bg-success';
                strengthText = 'Very Strong';
                break;
        }

        passwordStrength.innerHTML = '<div class="progress mt-1" style="height: 5px;"><div class="progress-bar ' + strengthClass + '" role="progressbar" style="width: ' + (strength * 20) + '%"></div></div><small class="text-muted">' + strengthText + '</small>';
        
        if (feedback.length > 0 && password.length > 0) {
            passwordStrength.innerHTML += '<small class="text-muted d-block">Add ' + feedback.join(', ') + '</small>';
        }
    }

    // Validate entire form
    function validateForm() {
        const form = registrationForm;
        let isValid = true;

        // Check all required fields
        allInputs.forEach(function(input) {
            if (input.required && !input.value.trim()) {
                isValid = false;
            }
        });

        // Check email validity
        if (!emailInput.validity.valid) {
            isValid = false;
        }

        // Check OTP verification
        if (!isOtpVerified) {
            isValid = false;
        }

        // Check password
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        let passwordValid = false;

        if (password.length >= 8 && 
            /[A-Z]/.test(password) && 
            /[a-z]/.test(password) && 
            /[0-9]/.test(password)) {
            passwordValid = true;
        }

        // Check password match
        const passwordsMatch = password === confirmPassword && password.length > 0;

        // Enable/disable register button
        registerBtn.disabled = !(isValid && passwordValid && passwordsMatch);
    }

    // Submit registration
    function submitRegistration() {
        const formData = new FormData(registrationForm);
        formData.append('otp_verified', 'true');

        fetch('php/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear session storage
                sessionStorage.removeItem('otp_' + currentEmail.toLowerCase().replace(/[^a-z0-9]/g, ''));
                sessionStorage.removeItem('otp_email');
                
                // Show success and redirect
                showOtpStatus('Registration successful! Redirecting to login...', 'success');
                setTimeout(function() {
                    window.location.href = 'login.html?registered=1';
                }, 1500);
            } else {
                showOtpStatus(data.message || 'Registration failed. Please try again.', 'danger');
            }
        })
        .catch(error => {
            console.error('Registration error:', error);
            showOtpStatus('Network error. Please try again.', 'danger');
        });
    }
});
