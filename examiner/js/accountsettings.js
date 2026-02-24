// Account Settings Page
document.addEventListener("DOMContentLoaded", () => {
    const avatarInput = document.getElementById("avatarInput");
    const avatarPreview = document.getElementById("avatarPreview");
    const avatarIcon = document.getElementById("avatarIcon");
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
            
            // Add error handler to show placeholder icon if image fails to load
            avatarPreview.onerror = function() {
                console.warn('Failed to load profile picture, showing placeholder');
                avatarPreview.style.display = 'none';
                avatarIcon.style.display = 'block';
                avatarPreview.onerror = null; // Prevent infinite loop
            };
        } else {
            // Show placeholder icon if no profile picture
            avatarPreview.style.display = 'none';
            avatarIcon.style.display = 'block';
        }

        // Personal information
        document.getElementById('lname').textContent = user.last_name || '—';
        document.getElementById('fname').textContent = user.first_name || '—';
        document.getElementById('mname').textContent = user.middle_name || '—';
        document.getElementById('email').textContent = user.email || '—';
        document.getElementById('contact').textContent = user.contact_number || '—';
        
        // Format birthday
        if (user.date_of_birth) {
            const date = new Date(user.date_of_birth);
            const formatted = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('birthday').textContent = formatted;
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
        
        // Check if profile picture exists and update View Permit button
        const viewPermitBtn = document.getElementById('btnViewPermit');
        if (viewPermitBtn) {
            const hasProfilePicture = user.profile_picture && user.profile_picture.trim() !== '';
            viewPermitBtn.disabled = !hasProfilePicture;
            viewPermitBtn.style.opacity = hasProfilePicture ? '1' : '0.5';
            viewPermitBtn.style.cursor = hasProfilePicture ? 'pointer' : 'not-allowed';
        }
    }

    // Check if user can edit (RESTRICTION TEMPORARILY DISABLED)
    function checkEditPermission(user) {
        // 7-day restriction is currently disabled
        // Users can always edit their profile
        
        /* ORIGINAL RESTRICTION CODE (COMMENTED OUT)
        if (!user.can_edit) {
            globalEditButton.disabled = true;
            globalEditButton.title = `You can edit again in ${user.days_remaining} day(s)`;
            globalEditButton.style.opacity = '0.5';
            globalEditButton.style.cursor = 'not-allowed';
            
            // Also disable avatar upload if can't edit
            avatarInput.disabled = true;
            const uploadArea = document.querySelector('.upload-area');
            if (uploadArea) {
                uploadArea.style.cursor = 'not-allowed';
                uploadArea.style.opacity = '0.6';
                uploadArea.title = `You can upload a new picture in ${user.days_remaining} day(s)`;
            }
        }
        */
    }

    // Handle avatar upload
    function handleAvatarUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        // 7-DAY RESTRICTION TEMPORARILY DISABLED
        /* ORIGINAL RESTRICTION CHECK (COMMENTED OUT)
        // Check if user can upload (same restriction as profile edit)
        if (!userData || !userData.can_edit) {
            showNotification(`You can upload a new profile picture in ${userData.days_remaining} day(s)`, 'warning');
            avatarInput.value = ''; // Reset file input
            return;
        }
        */

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
                showNotification(data.message || 'Profile picture updated successfully', 'success');
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
                showNotification(data.message || 'Failed to upload image', 'error');
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
            showNotification('Error uploading image', 'error');
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
        // 7-DAY RESTRICTION TEMPORARILY DISABLED
        /* ORIGINAL RESTRICTION CHECK (COMMENTED OUT)
        if (!userData || !userData.can_edit) {
            showNotification(`You can edit your profile again in ${userData.days_remaining} day(s)`, 'warning');
            return;
        }
        */

        isEditMode = !isEditMode;

        if (isEditMode) {
            enableEditMode();
            globalEditButton.innerHTML = '<i class="bx bx-x me-2"></i> <span>Cancel Edit</span>';
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
            globalEditButton.innerHTML = '<i class="bx bx-edit me-2"></i> <span class=\"d-none d-sm-inline\">Edit All</span><span class=\"d-sm-none\">Edit</span>';
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

    // Enable edit mode
    function enableEditMode() {
        const editableFields = [
            { id: 'lname', type: 'text', key: 'last_name' },
            { id: 'fname', type: 'text', key: 'first_name' },
            { id: 'mname', type: 'text', key: 'middle_name' },
            { id: 'contact', type: 'tel', key: 'contact_number' },
            { id: 'birthday', type: 'date', key: 'date_of_birth' },
            { id: 'school', type: 'text', key: 'school' }
        ];

        editableFields.forEach(field => {
            const element = document.getElementById(field.id);
            const currentValue = field.type === 'date' ? 
                originalData[field.key] : 
                element.textContent;
            
            element.innerHTML = `<input type="${field.type}" 
                class="form-control form-control-sm" 
                value="${currentValue}" 
                data-field="${field.key}" />`;
        });

        // Add change listener to birthday input
        const birthdayInput = document.querySelector('#birthday input');
        if (birthdayInput) {
            birthdayInput.addEventListener('change', updateAge);
        }
    }

    // Disable edit mode (restore original values)
    function disableEditMode() {
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

        // Collect updated data
        const updatedData = {};
        const inputs = document.querySelectorAll('.form-control');
        
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
                showNotification(data.message, 'success');
                // Reload user data
                loadUserData();
                // Exit edit mode
                isEditMode = true; // Set to true so toggleEditMode will disable it
                toggleEditMode();
            } else {
                showNotification(data.message || 'Failed to update profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            showNotification('Error updating profile', 'error');
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
});
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
const avatarIcon = document.getElementById('avatarIcon');
const viewPermitBtn = document.getElementById('btnViewPermit');

let hasUploadedImage = false;

// Show image preview and enable button
avatarInput.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      avatarPreview.src = e.target.result;
      avatarPreview.style.display = 'block';
      avatarIcon.style.display = 'none';
      hasUploadedImage = true;

      // Enable the View Test Permit button
      viewPermitBtn.disabled = false;
      viewPermitBtn.style.opacity = 1; // Optional: visually indicate active
      viewPermitBtn.style.cursor = 'pointer';
    }
    reader.readAsDataURL(file);
  }
});

avatarInput.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      avatarPreview.src = e.target.result;
      avatarPreview.style.display = 'block';
      avatarIcon.style.display = 'none';

      // Hide overlay
      document.getElementById('avatarOverlay').style.display = 'none';

      hasUploadedImage = true;
      viewPermitBtn.disabled = false;
      viewPermitBtn.style.opacity = 1;
      viewPermitBtn.style.cursor = 'pointer';
    }
    reader.readAsDataURL(file);
  }
});