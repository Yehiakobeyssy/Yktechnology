document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const message = document.getElementById('message');
    
    if (newPassword.length < 6) {
        e.preventDefault();
        message.innerHTML = '<p class="error">Password must be at least 6 characters long.</p>';
    } else if (newPassword !== confirmPassword) {
        e.preventDefault();
        message.innerHTML = '<p class="error">New password and confirmation do not match.</p>';
    } else {
        message.innerHTML = '';
    }
});