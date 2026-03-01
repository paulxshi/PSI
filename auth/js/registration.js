// OTP Email Verification System - Client-Side Generation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for date of birth
    function initFlatpickrDOB() {
        const dobEl = document.getElementById('dateOfBirth');
        if (!dobEl) return;
        // Destroy any existing flatpickr instance to avoid duplicates
        if (dobEl._flatpickr) dobEl._flatpickr.destroy();
        flatpickr(dobEl, {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            defaultDate: null,
            allowInput: true,
            placeholder: 'Select date of birth',
            onReady: function(selectedDates, dateStr, instance) {
                instance.input.setAttribute('data-date', dateStr);
            },
            onChange: function(selectedDates) {
                const ageInput = document.getElementById('age');
                if (!selectedDates.length) {
                    if (ageInput) ageInput.value = '';
                    validateForm();
                    return;
                }
                const birthDate = selectedDates[0];
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                if (ageInput) ageInput.value = age >= 0 ? age : '';
                validateForm();
            }
        });
    }
    // Initial init (will be re-initialized after field is enabled)
    initFlatpickrDOB(); 

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
    const allSelects = registrationForm.querySelectorAll('select');
    const invalidTestPermitModal = new bootstrap.Modal(document.getElementById('invalidTestPermitModal'), {
        keyboard: false,
        backdrop: 'static'
    });
    
    // Test Permit Elements
    const testPermitInput = document.getElementById('testPermitNo');
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');
    const verifyTestPermitBtn = document.getElementById('verifyTestPermitBtn');

    // Constants
    const COOLDOWN_SECONDS = 60; // 60 seconds between OTP requests
    const SEND_OTP_URL = 'php/send_otp.php';
    const CHECK_TEST_PERMIT_URL = 'php/check_test_permit.php';

    // State
    let cooldownTimer = null;
    let isCooldownActive = false;
    let isOtpVerified = false;
    let currentEmail = '';
    let testPermitVerified = false;
    let examineeData = null;

    // Show/hide Send OTP button based on email validation and form state
    emailInput.addEventListener('input', function() {
        // Only show OTP button if:
        // 1. Email is not disabled (test permit verified)
        // 2. Email is valid
        // 3. OTP not yet verified
        if (!emailInput.disabled && emailInput.validity.valid && !isOtpVerified) {
            sendOtpBtn.style.display = 'inline';
        } else if (isOtpVerified) {
            // Keep button visible but disabled after OTP verified
            sendOtpBtn.style.display = 'inline';
        } else {
            sendOtpBtn.style.display = 'none';
        }
        validateForm();
    });

    // Test Permit validation and auto-fill
    if (testPermitInput) {
        testPermitInput.addEventListener('blur', function() {
            const testPermit = testPermitInput.value.trim();
            if (testPermit.length > 0) {
                checkTestPermit(testPermit);
            }
        });

        // Allow Enter key to verify test permit
        testPermitInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const testPermit = testPermitInput.value.trim();
                if (testPermit.length > 0) {
                    checkTestPermit(testPermit);
                }
            }
        });
    }

    // Verify Test Permit button click
    if (verifyTestPermitBtn) {
        verifyTestPermitBtn.addEventListener('click', function() {
            const testPermit = testPermitInput.value.trim();
            if (testPermit.length > 0) {
                checkTestPermit(testPermit);
            } else {
                showTestPermitStatus('Please enter a Test Permit Number', 'danger');
            }
        });
    }

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
    
    // OTP input - explicit keyboard handling for backspace/delete
    otpInput.addEventListener('keydown', function(e) {
        // Ensure backspace and delete always work
        if (e.key === 'Backspace' || e.key === 'Delete') {
            // Allow default behavior - these keys should always work
            return true;
        }
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

    // Fallback age calculation via native events (covers mobile/native pickers)
    const dateOfBirthInput = document.getElementById('dateOfBirth');
    if (dateOfBirthInput) {
        function calculateAndSetAge() {
            const ageInput = document.getElementById('age');
            const dobValue = dateOfBirthInput.value.trim();
            if (!ageInput) return;
            if (!dobValue) { ageInput.value = ''; return; }

            const birthDate = new Date(dobValue);
            if (isNaN(birthDate.getTime())) return;

            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age : '';
            validateForm();
        }

        dateOfBirthInput.addEventListener('change', calculateAndSetAge);
        dateOfBirthInput.addEventListener('input', calculateAndSetAge);
        dateOfBirthInput.addEventListener('blur', calculateAndSetAge);
    }

    // All other inputs - validate form
    allInputs.forEach(function(input) {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });

    // All selects - validate form (especially for mobile)
    allSelects.forEach(function(select) {
        select.addEventListener('input', validateForm);
        select.addEventListener('change', validateForm);
    });

    // Form submission - handled by submitRegistration() in HTML
    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Form is submitted via submitRegistration() button onclick
    });

    // Generate OTP - generates 6-digit code client-side
    function generateOtp() {
        return String(Math.floor(100000 + Math.random() * 900000));
    }

    // Send OTP function - backend generates OTP and sends email
    function sendOtp(email) {
        // Extra safety: trim email
        email = String(email).trim();
        
        showOtpStatus('Sending OTP...', 'info');
        sendOtpBtn.disabled = true;

        // Send request to backend to generate and send OTP
        fetch(SEND_OTP_URL, {
            method: 'POST',
            headers:  {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                purpose: 'registration'
            }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Send OTP Response:', data);
            sendOtpBtn.disabled = false;
            
            if (data.success) {
                showOtpStatus('OTP sent! Check your email. Spam folder too.', 'success');
                
                // Show OTP input and verify button
                otpContainer.style.display = 'block';
                sendOtpBtn.style.display = 'none';
                
                // Start cooldown
                startCooldown();
                
                // Ensure OTP input is enabled and ready for input
                otpInput.disabled = false;
                otpInput.readOnly = false;
                otpInput.value = '';
                
                // Focus on OTP input
                otpInput.focus();
            } else {
                showOtpStatus(data.message || 'Failed to send OTP. Please try again.', 'danger');
                resetOtpState();
                sendOtpBtn.disabled = false;
            }
        })
        .catch(error => {
            sendOtpBtn.disabled = false;
            console.error('Error sending OTP:', error);
            showOtpStatus('Network error. Please try again.', 'danger');
            resetOtpState();
        });
    }

    // Verify OTP function - server-side verification
    function verifyOtp(email, otp) {
        // Extra safety: trim inputs
        email = String(email).trim();
        otp = String(otp).trim();
        
        console.log('Verify OTP called - Email:', email, 'Entered OTP:', otp);
        
        if (!otp || otp.length !== 6) {
            showOtpStatus('Please enter a valid 6-digit OTP.', 'danger');
            return;
        }

        // Disable verify button while processing
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying...';
        showOtpStatus('Verifying OTP...', 'info');

        // Call backend to verify OTP
        fetch('php/verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                otp: otp,
                purpose: 'registration'
            }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Verify OTP Response:', data);
            
            if (data.success) {
                // OTP verified successfully on server
                isOtpVerified = true;
                
                // Clear sessionStorage
                sessionStorage.removeItem('reg_otp');
                sessionStorage.removeItem('reg_email');
                
                showOtpStatus('OTP verified successfully!', 'success');
                
                // Hide OTP section and show success
                otpContainer.style.display = 'none';
                resendOtpBtn.style.display = 'none';
                
                // Disable OTP button but keep it visible
                sendOtpBtn.disabled = true;
                sendOtpBtn.style.opacity = '0.6';
                sendOtpBtn.style.cursor = 'not-allowed';
                
                // Show OTP verified badge
                otpVerifiedBadge.style.display = 'inline';
                
                // Stop cooldown timer
                if (cooldownTimer) {
                    clearInterval(cooldownTimer);
                }
                
                // Lock email field after verification to prevent changes
                emailInput.readOnly = true;
                emailInput.classList.add('bg-light');
                emailInput.title = 'Email locked after verification';

                // Show password section
                passwordSection.style.display = 'block';
                const registerBtnWrapper = document.getElementById('registerBtnWrapper');
                if (registerBtnWrapper) registerBtnWrapper.classList.remove('d-none');
                
                // ENABLE password fields
                passwordInput.disabled = false;
                confirmPasswordInput.disabled = false;
                
                // Focus on password input
                passwordInput.focus();
                
                // Validate form
                validateForm();
            } else {
                // OTP verification failed
                console.log('OTP verification failed:', data.message);
                showOtpStatus(data.message || 'Invalid OTP. Please try again.', 'danger');
                
                // Ensure OTP input remains editable and clear it
                otpInput.disabled = false;
                otpInput.readOnly = false;
                otpInput.value = '';
                
                // Trigger input event to clear visual boxes
                otpInput.dispatchEvent(new Event('input', { bubbles: true }));
                
                // Refocus and re-enable verify button
                otpInput.focus();
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify OTP';
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            showOtpStatus('Network error. Please try again.', 'danger');
            
            // Ensure OTP input remains editable after error
            otpInput.disabled = false;
            otpInput.readOnly = false;
            otpInput.value = '';
            
            // Trigger input event to clear visual boxes
            otpInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            // Refocus and re-enable verify button
            otpInput.focus();
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'Verify OTP';
        });
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

    // Check Test Permit in examinee_masterlist
    function checkTestPermit(testPermit) {
        const formData = new FormData();
        formData.append('test_permit', testPermit);

        fetch(CHECK_TEST_PERMIT_URL, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Check Test Permit Response:', data);
            
            if (data.success) {
                // Auto-fill examinee data
                examineeData = data.data;
                testPermitVerified = true;

                // Auto-fill fields with data from masterlist (no parsing needed)
                lastNameInput.value = examineeData.last_name || '';
                firstNameInput.value = examineeData.first_name || '';
                document.getElementById('middleName').value = examineeData.middle_name || '';
                emailInput.value = examineeData.email;

                // ENABLE all personal info fields (NOT readonly - user can edit)
                document.getElementById('firstName').disabled = false;
                document.getElementById('lastName').disabled = false;
                document.getElementById('middleName').disabled = false;
                document.getElementById('dateOfBirth').disabled = false;
                document.getElementById('age').disabled = false;
                // Re-initialize flatpickr now that the field is enabled
                initFlatpickrDOB();
                document.getElementById('gender').disabled = false;
                document.getElementById('contactNumber').disabled = false;
                document.getElementById('school').disabled = false;
                
                // Make email read-only (locked after test permit verification)
                emailInput.disabled = false;
                emailInput.readOnly = true;
                emailInput.classList.add('bg-light');
                emailInput.style.cursor = 'not-allowed';
                emailInput.title = 'Email is locked after test permit verification';

                // Show OTP button (keep it visible)
                sendOtpBtn.style.display = 'inline';
                showTestPermitStatus('Test permit verified! Please complete remaining fields.', 'success');
                
                validateForm();
            } else {
                testPermitVerified = false;
                examineeData = null;
                
                // DISABLE all personal info fields
                document.getElementById('firstName').disabled = true;
                document.getElementById('lastName').disabled = true;
                document.getElementById('middleName').disabled = true;
                document.getElementById('dateOfBirth').disabled = true;
                document.getElementById('age').disabled = true;
                document.getElementById('gender').disabled = true;
                document.getElementById('contactNumber').disabled = true;
                document.getElementById('school').disabled = true;
                document.getElementById('email').disabled = true;

                // Clear fields
                lastNameInput.value = '';
                firstNameInput.value = '';
                document.getElementById('middleName').value = '';
                emailInput.value = '';

                // Hide OTP button
                sendOtpBtn.style.display = 'none';
                
                // Show modal
                invalidTestPermitModal.show();
                
                validateForm();
            }
        })
        .catch(error => {
            console.error('Error checking test permit:', error);
            testPermitVerified = false;
            examineeData = null;

            // DISABLE all personal info fields
            document.getElementById('firstName').disabled = true;
            document.getElementById('lastName').disabled = true;
            document.getElementById('middleName').disabled = true;
            document.getElementById('dateOfBirth').disabled = true;
            document.getElementById('age').disabled = true;
            document.getElementById('gender').disabled = true;
            document.getElementById('contactNumber').disabled = true;
            document.getElementById('school').disabled = true;
            document.getElementById('email').disabled = true;

            // Clear fields
            lastNameInput.value = '';
            firstNameInput.value = '';
            emailInput.value = '';

            // Hide OTP button
            sendOtpBtn.style.display = 'none';
            
            // Show modal
            invalidTestPermitModal.show();
            
            validateForm();
        });
    }

    // Show test permit status message
    function showTestPermitStatus(message, type) {
        // Create or get status element
        let statusEl = document.getElementById('testPermitStatus');
        
        if (!statusEl) {
            statusEl = document.createElement('small');
            statusEl.id = 'testPermitStatus';
            statusEl.style.display = 'block';
            statusEl.style.marginTop = '6px';
            testPermitInput.parentElement.appendChild(statusEl);
        }

        statusEl.textContent = message;
        statusEl.className = 'small fw-semibold text-' + type;
    }

    // Validate entire form
    function validateForm() {
        const form = registrationForm;
        let isValid = true;

        // Check all required input fields that are NOT disabled
        allInputs.forEach(function(input) {
            // Skip disabled fields from validation
            if (!input.disabled && input.required && !input.value.trim()) {
                isValid = false;
            }
        });

        // Check all required select fields that are NOT disabled
        allSelects.forEach(function(select) {
            if (!select.disabled && select.required && !select.value) {
                isValid = false;
            }
        });

        // Check email validity (if enabled)
        if (!emailInput.disabled && !emailInput.validity.valid) {
            isValid = false;
        }

        // Check OTP verification
        if (!isOtpVerified) {
            isValid = false;
        }

        // Check password (only if password section is shown)
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

        // Enable/disable register button - only if passwords are being shown and filled
        if (passwordSection.style.display !== 'none') {
            registerBtn.disabled = !(isValid && passwordValid && passwordsMatch);
        } else {
            registerBtn.disabled = true;
        }
    }


});
