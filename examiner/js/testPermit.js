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
    if (photoBox) {
        if (user.profile_picture && user.profile_picture.trim() !== '') {
            const photoSrc = `../${user.profile_picture}`;
            photoBox.src = photoSrc;
            photoBox.alt = "Examinee Photo";

            photoBox.onerror = function () {
                console.warn('Failed to load profile picture, using default avatar');
                this.src = "../imgs/avatar.png";
                this.alt = "Default Avatar";
                this.onerror = null;
            };
        } else {
            photoBox.src = "../imgs/avatar.png";
            photoBox.alt = "Default Avatar";
        }
    }

    // Update navigation greeting
    const greetingSpan = document.querySelector('.greeting span');
    if (greetingSpan) {
        greetingSpan.textContent = `${user.first_name || ''} ${user.last_name || ''}`.trim();
    }

    // Full name
    const fullName = [
        user.first_name || '',
        user.middle_name || '',
        user.last_name || ''
    ].filter(Boolean).join(' ');

    // Elements
    const testPermitEl = document.getElementById('testPermit');
    const fullNameEl = document.getElementById('fullName');
    const dobEl = document.getElementById('dob');
    const contactEl = document.getElementById('contact');
    const emailEl = document.getElementById('email');
    const dateOfTestEl = document.getElementById('dateOfTest');
    const venueEl = document.getElementById('venue');
    const mealOptionEl = document.getElementById('mealOption');

    // Personal Information
    if (testPermitEl) {
        testPermitEl.textContent = user.test_permit || 'N/A';
    }

    if (fullNameEl) {
        fullNameEl.textContent = fullName || 'N/A';
    }

    // Date of Birth
    let dobText = 'N/A';
    if (user.date_of_birth) {
        const dob = new Date(user.date_of_birth);
        if (!isNaN(dob.getTime())) {
            dobText = dob.toLocaleDateString('en-US', {
                month: '2-digit',
                day: '2-digit',
                year: 'numeric'
            });
        }
    }
    if (dobEl) {
        dobEl.textContent = dobText;
    }

    // Contact Number
    if (contactEl) {
        contactEl.textContent = user.contact_number || 'N/A';
    }

    // Email Address
    if (emailEl) {
        emailEl.textContent = user.email || 'N/A';
    }

    // Schedule of Examination
    let dateOfTestText = 'Not Scheduled';
    if (user.exam_date) {
        const examDate = new Date(user.exam_date);
        if (!isNaN(examDate.getTime())) {
            dateOfTestText = examDate.toLocaleDateString('en-US', {
                month: '2-digit',
                day: '2-digit',
                year: 'numeric'
            });
        }
    }
    if (dateOfTestEl) {
        dateOfTestEl.textContent = dateOfTestText;
    }

    // Venue
    const venueText = user.exam_venue
        ? `${user.exam_venue}${user.region ? ', ' + user.region : ''}`
        : 'Not Scheduled';

    if (venueEl) {
        venueEl.textContent = venueText;
    }

    // Meal Option (supports single or multiple meals)
    if (mealOptionEl) {
        mealOptionEl.innerHTML = '';

        const formatMealPrice = (value) => {
            const num = Number(value);
            if (isNaN(num)) return '';
            return `₱${num.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        };

        let meals = [];

        // Case 1: array already provided
        if (Array.isArray(user.meals)) {
            meals = user.meals;
        }
        // Case 2: alternate array field
        else if (Array.isArray(user.selected_meals)) {
            meals = user.selected_meals;
        }
        // Case 3: JSON string from backend
        else if (typeof user.meals === 'string' && user.meals.trim() !== '') {
            try {
                const parsedMeals = JSON.parse(user.meals);
                if (Array.isArray(parsedMeals)) {
                    meals = parsedMeals;
                }
            } catch (error) {
                console.warn('Meals is not valid JSON, falling back to single meal handling.');
            }
        }

        // Case 4: fallback single meal fields
        if (meals.length === 0) {
            const singleMealName =
                user.meal_name ||
                user.meal_option ||
                user.selected_meal ||
                '';

            if (singleMealName) {
                meals = [{
                    meal_name: singleMealName,
                    meal_price: user.meal_price || null
                }];
            }
        }

        // Normalize meals
        const normalizedMeals = meals
            .map(meal => {
                if (typeof meal === 'string') {
                    return {
                        name: meal.trim(),
                        price: null
                    };
                }

                if (meal && typeof meal === 'object') {
                    return {
                        name: (
                            meal.meal_name ||
                            meal.meal_option ||
                            meal.selected_meal ||
                            meal.name ||
                            ''
                        ).trim(),
                        price: meal.meal_price ?? meal.price ?? null
                    };
                }

                return { name: '', price: null };
            })
            .filter(meal => meal.name !== '');

        // No meal selected
        if (normalizedMeals.length === 0) {
            mealOptionEl.textContent = 'N/A';
        }
        // One or more meals selected
        else {
            normalizedMeals.forEach(meal => {
                const item = document.createElement('div');
                item.className = 'meal-item';

                item.textContent = meal.name;
                mealOptionEl.appendChild(item);
            });
        }
    }
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
                loadPaymentDetails(userId);
                return;
            }
            transactionNo = data.external_id;
            console.log("Transaction number:", transactionNo);
            document.getElementById('transactionNo').textContent = transactionNo;

            // Fetch user full name for QR value
            fetch(`php/get_user.php?user_id=${userId}`)
                .then(res => res.json())
                .then(userData => {
                    let qrValue = transactionNo;
                    if (userData && userData.success && userData.user) {
                        const user = userData.user;
                        const fullName = `${user.first_name} ${user.last_name}`;
                        const testPermit = user.test_permit;
                        // CSV style for Excel: transaction_no, full_name
                        // \u000A is line feed (LF, Excel accepts this inside a cell)
                        qrValue = `${fullName} ${transactionNo}\t${testPermit}`;
                    }
                    generateQRCode(qrValue);
                })
                .catch(() => {
                    generateQRCode(transactionNo);
                });

            loadPaymentDetails(userId);
        })
        .catch(error => {
            console.error("Error loading transaction:", error);
            document.getElementById('transactionNo').textContent = 'N/A';
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
