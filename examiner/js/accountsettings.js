// Account Settings Page
document.addEventListener("DOMContentLoaded", () => {
    const avatarInput = document.getElementById("avatarInput");
    const avatarPreview = document.getElementById("avatarPreview");
    const avatarIcon = document.getElementById("avatarIcon");
    const avatarOverlay = document.getElementById("avatarOverlay");
    const globalEditButton = document.getElementById("globalEditButton");
    const formActions = document.querySelector(".form-actions");
    const btnCancel = document.querySelector(".btn-cancel");
    const btnSave = document.querySelector(".btn-save");
    
    let isEditMode = false;
    let originalData = {};
    let userData = null;
    let profilePictureUploaded = false; // Track if picture was uploaded during this session

    // Load user data
    loadUserData();

    // Avatar upload handler
    avatarInput.addEventListener("change", handleAvatarUpload);

    // Global Edit button
    globalEditButton.addEventListener("click", toggleEditMode);

    // Cancel button
    btnCancel.addEventListener("click", cancelEdit);

    // Save button
    btnSave.addEventListener("click", saveChanges);

    // Load user data from server
    function loadUserData() {
        fetch('php/get_user.php', {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userData = data.user;
                populateFields(userData);
                checkEditPermission(userData);
            } else {
                showNotification(data.message || 'Failed to load user data', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading user data:', error);
            showNotification('Error loading user data', 'error');
        });
    }

    // Populate form fields with user data
    function populateFields(user) {
        // Profile picture with default fallback
        if (user.profile_picture && user.profile_picture.trim() !== '') {
            avatarPreview.src = `../${user.profile_picture}`;
            avatarPreview.style.display = 'block';
            avatarIcon.style.display = 'none';
            avatarOverlay.style.display = 'none'; // Hide overlay when picture exists
            
            // Add error handler to show placeholder icon if image fails to load
            avatarPreview.onerror = function() {
                console.warn('Failed to load profile picture, showing placeholder');
                avatarPreview.style.display = 'none';
                avatarIcon.style.display = 'block';
                avatarOverlay.style.display = 'flex'; // Show overlay when picture fails
                avatarPreview.onerror = null; // Prevent infinite loop
            };
        } else {
            // Show placeholder icon if no profile picture
            avatarPreview.style.display = 'none';
            avatarIcon.style.display = 'block';
            avatarOverlay.style.display = 'flex'; // Show overlay when no picture
        }

        // Personal information
        document.getElementById('lname').textContent = user.last_name || '—';
        document.getElementById('fname').textContent = user.first_name || '—';
        document.getElementById('mname').textContent = user.middle_name || '—';
        document.getElementById('email').textContent = user.email || '—';
        document.getElementById('contact').textContent = user.contact_number || '—';
        
        // Also set values in input fields for edit mode
        document.getElementById('input-lname').value = user.last_name || '';
        document.getElementById('input-fname').value = user.first_name || '';
        document.getElementById('input-mname').value = user.middle_name || '';
        document.getElementById('input-contact').value = user.contact_number || '';
        document.getElementById('input-school').value = user.school || '';
        
        // Format birthday
        if (user.date_of_birth) {
            const date = new Date(user.date_of_birth);
            const formatted = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('birthday').textContent = formatted;
            document.getElementById('input-birthday').value = user.date_of_birth;
        }
        
        document.getElementById('age').textContent = user.age || '—';
        document.getElementById('school').textContent = user.school || '—';
        
        // Account details (non-editable)
        document.getElementById('account-test-permit').textContent = user.test_permit || '—';
        document.getElementById('account-exam-date').textContent = user.exam_date ? 
            new Date(user.exam_date).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            }) : '—';
        document.getElementById('account-exam-venue').textContent = 
            user.exam_venue ? `${user.exam_venue}, ${user.region}` : '—';

        // Store original data
        originalData = {
            last_name: user.last_name,
            first_name: user.first_name,
            middle_name: user.middle_name,
            contact_number: user.contact_number,
            date_of_birth: user.date_of_birth,
            school: user.school
        };

        // Update greeting in nav
        const greetingSpan = document.querySelector('.greeting span');
        if (greetingSpan) {
            greetingSpan.textContent = `${user.first_name} ${user.last_name}`;
        }
    }

    // Check if user can upload profile picture (3-attempt limit)
    function checkEditPermission(user) {
        // Edit button is always enabled - only photo upload has 3-attempt limit
        globalEditButton.disabled = false;
        globalEditButton.style.opacity = '1';
        globalEditButton.style.cursor = 'pointer';
        
        const attemptsUsed = user.upload_attempts_used || 0;
        const attemptsRemaining = user.upload_attempts_remaining || 3;
        
        // Display attempts used/remaining
        const uploadText = document.querySelector('.upload-text');
        if (uploadText) {
            if (attemptsUsed === 0) {
                uploadText.textContent = 'Upload picture (0/3 used)';
            } else {
                uploadText.textContent = `Upload picture (${attemptsUsed}/3 used)`;
            }
        }
        
        // Only check 3-attempt restriction for profile picture upload
        if (user.can_upload_picture === false) {
            // Keep edit button enabled but disable avatar upload
            avatarInput.disabled = true;
            const uploadArea = document.querySelector('.upload-area');
            if (uploadArea) {
                uploadArea.style.cursor = 'not-allowed';
                uploadArea.style.opacity = '0.6';
                uploadArea.style.pointerEvents = 'none';
                uploadArea.title = 'You have reached the maximum limit of 3 profile picture uploads';
            }
        } else {
            // Reset avatar upload area if user can upload
            avatarInput.disabled = false;
            const uploadArea = document.querySelector('.upload-area');
            if (uploadArea) {
                uploadArea.style.cursor = 'pointer';
                uploadArea.style.opacity = '1';
                uploadArea.style.pointerEvents = 'auto';
                uploadArea.title = '';
            }
        }
    }

    // Handle avatar upload
    function handleAvatarUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Check if user can upload (3-attempt restriction)
        if (userData && userData.can_upload_picture === false) {
            showNotification('You have reached the maximum limit of 3 profile picture uploads.', 'warning');
            avatarInput.value = ''; // Reset file input
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showNotification('Please select an image file', 'error');
            avatarInput.value = '';
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('File size must be less than 5MB', 'error');
            avatarInput.value = '';
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = e => {
            avatarPreview.src = e.target.result;
            avatarPreview.style.display = 'block';
            avatarIcon.style.display = 'none';
            avatarOverlay.style.display = 'none'; // Hide overlay when preview is shown
        };
        reader.readAsDataURL(file);

        // Upload to server
        const formData = new FormData();
        formData.append('profile_picture', file);

        fetch('php/upload_profile_picture.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                profilePictureUploaded = true; // Mark that picture was uploaded successfully
                
                // Show success modal for profile picture upload
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reload user data to get updated profile picture and restrictions
                loadUserData();
                // Update local storage to trigger other pages to refresh
                localStorage.setItem('profilePictureUpdated', Date.now());
                
                // Remove required indicator if it was showing
                const uploadArea = document.querySelector('.upload-area');
                if (uploadArea) {
                    uploadArea.style.border = '';
                    const uploadText = uploadArea.querySelector('.upload-text');
                    if (uploadText) {
                        uploadText.textContent = 'Upload picture';
                    }
                }
            } else {
                // Show error modal for profile picture upload
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
                
                // Revert to previous image on error
                if (userData && userData.profile_picture) {
                    avatarPreview.src = `../${userData.profile_picture}`;
                    avatarPreview.style.display = 'block';
                    avatarIcon.style.display = 'none';
                } else {
                    avatarPreview.style.display = 'none';
                    avatarIcon.style.display = 'block';
                }
            }
            avatarInput.value = ''; // Reset file input
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            
            // Show error modal for profile picture upload
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            
            // Revert to previous image on error
            if (userData && userData.profile_picture) {
                avatarPreview.src = `../${userData.profile_picture}`;
                avatarPreview.style.display = 'block';
                avatarIcon.style.display = 'none';
            } else {
                avatarPreview.style.display = 'none';
                avatarIcon.style.display = 'block';
            }
            avatarInput.value = ''; // Reset file input
        });
    }

    // Toggle edit mode
    function toggleEditMode() {
        // Edit information is always allowed - only photo upload has 7-day restriction
        isEditMode = !isEditMode;

        if (isEditMode) {
            enableEditMode();
            globalEditButton.innerHTML = '<i class="bx bx-x"></i> <span>Cancel Edit</span>';
            formActions.style.display = 'flex';
            
            // Reset profile picture uploaded flag when entering edit mode
            profilePictureUploaded = false;
            
            // Show visual indicator if no profile picture exists
            if (!userData.profile_picture || userData.profile_picture.trim() === '') {
                const uploadArea = document.querySelector('.upload-area');
                if (uploadArea) {
                    uploadArea.style.border = '2px dashed #dc3545';
                    const uploadText = uploadArea.querySelector('.upload-text');
                    if (uploadText) {
                        uploadText.textContent = 'Upload picture';
                    }
                }
            }
        } else {
            disableEditMode();
            globalEditButton.innerHTML = '<i class="bx bx-edit me-2"></i> <span class=\"d-none d-sm-inline\">Edit Information</span><span class=\"d-sm-none\">Edit</span>';
            formActions.style.display = 'none';
            
            // Reset upload area styling
            const uploadArea = document.querySelector('.upload-area');
            if (uploadArea) {
                uploadArea.style.border = '';
                const uploadText = uploadArea.querySelector('.upload-text');
                if (uploadText) {
                    uploadText.textContent = 'Upload picture';
                }
            }
        }
    }

    // Enable edit mode - show and enable input fields
    function enableEditMode() {
        const editableFields = [
            { id: 'lname', inputId: 'input-lname', key: 'last_name' },
            { id: 'fname', inputId: 'input-fname', key: 'first_name' },
            { id: 'mname', inputId: 'input-mname', key: 'middle_name' },
            { id: 'contact', inputId: 'input-contact', key: 'contact_number' },
            { id: 'birthday', inputId: 'input-birthday', key: 'date_of_birth' },
            { id: 'school', inputId: 'input-school', key: 'school' }
        ];

        editableFields.forEach(field => {
            const fieldGroup = document.getElementById('field-' + field.id);
            const inputElement = document.getElementById(field.inputId);
            
            if (fieldGroup && inputElement) {
                // Add active class to show border
                fieldGroup.classList.add('active');
                
                // Enable the input
                inputElement.disabled = false;
                
                // Set value from original data or display text
                const value = originalData[field.key] || document.getElementById(field.id).textContent;
                inputElement.value = value === '—' ? '' : value;
                
                // Add change listener to birthday input for age calculation
                if (field.id === 'birthday') {
                    inputElement.addEventListener('change', updateAge);
                }
            }
        });
    }

    // Disable edit mode - hide and disable input fields
    function disableEditMode() {
        const editableFields = [
            { id: 'lname', inputId: 'input-lname', key: 'last_name' },
            { id: 'fname', inputId: 'input-fname', key: 'first_name' },
            { id: 'mname', inputId: 'input-mname', key: 'middle_name' },
            { id: 'contact', inputId: 'input-contact', key: 'contact_number' },
            { id: 'birthday', inputId: 'input-birthday', key: 'date_of_birth' },
            { id: 'school', inputId: 'input-school', key: 'school' }
        ];

        editableFields.forEach(field => {
            const fieldGroup = document.getElementById('field-' + field.id);
            const inputElement = document.getElementById(field.inputId);
            
            if (fieldGroup && inputElement) {
                // Remove active class to hide border
                fieldGroup.classList.remove('active');
                
                // Disable the input
                inputElement.disabled = true;
                
                // Remove change listener from birthday input
                if (field.id === 'birthday') {
                    inputElement.removeEventListener('change', updateAge);
                }
            }
        });
        
        // Restore original display values
        document.getElementById('lname').textContent = originalData.last_name || '—';
        document.getElementById('fname').textContent = originalData.first_name || '—';
        document.getElementById('mname').textContent = originalData.middle_name || '—';
        document.getElementById('contact').textContent = originalData.contact_number || '—';
        
        if (originalData.date_of_birth) {
            const date = new Date(originalData.date_of_birth);
            const formatted = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('birthday').textContent = formatted;
        }
        
        document.getElementById('school').textContent = originalData.school || '—';
    }

    // Update age when birthday changes
    function updateAge(e) {
        const dob = new Date(e.target.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        document.getElementById('age').textContent = age;
    }

    // Cancel edit
    function cancelEdit() {
        toggleEditMode();
    }

    // Save changes
    function saveChanges() {
        if (!isEditMode) return;

        // Collect updated data from the edit-input fields
        const updatedData = {};
        const inputs = document.querySelectorAll('.edit-input');
        
        inputs.forEach(input => {
            const fieldKey = input.dataset.field;
            updatedData[fieldKey] = input.value;
        });

        // Validate required fields
        if (!updatedData.first_name || !updatedData.last_name) {
            showNotification('First name and last name are required', 'error');
            return;
        }

        // Profile picture is NOT required to save/edit - users can edit without uploading
        // View Test Permit requires profile picture, but editing is allowed without it

        // Disable save button
        btnSave.disabled = true;
        btnSave.textContent = 'Saving...';

        // Send update request
        fetch('php/update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedData),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success modal for personal information update
                const infoSuccessModal = new bootstrap.Modal(document.getElementById('infoSuccessModal'));
                infoSuccessModal.show();
                
                // Reload user data
                loadUserData();
                // Exit edit mode
                isEditMode = true; // Set to true so toggleEditMode will disable it
                toggleEditMode();
            } else {
                // Show error modal for personal information update
                const infoErrorModal = new bootstrap.Modal(document.getElementById('infoErrorModal'));
                infoErrorModal.show();
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            
            // Show error modal for personal information update
            const infoErrorModal = new bootstrap.Modal(document.getElementById('infoErrorModal'));
            infoErrorModal.show();
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.textContent = 'Save changes';
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Show professional success notification for profile picture upload
    function showProfileUpdateSuccess() {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '380px';
        notification.style.borderLeft = '4px solid #15803d';
        notification.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="bx bx-check-circle fs-4 me-2" style="color: #15803d;"></i>
                <div class="flex-grow-1">
                    <strong style="color: #15803d;">Profile Picture Updated Successfully</strong>
                    <p class="mb-2 mt-1" style="font-size: 0.85rem; color: #4b5563;">
                        Your profile picture has been uploaded. You may now proceed to the dashboard to view your Test Permit.
                    </p>
                    <a href="dashboard.html" class="btn btn-sm" style="background: #15803d; color: white; padding: 6px 16px; font-size: 0.8rem; border-radius: 6px; text-decoration: none;">
                        Go to Dashboard <i class="bx bx-arrow-from-left ms-1"></i>
                    </a>
                </div>
            </div>
            <button type="button" class="btn-close position-absolute" style="top: 10px; right: 10px;" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 10 seconds (longer for this important message)
        setTimeout(() => {
            notification.remove();
        }, 10000);
    }
});