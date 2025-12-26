// Password toggle functionality
document.querySelectorAll('.password-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('.eye-icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    });
});

// Login form handler
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const data = {
            email: form.email.value,
            password: form.password.value
        };

        try {
            const res = await fetch('../api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            const messageDiv = document.getElementById('message');

            if (json.success) {
                messageDiv.innerHTML = `<div class="bg-green-900 border border-green-600 text-green-100 px-4 py-3 rounded text-center steam-font">✓ ${json.message}</div>`;
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1200);
            } else {
                messageDiv.innerHTML = `<div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-center steam-font">✗ ${json.message}</div>`;
            }
        } catch (err) {
            document.getElementById('message').innerHTML =
                `<div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-center steam-font">✗ Connection error. Please try again.</div>`;
        }
    });
}

// Register form handler
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const password = form.password.value;
        const confirm = form.password_confirm.value;

        if (password !== confirm) {
            document.getElementById('message').innerHTML = `<div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-center steam-font">Passwords do not match!</div>`;
            return;
        }

        const data = {
            username: form.username.value.trim(),
            email: form.email.value.trim(),
            password: password
        };

        try {
            const res = await fetch('../api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            const messageDiv = document.getElementById('message');

            if (json.success) {
                messageDiv.innerHTML =
                    `<div class="bg-green-900 border border-green-600 text-green-100 px-4 py-3 rounded text-center steam-font">Account created successfully! Redirecting...</div>`;
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1500);
            } else {
                messageDiv.innerHTML =
                    `<div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-center steam-font">${json.message || 'Registration failed'}</div>`;
            }
        } catch (err) {
            document.getElementById('message').innerHTML = `<div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-center steam-font">Connection error. Try again later.</div>`;
        }
    });
}