document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verifyOtpForm');
    const message = document.getElementById('message');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const btnText = document.getElementById('btnText');
    const loading = document.getElementById('loading');
    const timerDisplay = document.getElementById('timer');
    const emailDisplay = document.getElementById('emailDisplay');
    
    const otpInputs = document.querySelectorAll('.otp-input');
    
    // Get email from sessionStorage
    const email = sessionStorage.getItem('reset_email');
    
    if (!email) {
        // Redirect to forgot password if no email
        window.location.href = 'forgot_password.html';
        return;
    }
    
    // Display email
    emailDisplay.textContent = email;
    
    // Timer variables
    let timeLeft = 300; // 5 minutes in seconds
    let timerInterval;
    
    // Start countdown timer
    function startTimer() {
        timerInterval = setInterval(function() {
            timeLeft--;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = '00:00';
                verifyBtn.disabled = true;
                resendBtn.disabled = false;
                return;
            }
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }, 1000);
    }
    
    // OTP input handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            e.target.value = value.replace(/\D/g, '');
            
            // Move to next input
            if (value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
        
        // Paste handling
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/\D/g, '');
            
            otpInputs.forEach((inp, i) => {
                if (pasteData[i]) {
                    inp.value = pasteData[i];
                    if (i < otpInputs.length - 1) {
                        otpInputs[i + 1].focus();
                    }
                }
            });
        });
    });
    
    // Start timer
    startTimer();
    
    // Verify OTP form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        
        // Show loading state
        verifyBtn.disabled = true;
        btnText.style.display = 'none';
        loading.style.display = 'inline-block';
        hideMessage();
        
        try {
            const response = await fetch('../php/verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, otp, purpose: 'registration' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(data.message, 'success');
                
                // Redirect to new password page after short delay
                setTimeout(() => {
                    window.location.href = 'new_password.html';
                }, 1500);
            } else {
                showMessage(data.message, 'error');
                verifyBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('An error occurred. Please try again.', 'error');
            verifyBtn.disabled = false;
        } finally {
            btnText.style.display = 'inline';
            loading.style.display = 'none';
        }
    });
    
    // Resend OTP button
    resendBtn.addEventListener('click', async function() {
        resendBtn.disabled = true;
        resendBtn.textContent = 'Sending...';
        hideMessage();
        
        try {
            const response = await fetch('../php/resend_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Reset timer
                clearInterval(timerInterval);
                timeLeft = 300;
                timerDisplay.textContent = '05:00';
                verifyBtn.disabled = false;
                
                // Clear OTP inputs
                otpInputs.forEach(input => input.value = '');
                otpInputs[0].focus();
                
                // Start new timer
                startTimer();
                
                showMessage(data.message, 'success');
            } else {
                showMessage(data.message, 'error');
                resendBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Failed to resend OTP. Please try again.', 'error');
            resendBtn.disabled = false;
        } finally {
            resendBtn.textContent = 'Resend OTP';
        }
    });
    
    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'message ' + type;
        message.style.display = 'block';
    }
    
    function hideMessage() {
        message.style.display = 'none';
    }
});
