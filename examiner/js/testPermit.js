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
        const photoSrc = `../${user.profile_picture}`;
        photoBox.src = photoSrc;
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

    // Full name
    const fullName = `${user.first_name} ${user.middle_name ? user.middle_name + ' ' : ''}${user.last_name}`;
    
    // Personal Information
    document.getElementById('testPermit').textContent = user.test_permit || 'N/A';
    document.getElementById('fullName').textContent = fullName;

    // Date of Birth
    let dobText = 'N/A';
    if (user.date_of_birth) {
        const dob = new Date(user.date_of_birth);
        dobText = dob.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit',
            year: 'numeric'
        });
        document.getElementById('dob').textContent = dobText;
    }

    // Contact Number
    const contactText = user.contact_number || 'N/A';
    document.getElementById('contact').textContent = contactText;

    // Email Address
    const emailText = user.email || 'N/A';
    document.getElementById('email').textContent = emailText;

    // Schedule of Examination
    let dateOfTestText = 'Not Scheduled';
    if (user.exam_date) {
        const examDate = new Date(user.exam_date);
        dateOfTestText = examDate.toLocaleDateString('en-US', {
            month: '2-digit',
            day: '2-digit',
            year: 'numeric'
        });
        document.getElementById('dateOfTest').textContent = dateOfTestText;
    } else {
        document.getElementById('dateOfTest').textContent = 'Not Scheduled';
    }

    // Venue
    const venueText = user.exam_venue ? `${user.exam_venue}, ${user.region}` : 'Not Scheduled';
    document.getElementById('venue').textContent = venueText;
    
    // Test Permit Number
    const testPermitText = user.test_permit || 'N/A';
}

function loadTransaction(userId) {
    console.log('Loading transaction for user:', userId);
    
    fetch(`php/get_transaction.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Transaction response:', data);
            
            let transactionNo = 'N/A';
            
            if (data.status !== "success") {
                console.warn("No payment found in transaction query");
                document.getElementById('transactionNo').textContent = 'N/A';
                // Still try to load payment details
                loadPaymentDetails(userId);
                return;
            }

            transactionNo = data.external_id;
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
    
    const paymentDateEl = document.getElementById('paymentDate');
    const paymentMethodEl = document.getElementById('paymentMethod');
    
    // Set default values first
    if (paymentDateEl) paymentDateEl.textContent = 'N/A';
    if (paymentMethodEl) paymentMethodEl.textContent = 'N/A';
    
    // Fetch full payment details
    fetch(`php/get_payment_details.php?user_id=${userId}`)
        .then(response => {
            console.log('Payment response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment details response:', data);
            
            if (data.success && data.payment) {
                const payment = data.payment;
                console.log('Payment data:', payment);

                // Payment Date - try multiple date fields
                let paymentDateStr = 'N/A';
                const dateValue = payment.paid_at || payment.payment_date || payment.created_at;
                console.log('Raw date value:', dateValue);
                
                if (dateValue) {
                    try {
                        const paymentDate = new Date(dateValue);
                        console.log('Parsed date:', paymentDate);
                        
                        if (!isNaN(paymentDate.getTime())) {
                            paymentDateStr = paymentDate.toLocaleDateString('en-US', {
                                month: '2-digit',
                                day: '2-digit',
                                year: 'numeric'
                            });
                            console.log('Formatted payment date:', paymentDateStr);
                        }
                    } catch (e) {
                        console.error('Error parsing date:', e);
                    }
                } else {
                    console.warn('No date value found in payment data');
                }
                
                // Set payment date
                if (paymentDateEl) {
                    paymentDateEl.textContent = paymentDateStr;
                }

                // Payment Method / Channel
                const channelValue = payment.channel || 'N/A';
                console.log('Payment channel:', channelValue);
                
                if (paymentMethodEl) {
                    paymentMethodEl.textContent = channelValue;
                }

            } else {
                console.warn('Payment details not found or unsuccessful response');
                console.log('Success:', data.success);
                console.log('Payment:', data.payment);
                if (data.message) {
                    console.log('Message:', data.message);
                }
            }
        })
        .catch(error => {
            console.error("Error loading payment details:", error);
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
