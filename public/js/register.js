document.getElementById('registerForm').addEventListener('submit', async (e) => {
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
        const res = await fetch('../api/register.php', {
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