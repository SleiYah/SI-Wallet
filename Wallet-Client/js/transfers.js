function showTransferForm(formType) {
    document.querySelectorAll(".transfer-form").forEach((form) => {
      form.classList.add("disp-none");
    });

    document
      .getElementById(formType + "-form")
      .classList.remove("disp-none");
    event.target.classList.remove("disp-none");
  }

  function showP2POption(option) {
    document.querySelectorAll(".p2p-form").forEach((form) => {
      form.classList.add("disp-none");
    });

    document.getElementById("p2p-" + option).classList.remove("disp-none");
    event.target.classList.remove("disp-none");
  }

  function toggleSchedule(checkbox) {
    const form = checkbox.closest("form");
    const scheduleContainer = form.querySelector(".schedule-container");

    if (checkbox.checked) {
      scheduleContainer.style.display = "flex";
    } else {
      scheduleContainer.style.display = "none";
    }
  }