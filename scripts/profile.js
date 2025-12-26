// Show message helper
function showMessage(message, isSuccess = true) {
    const messageDiv = document.getElementById('message');
    const bgColor = isSuccess ? 'bg-green-900 border-green-600 text-green-100' : 'bg-red-900 border-red-700 text-red-200';
    const icon = isSuccess ? '✓' : '✗';
    
    messageDiv.innerHTML = `
        <div class="${bgColor} border px-4 py-3 rounded text-center font-medium">
            ${icon} ${message}
        </div>
    `;
    
    // Scroll to top to see message
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageDiv.innerHTML = '';
    }, 5000);
}

// Update Profile Form
document.getElementById('updateProfileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        
        username: document.getElementById('username').value.trim(),
        
        email: document.getElementById('email').value.trim()
    };

    try {

        const response = await fetch('../api/profile/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();

        if (data.success) {
            showMessage(data.message, true);
            // Update username in navigation if changed
            setTimeout(() => location.reload(), 1500);
        } else {
            showMessage(data.message, false);
        }
    }
    catch (error)
    {
        showMessage('Connection error. Please try again.', false);
    }
});

// Change Password Form
document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        showMessage('New passwords do not match!', false);
        return;
    }
    
    const formData = {
        current_password: document.getElementById('current_password').value,
        new_password: newPassword
    };
    
    try {
        const response = await fetch('../api/profile/change_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message, true);
            // Clear password fields
            document.getElementById('changePasswordForm').reset();
        } else {
            showMessage(data.message, false);
        }
    } catch (error) {
        showMessage('Connection error. Please try again.', false);
    }
});

// Delete Account Modal
const deleteModal = document.getElementById('deleteModal');
const deleteAccountBtn = document.getElementById('deleteAccountBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

deleteAccountBtn.addEventListener('click', () => {
    deleteModal.classList.remove('hidden');
});

cancelDeleteBtn.addEventListener('click', () => {
    deleteModal.classList.add('hidden');
    document.getElementById('delete_password').value = '';
});

confirmDeleteBtn.addEventListener('click', async () => {
    const password = document.getElementById('delete_password').value;
    
    if (!password) {
        alert('Please enter your password to confirm deletion.');
        return;
    }
    
    if (!confirm('Are you absolutely sure? This cannot be undone!')) {
        return;
    }
    
    try {
        const response = await fetch('../api/profile/delete_account.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Your account has been deleted. Goodbye!');
            window.location.href = 'login.php';
        } else {
            showMessage(data.message, false);
            deleteModal.classList.add('hidden');
        }
    } catch (error) {
        showMessage('Connection error. Please try again.', false);
        deleteModal.classList.add('hidden');
    }
});

// Close modal on outside click
deleteModal.addEventListener('click', (e) => {
    if (e.target === deleteModal) {
        deleteModal.classList.add('hidden');
        document.getElementById('delete_password').value = '';
    }
});