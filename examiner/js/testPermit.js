// Test Permit Page - Auto-populate user data
document.addEventListener("DOMContentLoaded", () => {
    loadTestPermitData();
    
    // Listen for profile picture updates from other pages
    window.addEventListener('storage', (e) => {
        if (e.key === 'profilePictureUpdated') {
            console.log('Profile picture updated, reloading data...');
            loadTestPermitData();
        }
    });
});

function loadTestPermitData() {
    // Fetch user data
    fetch("php/get_user.php")
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (!data.success) {
                console.error("API Error:", data.message);
                alert("Failed to load test permit data. Please try again.");
                return;
            }

            const user = data.user;
            console.log("User data loaded:", user);

            populateTestPermit(user);
            
            // Load transaction and generate QR
            if (user.user_id) {
                loadTransaction(user.user_id);
            }
        })
        .catch(error => {
            console.error("Error loading user data:", error);
            alert("Error loading test permit data. Please refresh the page.");
        });
}

function populateTestPermit(user) {
    // Profile Picture with default fallback
    const photoBox = document.querySelector('.photo-box img');
    if (user.profile_picture && user.profile_picture.trim() !== '') {
        photoBox.src = `../${user.profile_picture}`;
        photoBox.alt = "Examinee Photo";
        
        // Add error handler to fallback to default avatar if image fails to load
        photoBox.onerror = function() {
            console.warn('Failed to load profile picture, using default avatar');
            this.src = "../imgs/avatar.png";
            this.alt = "Default Avatar";
            this.onerror = null; // Prevent infinite loop
        };
    } else {
        // Use default avatar if no profile picture
        photoBox.src = "../imgs/avatar.png";
        photoBox.alt = "Default Avatar";
    }

    // Update navigation greeting
    const greetingSpan = document.querySelector('.greeting span');
    if (greetingSpan) {
        greetingSpan.textContent = `${user.first_name} ${user.last_name}`;
    }

    // Personal Information
    document.getElementById('testPermit').textContent = user.test_permit || 'N/A';
    
    // Status with proper formatting
    const statusEl = document.getElementById('status');
    if (user.status === 'active') {
        statusEl.textContent = 'Active';
        statusEl.className = 'fw-semibold text-success';
    } else {
        statusEl.textContent = user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Inactive';
        statusEl.className = 'fw-semibold text-warning';
    }

    // Full name
    const fullName = `${user.first_name} ${user.middle_name ? user.middle_name + ' ' : ''}${user.last_name}`;
    document.getElementById('fullName').textContent = fullName;

    // Date of Birth
    if (user.date_of_birth) {
        const dob = new Date(user.date_of_birth);
        document.getElementById('dob').textContent = dob.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit',
            year: 'numeric'
        });
    }

    // Age
    document.getElementById('age').textContent = user.age || 'N/A';

    // Gender
    document.getElementById('gender').textContent = user.gender || 'N/A';

    // Nationality (should be Filipino)
    document.getElementById('nationality').textContent = user.nationality || 'Filipino';

    // Contact Number
    document.getElementById('contact').textContent = user.contact_number || 'N/A';

    // Email Address
    document.getElementById('email').textContent = user.email || 'N/A';

    // Schedule of Examination
    if (user.exam_date) {
        const examDate = new Date(user.exam_date);
        document.getElementById('dateOfTest').textContent = examDate.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit',
            year: 'numeric'
        });
    } else {
        document.getElementById('dateOfTest').textContent = 'Not Scheduled';
    }

    // Venue
    if (user.exam_venue) {
        document.getElementById('venue').textContent = `${user.exam_venue}, ${user.region}`;
    } else {
        document.getElementById('venue').textContent = 'Not Scheduled';
    }
}

function loadTransaction(userId) {
    console.log('Loading transaction for user:', userId);
    
    fetch(`php/get_transaction.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Transaction response:', data);
            
            if (data.status !== "success") {
                console.warn("No payment found in transaction query");
                document.getElementById('transactionNo').textContent = 'N/A';
                // Still try to load payment details
                loadPaymentDetails(userId);
                return;
            }

            const transactionNo = data.external_id;
            console.log("Transaction number:", transactionNo);

            // Populate transaction number
            document.getElementById('transactionNo').textContent = transactionNo;

            // Generate QR Code with transaction number
            generateQRCode(transactionNo);

            // Load additional payment details
            loadPaymentDetails(userId);
        })
        .catch(error => {
            console.error("Error loading transaction:", error);
            document.getElementById('transactionNo').textContent = 'N/A';
            // Still try to load payment details
            loadPaymentDetails(userId);
        });
}

function loadPaymentDetails(userId) {
    console.log('Loading payment details for user:', userId);
    
    // Fetch full payment details
    fetch(`php/get_payment_details.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Payment details response:', data);
            
            if (data.success && data.payment) {
                const payment = data.payment;
                console.log('Payment data:', payment);

                // Payment Date (using paid_at column, fallback to payment_date)
                const dateValue = payment.paid_at || payment.payment_date || payment.created_at;
                if (dateValue) {
                    const paymentDate = new Date(dateValue);
                    const formattedDate = paymentDate.toLocaleDateString('en-US', {
                        month: '2-digit',
                        day: '2-digit',
                        year: 'numeric'
                    });
                    console.log('Setting payment date to:', formattedDate);
                    document.getElementById('paymentDate').textContent = formattedDate;
                } else {
                    console.warn('No date value in payment data');
                    document.getElementById('paymentDate').textContent = 'N/A';
                }

                // Amount
                if (payment.amount) {
                    const formattedAmount = `₱${parseFloat(payment.amount).toFixed(2)}`;
                    console.log('Setting amount to:', formattedAmount);
                    document.getElementById('amount').textContent = formattedAmount;
                } else {
                    console.warn('No amount value in payment data');
                    document.getElementById('amount').textContent = 'N/A';
                }

                // Channel
                if (payment.channel) {
                    console.log('Setting channel to:', payment.channel);
                    document.getElementById('paymentMethod').textContent = payment.channel;
                }  
                    else {
                    console.warn('No channel value in payment data');
                    document.getElementById('paymentMethod').textContent = 'N/A';
                }


            } else {
                console.warn('Payment details not found or unsuccessful response');
                if (data.error_detail) {
                    console.error('Error detail:', data.error_detail);
                }
                document.getElementById('paymentDate').textContent = 'N/A';
                document.getElementById('amount').textContent = 'N/A';
            }
        })
        .catch(error => {
            console.error("Error loading payment details:", error);
            document.getElementById('paymentDate').textContent = 'N/A';
            document.getElementById('amount').textContent = 'N/A';
        });
}

function generateQRCode(transactionNo) {
    const qrContainer = document.querySelector('.qr-code-square-container');
    
    if (!qrContainer) {
        console.error('QR container not found');
        return;
    }
    
    // Clear any existing QR code
    const existingImg = qrContainer.querySelector('img');
    if (existingImg) {
        existingImg.remove();
    }

    // Validate transaction number
    if (!transactionNo || transactionNo === 'N/A') {
        console.warn('No valid transaction number to generate QR code');
        // Keep the placeholder image
        return;
    }

    // Check if QRCode library is loaded
    if (typeof QRCode === 'undefined') {
        console.error('QRCode library not loaded');
        return;
    }

    // Create a temporary container for QR generation
    const tempDiv = document.createElement('div');
    tempDiv.style.display = 'none';
    document.body.appendChild(tempDiv);

    try {
        // Generate QR code
        const qr = new QRCode(tempDiv, {
            text: transactionNo,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Wait for QR code to be generated
        setTimeout(() => {
            const canvas = tempDiv.querySelector('canvas');
            if (canvas) {
                // Convert canvas to image
                const img = document.createElement('img');
                img.id = 'qrCode';
                img.src = canvas.toDataURL();
                img.alt = 'QR Code';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';

                qrContainer.appendChild(img);
            }

            // Remove temporary container
            tempDiv.remove();
        }, 100);
    } catch (error) {
        console.error('Error creating QR code:', error);
        tempDiv.remove();
        return;
    }
}
