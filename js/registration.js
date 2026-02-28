// OTP Email Verification System - Client-Side Generation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize native date input with age calculation
    function initializeDateInput() {
        const dobInput = document.getElementById('dateOfBirth');
        const ageInput = document.getElementById('age');
        
        if (!dobInput || !ageInput) {
            console.error('Date of birth or age input not found');
            return;
        }

        // Set max date to today
        const today = new Date();
        const maxDate = today.toISOString().split('T')[0];
        dobInput.setAttribute('max', maxDate);

        // Add event listener for date change
        dobInput.addEventListener('change', function() {
            calculateAge(this.value, ageInput);
        });

        // Also listen to input event for real-time updates
        dobInput.addEventListener('input', function() {
            calculateAge(this.value, ageInput);
        });

        console.log('Native date input initialized successfully');
    }

    // Calculate age from date of birth
    function calculateAge(dateString, ageInput) {
        if (!dateString) {
            ageInput.value = '';
            return;
        }

        const birthDate = new Date(dateString);
        const today = new Date();

        // Validate date
        if (isNaN(birthDate.getTime())) {
            ageInput.value = '';
            return;
        }

        // Prevent future dates
        if (birthDate > today) {
            ageInput.value = '';
            return;
        }

        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        ageInput.value = age >= 0 ? age : '';
    }

    // Initialize date input
    initializeDateInput(); 

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
                
                // Focus on OTP input
                otpInput.focus();
                
                // Clear any previous OTP input
                otpInput.value = '';
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
                otpInput.value = '';
                otpInput.focus();
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify OTP';
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            showOtpStatus('Network error. Please try again.', 'danger');
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
                
                // Trigger validation for autofilled fields
                setTimeout(() => {
                    triggerFieldValidation('firstName');
                    triggerFieldValidation('lastName');
                    triggerFieldValidation('middleName');
                    validateForm();
                }, 100);
                
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

        // Check all required fields that are NOT disabled
        allInputs.forEach(function(input) {
            // Skip disabled fields from validation
            if (!input.disabled && input.required && !input.value.trim()) {
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

    // ========== VISUAL FIELD COMPLETION INDICATORS (GREEN BORDER ONLY) ==========
    const fieldsToMonitor = [
        { id: 'lastName', required: true },
        { id: 'firstName', required: true },
        { id: 'middleName', required: false },
        { id: 'dateOfBirth', required: true },
        { id: 'gender', required: true },
        { id: 'contactNumber', required: true },
        { id: 'school', required: true }
    ];

    // Setup field validation (green border only, no wrapper needed)
    fieldsToMonitor.forEach(fieldConfig => {
        const field = document.getElementById(fieldConfig.id);
        if (!field) return;
        
        // Add validation event listeners
        const validateField = () => {
            if (field.disabled) {
                // Don't show validation for disabled fields
                field.classList.remove('is-valid-filled');
                return;
            }

            let isValid = false;
            
            if (field.tagName === 'SELECT') {
                // For select elements, check if value is not empty and not the disabled option
                isValid = field.value && field.value !== '';
            } else {
                // For input elements
                const value = field.value.trim();
                isValid = value !== '';
                
                // Additional validation for specific fields
                if (fieldConfig.id === 'contactNumber' && value) {
                    // Philippine mobile numbers: should start with 09 and be 11 digits
                    const phonePattern = /^09\d{9}$/;
                    isValid = phonePattern.test(value);
                } else if (fieldConfig.id === 'dateOfBirth' && value) {
                    // Check if valid date format
                    const datePattern = /^\d{4}-\d{2}-\d{2}$/;
                    isValid = datePattern.test(value);
                }
            }
            
            // Toggle visual feedback (green border only)
            if (isValid) {
                field.classList.add('is-valid-filled');
            } else {
                field.classList.remove('is-valid-filled');
            }
        };
        
        // Listen to multiple events
        field.addEventListener('input', validateField);
        field.addEventListener('change', validateField);
        field.addEventListener('blur', validateField);
        
        // Store validation function for external triggering
        field._validateField = validateField;
        
        // For date input fields, validate on initialization
        if (fieldConfig.id === 'dateOfBirth') {
            // Check initial state after a brief delay
            setTimeout(validateField, 100);
        }
    });

    // Function to trigger validation for a specific field (used after autofill)
    window.triggerFieldValidation = function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field && field._validateField) {
            field._validateField();
        }
    };

    // Observer to monitor when fields are enabled/disabled
    const observeFieldChanges = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
                const field = mutation.target;
                if (field.disabled) {
                    field.classList.remove('is-valid-filled');
                } else {
                    // When field is enabled, validate it if it has a value
                    if (field._validateField) {
                        setTimeout(() => field._validateField(), 50);
                    }
                }
            }
        });
    });

    // Observe all monitored fields
    fieldsToMonitor.forEach(fieldConfig => {
        const field = document.getElementById(fieldConfig.id);
        if (field) {
            observeFieldChanges.observe(field, { attributes: true });
        }
    });

});
