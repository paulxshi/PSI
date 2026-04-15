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
                    updateSendOtpButtonState();
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
                updateSendOtpButtonState();
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

    // Required field elements for OTP validation (dateOfBirth is declared later)
    const genderInput = document.getElementById('gender');
    const contactNumberInput = document.getElementById('contactNumber');
    const schoolInput = document.getElementById('school');

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

    // Helper function to check if all required fields are filled
    function areRequiredFieldsFilled() {
        const dateOfBirthEl = document.getElementById('dateOfBirth');
        const dateOfBirthFilled = dateOfBirthEl && dateOfBirthEl.value.trim() !== '';
        const genderFilled = genderInput && genderInput.value !== '' && genderInput.value !== 'Select Gender';
        const contactNumberFilled = contactNumberInput && contactNumberInput.value.trim() !== '';
        const schoolFilled = schoolInput && schoolInput.value.trim() !== '';
        
        return dateOfBirthFilled && genderFilled && contactNumberFilled && schoolFilled;
    }

    // Function to update Send OTP button state
    function updateSendOtpButtonState() {
        const emailValid = emailInput && !emailInput.disabled && emailInput.validity.valid;
        const requiredFieldsFilled = areRequiredFieldsFilled();
        
        if (emailValid && requiredFieldsFilled && !isOtpVerified) {
            sendOtpBtn.style.display = 'inline';
            sendOtpBtn.disabled = false;
        } else if (isOtpVerified) {
            // Keep button visible but disabled after OTP verified
            sendOtpBtn.style.display = 'inline';
            sendOtpBtn.disabled = true;
        } else {
            if (emailValid && !isOtpVerified) {
                // Show button but keep it disabled if required fields are not filled
                sendOtpBtn.style.display = 'inline';
                sendOtpBtn.disabled = true;
            } else {
                sendOtpBtn.style.display = 'none';
            }
        }
    }

    // Show/hide Send OTP button based on email validation and form state
    emailInput.addEventListener('input', function() {
        // If the user edits the email after OTP was already verified, reset verification
        // so they must re-verify the new address before proceeding.
        if (isOtpVerified && emailInput.value.trim() !== currentEmail) {
            isOtpVerified = false;
            currentEmail = '';

            otpVerifiedBadge.style.display = 'none';

            sendOtpBtn.disabled = false;
            sendOtpBtn.style.opacity = '1';
            sendOtpBtn.style.cursor = '';
            sendOtpBtn.style.display = 'inline';

            otpContainer.style.display = 'none';
            otpInput.value = '';
            otpInput.dispatchEvent(new Event('input', { bubbles: true }));

            passwordSection.style.display = 'none';
            passwordInput.disabled = true;
            confirmPasswordInput.disabled = true;
            passwordInput.value = '';
            confirmPasswordInput.value = '';

            showOtpStatus('Email changed — please verify your new email address.', 'warning');
        }
        updateSendOtpButtonState();
        validateForm();
    });

    // Add event listeners to required fields for OTP validation
    // Note: dateOfBirth listeners are added later where dateOfBirthInput is declared
    
    if (genderInput) {
        genderInput.addEventListener('change', updateSendOtpButtonState);
    }
    
    if (contactNumberInput) {
        contactNumberInput.addEventListener('input', updateSendOtpButtonState);
        contactNumberInput.addEventListener('change', updateSendOtpButtonState);
    }
    
    if (schoolInput) {
        schoolInput.addEventListener('input', updateSendOtpButtonState);
        schoolInput.addEventListener('change', updateSendOtpButtonState);
    }

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
        try { validatePassword(); } catch(e) { console.warn('Password strength display skipped:', e); }
        validateForm();
    });

    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        validateForm();
    });

    // Mobile-friendly: also listen for blur/change on password fields
    passwordInput.addEventListener('blur', function() {
        try { validatePassword(); } catch(e) {}
        validateForm();
    });
    passwordInput.addEventListener('change', function() {
        try { validatePassword(); } catch(e) {}
        validateForm();
    });
    confirmPasswordInput.addEventListener('blur', validateForm);
    confirmPasswordInput.addEventListener('change', validateForm);

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
            updateSendOtpButtonState();
            validateForm();
        }

        dateOfBirthInput.addEventListener('change', calculateAndSetAge);
        dateOfBirthInput.addEventListener('input', calculateAndSetAge);
        dateOfBirthInput.addEventListener('blur', calculateAndSetAge);
    }

    // All other inputs - validate form (input, change, AND blur for mobile)
    allInputs.forEach(function(input) {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
        input.addEventListener('blur', validateForm);
    });

    // All selects - validate form (especially for mobile)
    allSelects.forEach(function(select) {
        select.addEventListener('input', validateForm);
        select.addEventListener('change', validateForm);
        select.addEventListener('blur', validateForm);
    });

    // Mobile fallback: periodically check form validity when password section is visible
    // This catches edge cases where mobile browsers don't fire events reliably
    let mobileValidationInterval = null;
    function startMobileValidation() {
        if (mobileValidationInterval) return;
        mobileValidationInterval = setInterval(function() {
            if (passwordSection.style.display !== 'none') {
                validateForm();
            }
        }, 1000);
    }
    // Start periodic validation once password section is shown
    const passwordSectionObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'style' && passwordSection.style.display !== 'none') {
                startMobileValidation();
            }
        });
    });
    passwordSectionObserver.observe(passwordSection, { attributes: true, attributeFilter: ['style'] });

    // Form submission — registerBtn click handler
    registerBtn.addEventListener('click', function() {
        submitRegistration();
    });

    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
    });

    // Submit registration function
    function submitRegistration() {
        var formData = new FormData();

        // Add all form fields
        formData.append('test_permit', document.getElementById('testPermitNo').value.trim());
        formData.append('last_name', document.getElementById('lastName').value.trim());
        formData.append('first_name', document.getElementById('firstName').value.trim());
        formData.append('middle_name', document.getElementById('middleName').value.trim());
        formData.append('email', document.getElementById('email').value.trim());
        formData.append('date_of_birth', document.getElementById('dateOfBirth').value.trim());
        formData.append('contact_number', document.getElementById('contactNumber').value.trim());
        formData.append('gender', document.getElementById('gender').value);
        formData.append('school', document.getElementById('school').value.trim());
        var addressEl = document.getElementById('address');
        formData.append('address', addressEl ? addressEl.value.trim() : '');
        var nationalityEl = document.getElementById('nationality');
        formData.append('nationality', nationalityEl ? nationalityEl.value.trim() : '');
        formData.append('password', document.getElementById('password').value);
        formData.append('confirm_password', document.getElementById('confirmPassword').value);

        // Disable button and show loading state
        registerBtn.disabled = true;
        registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        fetch('php/register.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            console.log('Registration Response:', data);

            if (data.success) {
                // Show success modal
                var successModal = new bootstrap.Modal(document.getElementById('registrationSuccessModal'));
                successModal.show();

                // Redirect to exam schedule after short delay
                setTimeout(function() {
                    window.location.href = 'examsched.html';
                }, 1500);
            } else {
                // Show error modal
                document.getElementById('registrationErrorMessage').textContent = data.message || 'Registration failed. Please try again.';
                var errorModal = new bootstrap.Modal(document.getElementById('registrationErrorModal'));
                errorModal.show();

                registerBtn.disabled = false;
                registerBtn.innerHTML = 'Proceed to Examination Schedule <i class="bi bi-arrow-right-short"></i>';
            }
        })
        .catch(function(error) {
            console.error('Registration Error:', error);

            // Show error modal
            document.getElementById('registrationErrorMessage').textContent = 'Network error. Please try again.';
            var errorModal = new bootstrap.Modal(document.getElementById('registrationErrorModal'));
            errorModal.show();

            registerBtn.disabled = false;
            registerBtn.innerHTML = 'Proceed to Examination Schedule <i class="bi bi-arrow-right-short"></i>';
        });
    }

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

        if (passwordStrength) {
            passwordStrength.innerHTML = '<div class="progress mt-1" style="height: 5px;"><div class="progress-bar ' + strengthClass + '" role="progressbar" style="width: ' + (strength * 20) + '%"></div></div><small class="text-muted">' + strengthText + '</small>';
            
            if (feedback.length > 0 && password.length > 0) {
                passwordStrength.innerHTML += '<small class="text-muted d-block">Add ' + feedback.join(', ') + '</small>';
            }
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
                // Only pre-fill email if the masterlist record has one
                emailInput.value = examineeData.email || '';

                // Name fields are enabled for form submission but locked from editing
                document.getElementById('firstName').disabled = false;
                document.getElementById('firstName').readOnly = true;
                document.getElementById('firstName').classList.add('name-locked');
                document.getElementById('lastName').disabled = false;
                document.getElementById('lastName').readOnly = true;
                document.getElementById('lastName').classList.add('name-locked');
                document.getElementById('middleName').disabled = false;
                document.getElementById('middleName').readOnly = true;
                document.getElementById('middleName').classList.add('name-locked');
                document.getElementById('dateOfBirth').disabled = false;
                document.getElementById('age').disabled = false;
                // Re-initialize flatpickr now that the field is enabled
                initFlatpickrDOB();
                document.getElementById('gender').disabled = false;
                document.getElementById('contactNumber').disabled = false;
                document.getElementById('school').disabled = false;
                
                // Enable email field
                emailInput.disabled = false;

                // Update OTP button state based on all required fields
                updateSendOtpButtonState();
                showTestPermitStatus('Test permit verified! Please complete remaining fields.', 'success');
                
                validateForm();
            } else {
                testPermitVerified = false;
                examineeData = null;
                
                // DISABLE all personal info fields
                ['firstName', 'lastName', 'middleName'].forEach(function(id) {
                    var el = document.getElementById(id);
                    el.disabled = true;
                    el.readOnly = false;
                    el.classList.remove('name-locked');
                });
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

                // Update OTP button state
                updateSendOtpButtonState();
                
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
            ['firstName', 'lastName', 'middleName'].forEach(function(id) {
                var el = document.getElementById(id);
                el.disabled = true;
                el.readOnly = false;
                el.classList.remove('name-locked');
            });
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

            // Update OTP button state
            updateSendOtpButtonState();
            
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

    // Validate entire form — checks specific fields by ID for mobile reliability
    function validateForm() {
        let isValid = true;

        // Check specific required text/tel fields by ID
        var requiredFields = [
            'testPermitNo', 'lastName', 'firstName',
            'dateOfBirth', 'contactNumber', 'school'
        ];

        for (var i = 0; i < requiredFields.length; i++) {
            var el = document.getElementById(requiredFields[i]);
            if (el && !el.disabled && !el.value.trim()) {
                isValid = false;
                console.log('validateForm FAIL:', requiredFields[i], 'is empty');
            }
        }

        // Check gender select
        var genderEl = document.getElementById('gender');
        if (genderEl && !genderEl.disabled && !genderEl.value) {
            isValid = false;
            console.log('validateForm FAIL: gender not selected');
        }

        // Check email with regex (validity API unreliable on mobile with readOnly)
        if (!emailInput.disabled) {
            var emailVal = emailInput.value.trim();
            if (!emailVal || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                isValid = false;
                console.log('validateForm FAIL: email invalid or empty');
            }
        }

        // Check OTP verification
        if (!isOtpVerified) {
            isValid = false;
            console.log('validateForm FAIL: OTP not verified');
        }

        // Check password (only if password section is visible)
        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;
        var passwordValid = false;

        if (password.length >= 8 && 
            /[A-Z]/.test(password) && 
            /[a-z]/.test(password) && 
            /[0-9]/.test(password)) {
            passwordValid = true;
        }

        // Check password match
        var passwordsMatch = password === confirmPassword && password.length > 0;

        // Enable/disable register button
        if (passwordSection.style.display !== 'none') {
            var shouldEnable = isValid && passwordValid && passwordsMatch;
            registerBtn.disabled = !shouldEnable;
            if (!shouldEnable) {
                console.log('validateForm: Button DISABLED — isValid:', isValid,
                    'passwordValid:', passwordValid, 'passwordsMatch:', passwordsMatch);
            } else {
                console.log('validateForm: Button ENABLED');
            }
        } else {
            registerBtn.disabled = true;
        }
    }


});
