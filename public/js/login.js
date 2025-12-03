document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = {
        email: form.email.value,
        password: form.password.value
    };

    try {
        const res = await fetch('../api/login.php', {
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