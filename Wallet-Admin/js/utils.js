
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
  }
}

function logout() {
  localStorage.removeItem('authToken');
  window.location.href = 'index.html';
}


function checkAuthStatus(page_name) {
  const authToken = localStorage.getItem('authToken');
  if (!authToken) {
      window.location.href = page_name;
  }
}

