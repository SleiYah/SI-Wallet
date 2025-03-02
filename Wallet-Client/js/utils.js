document.addEventListener('DOMContentLoaded', function() {
    const burger = document.querySelector('.burger-menu');
    const nav = document.querySelector('nav ul');
    
    burger.addEventListener('click', function() {
      nav.classList.toggle('active');
    });
  });