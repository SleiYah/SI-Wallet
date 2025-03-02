function showProfileSection(sectionId) {
    document.querySelectorAll(".profile-section").forEach((section) => {
      section.classList.add("disp-none");
    });

    document.getElementById(sectionId + "-section").classList.remove("disp-none");
    event.target.classList.remove("disp-none");
   
    
  }