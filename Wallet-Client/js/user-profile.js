let isVerifying = false;

function showProfileSection(sectionId) {
  document.querySelectorAll(".profile-section").forEach((section) => {
    section.classList.add("disp-none");
  });

  document.getElementById(sectionId + "-section").classList.remove("disp-none");
}



function loadUserProfile() {
  const authToken = localStorage.getItem('authToken');
  
  if (!authToken) {
    showMessage('Authentication token not found. Please login again.', 'error');
    window.location.href = 'sign-in.html';
    return;
  }
  
  axios.post(
    'http://localhost/projects/SI-Wallet/Wallet-Server/users/v1/get-users-byId.php',
    {},
    {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    }
  )
  .then(function(response) {
    if (response.data.success) {
      const userData = response.data.data;
      document.getElementById('username').value = userData.username || '';
      document.getElementById('first_name').value = userData.first_name || '';
      document.getElementById('last_name').value = userData.last_name || '';
      document.getElementById('email').value = userData.email || '';
      
      updateEmailVerificationStatus(userData);
    } else {
      showMessage(response.data.message || 'Failed to load user data', 'error');
    }
  })
  .catch(function(error) {
    console.error('Error loading user data:', error);
    
    if (error.response && error.response.status === 401) {
      showMessage('Your session has expired. Please login again.', 'error');
      localStorage.removeItem('authToken');
      setTimeout(function() {
        window.location.href = 'sign-in.html';
      }, 1500);
    } else {
      showMessage('An error occurred while loading your profile.', 'error');
    }
  });
}

function updateEmailVerificationStatus(userData) {
  const statusElement = document.getElementById('email-verification-status');
  
  if (userData.tier > 1) {
    statusElement.textContent = `Your email (${userData.email}) is verified.`;
    statusElement.style.color = '#57ff65';
    
    const verifyButton = document.getElementById('verify-email-btn');
    verifyButton.disabled = true;
    verifyButton.style.opacity = '0.5';
    verifyButton.textContent = 'Already Verified';
  } else {
    statusElement.textContent = `Your email (${userData.email}) is not verified. Please verify your email to unlock basic features.`;
    statusElement.style.color = '#ff6347';
  }
}

function sendVerificationEmail() {
  if (isVerifying) return;
  
  isVerifying = true;
  const verifyButton = document.getElementById('verify-email-btn');
  verifyButton.disabled = true;
  verifyButton.textContent = 'Sending...';
  
  const authToken = localStorage.getItem('authToken');
  
  if (!authToken) {
    showMessage('Authentication token not found. Please login again.', 'error');
    verifyButton.disabled = false;
    verifyButton.textContent = 'Verify Email';
    isVerifying = false;
    return;
  }
  
  axios.post(
    'http://localhost/projects/SI-Wallet/Wallet-Server/verification/v1/send-verification-email.php', 
    {}, 
    {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    }
  )
  .then(function(response) {
    console.log("response", response)
    if (response.data.success) {
      showMessage('Verification email sent! Please check your inbox.', 'success');
      document.getElementById('email-verification-status').textContent = 
        'Verification email sent. Please check your inbox and click the verification link.';
      document.getElementById('email-verification-status').style.color = '#add8e6';
    } else {
      showMessage(response.data.message || 'Failed to send verification email', 'error');
    }
  })
  .catch(function(error) {
    console.error('Error sending verification email:', error);
    
    if (error.response && error.response.status === 401) {
      showMessage('Your session has expired. Please login again.', 'error');
      localStorage.removeItem('authToken');
      setTimeout(function() {
        window.location.href = 'sign-in.html';
      }, 1500);
    } else {
      showMessage('An error occurred while sending the verification email.', 'error');
    }
  })
  .finally(function() {
    verifyButton.disabled = false;
    verifyButton.textContent = 'Verify Email';
    isVerifying = false;
  });
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('User profile page loaded');
  
  checkAuthStatus("sign-in.html");
  
  loadUserProfile();
  
  const verifyEmailBtn = document.getElementById('verify-email-btn');
  if (verifyEmailBtn) {
    verifyEmailBtn.addEventListener('click', sendVerificationEmail);
  }
  
  const logoutBtn = document.querySelector('.login-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', logout);
  }
});