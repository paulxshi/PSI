document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newPasswordForm');
    const message = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const loading = document.getElementById('loading');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Get stored data from sessionStorage
    const email = sessionStorage.getItem('reset_email');
    const otp = sessionStorage.getItem('reset_otp');
    
    if (!email || !otp) {
        // Redirect to forgot password if no email/OTP
        window.location.href = 'forgot_password.html';
        return;
    }
    
    // Password validation
    function validatePassword(password) {
        const requirements = [];
        
        if (password.length < 8) {
            requirements.push('at least 8 characters');
        }
        if (!/[A-Z]/.test(password)) {
            requirements.push('one uppercase letter');
        }
        if (!/[a-z]/.test(password)) {
            requirements.push('one lowercase letter');
        }
        if (!/[0-9]/.test(password)) {
            requirements.push('one number');
        }
        
        return requirements;
    }
    
    // Real-time password validation
    newPassword.addEventListener('input', function() {
        const requirements = validatePassword(this.value);
        
        if (requirements.length > 0) {
            this.setCustomValidity('Password must contain ' + requirements.join(', '));
        } else {
            this.setCustomValidity('');
        }
        
        // Check if passwords match
        if (confirmPassword.value && this.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
    
    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = newPassword.value;
        const passwordConfirm = confirmPassword.value;
        
        // Validate passwords match
        if (password !== passwordConfirm) {
            showMessage('Passwords do not match', 'error');
            return;
        }
        
        // Validate password requirements
        const requirements = validatePassword(password);
        if (requirements.length > 0) {
            showMessage('Password must contain ' + requirements.join(', '), 'error');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        loading.style.display = 'inline-block';
        hideMessage();
        
        try {
            const response = await fetch('php/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    otp,
                    new_password: password,
                    confirm_password: passwordConfirm
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear sessionStorage
                sessionStorage.removeItem('reset_email');
                sessionStorage.removeItem('reset_otp');
                
                showMessage(data.message, 'success');
                
                // Redirect to login page after short delay
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 1500);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            loading.style.display = 'none';
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
