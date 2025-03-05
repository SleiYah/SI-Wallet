document.addEventListener('DOMContentLoaded', function() {
    
    const loginForm = document.getElementById('admin-login-form');
    const loginButton = document.getElementById('admin-login-button');
    
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleAdminLogin();
        });
    }
    
    if (loginButton) {
        loginButton.addEventListener('click', function(event) {
            event.preventDefault();
            handleAdminLogin();
        });
    }
});

function handleAdminLogin() {
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (!username || !password) {
        showMessage('Please enter both username and password', 'error');
        return;
    }
    
    const loginData = {
        username: username,
        password: password
    };
    
    
    axios.post('http://localhost/projects/SI-Wallet/Wallet-Server/admin/v1/admin-login.php', loginData)
        .then(function(response) {
            
            if (response.data.success) {
                localStorage.setItem('authToken', response.data.token);
                
                showMessage('Admin login successful! Redirecting...', 'success');
                
                setTimeout(function() {
                    window.location.href = 'identity-verification.html';
                }, 1000);
            } else {
                showMessage(response.data.message || 'Admin login failed', 'error');
            }
        })
        .catch(function(error) {
            console.error('Error during admin login:', error);
            showMessage('An error occurred during login. Please try again.', 'error');
        });
}

