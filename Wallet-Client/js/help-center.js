function showSection(type) {
 
    const faqSection = document.getElementById("faq-section");
    const supportSection = document.getElementById("support-section");
    const faqButton = document.querySelectorAll(".option-btn")[0];
    const supportButton = document.querySelectorAll(".option-btn")[1];
  
    if (type === "faq") {
      faqSection.classList.remove("disp-none"); 
      supportSection.classList.add("disp-none");
  
    } else if (type === "support") {
      faqSection.classList.add("disp-none"); 
      supportSection.classList.remove("disp-none"); 
  
    }
  }