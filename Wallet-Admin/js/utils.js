
document.addEventListener('DOMContentLoaded', function() {
  
  
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) {
      console.log('Logout button found, adding click listener');
      logoutBtn.addEventListener('click', logout);
  }
});

function showMessage(message, type = 'info') {
  console.log(`Showing message: "${message}" with type: ${type}`);
  const messageContainer = document.getElementById('message-container');
  const messageText = document.getElementById('message-text');
  
  if (messageContainer && messageText) {
      messageContainer.classList.remove('message-info', 'message-success', 'message-error');
      messageContainer.classList.add(`message-${type}`);
      messageText.textContent = message;
      messageContainer.style.display = 'block';
      
      setTimeout(function() {
          messageContainer.style.display = 'none';
      }, 3000);
  } else {
      console.warn('Message container or text element not found in the DOM');
  }
}

function logout() {
  localStorage.removeItem('adminAuthToken');
  localStorage.removeItem('adminData');
  window.location.href = 'index.html';
}


function checkAuthStatus(page_name) {
  const adminAuthToken = localStorage.getItem('adminAuthToken');
  if (!adminAuthToken) {
      window.location.href = page_name;
  }
}

