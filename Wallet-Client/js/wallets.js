function openAddWalletModal() {
    document.getElementById("addWalletModal").style.display = "flex";
  }

  function closeAddWalletModal() {
    document.getElementById("addWalletModal").style.display = "none";
  }


  window.onclick = function (event) {
    const modal = document.getElementById("addWalletModal");
    if (event.target === modal) {
      closeAddWalletModal();
    }

};
    function toggleCardDetails(element) {
        const isCardNumber = element.classList.contains('card-number');
        const isCVV = element.classList.contains('cvv-value');
    
        if (isCardNumber) {
            const fullNumber = element.dataset.number;
            const isHidden = element.innerHTML.includes('****');
    
            if (isHidden) {
                element.innerHTML = fullNumber;
            } else {
                const lastFour = fullNumber.slice(-4);
                element.innerHTML = `**** **** **** ${lastFour}`;
                element.style.color = "";
            }
        } else if (isCVV) {
            const cvv = element.dataset.cvv;
            const isHidden = element.innerHTML === '***';
    
            if (isHidden) {
                element.innerHTML = cvv;
                
            } else {
                element.innerHTML = '***';
                element.style.color = "";
            }
        }
    }
 