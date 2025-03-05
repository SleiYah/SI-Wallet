document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM fully loaded - utils.js initialized');
  
  const burger = document.querySelector('.burger-menu');
  const nav = document.querySelector('nav ul');
  
  if (burger && nav) {
      console.log('Burger menu found, adding click listener');
      burger.addEventListener('click', function() {
          nav.classList.toggle('active');
      });
  }
  
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
  console.log('Logout function called');
  localStorage.removeItem('authToken');
  localStorage.removeItem('userData');
  window.location.href = 'sign-in.html';
}


function checkAuthStatus(page_name) {
  const authToken = localStorage.getItem('authToken');
  if (!authToken) {
      window.location.href = page_name;
  }
}