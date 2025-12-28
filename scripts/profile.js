document.addEventListener('DOMContentLoaded', () => {
    
    // --- Helper: Show Message ---
    function showMessage(message, isSuccess = true) {
        const messageDiv = document.getElementById('message');
        const bgColor = isSuccess ? 'bg-green-900/40 border-green-500/50 text-green-200' : 'bg-red-900/40 border-red-500/50 text-red-200';
        const icon = isSuccess ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-exclamation-triangle-fill"></i>';
        
        messageDiv.className = `p-4 rounded border text-center font-medium ${bgColor} flex items-center justify-center gap-2 animate-fade-in`;
        messageDiv.innerHTML = `${icon} ${message}`;
        messageDiv.classList.remove('hidden');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        if (isSuccess) {
            setTimeout(() => {
                messageDiv.classList.add('hidden');
            }, 3000);
        }
    }

    // --- Update Profile ---
    const updateProfileForm = document.getElementById('updateProfileForm');
    if (updateProfileForm) {
        updateProfileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Saving...';
            btn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch('../api/profile/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    console.error("API Error:", text);
                    throw new Error("Server error");
                }

                if (data.success) {
                    showMessage(data.message, true);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.message, false);
                }
            } catch (error) {
                console.error(error);
                showMessage('Connection error. Please try again.', false);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    // --- Change Password ---
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                showMessage('New passwords do not match!', false);
                return;
            }
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Updating...';
            btn.disabled = true;

            const formData = new FormData(this);
            
            try {
                const response = await fetch('../api/profile/change_password.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage(data.message, true);
                    this.reset();
                } else {
                    showMessage(data.message, false);
                }
            } catch (error) {
                showMessage('Connection error. Please try again.', false);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    // --- Delete Account ---
    const deleteModal = document.getElementById('deleteModal');
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deletePasswordInput = document.getElementById('delete_password');

    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', () => {
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
            deletePasswordInput.value = '';
            deletePasswordInput.focus();
        });
    }

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async () => {
            const password = deletePasswordInput.value;
            
            if (!password) {
                alert('Please enter your password to confirm deletion.');
                return;
            }
            
            const originalText = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Deleting...';
            confirmDeleteBtn.disabled = true;
            
            try {
                const response = await fetch('../api/profile/delete_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password: password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Your account has been deleted. Goodbye!');
                    window.location.href = 'login.php';
                } else {
                    showMessage(data.message, false);
                    deleteModal.classList.add('hidden');
                    deleteModal.classList.remove('flex');
                }
            } catch (error) {
                showMessage('Connection error. Please try again.', false);
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            } finally {
                confirmDeleteBtn.innerHTML = originalText;
                confirmDeleteBtn.disabled = false;
            }
        });
    }

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }
    });
});