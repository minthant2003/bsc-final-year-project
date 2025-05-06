document.addEventListener("DOMContentLoaded", () => {
    // toast msg config
    const toastDiv = document.getElementById("toastDiv");
    const toastBs = bootstrap.Toast.getOrCreateInstance(toastDiv);
    const toastTitle = document.getElementById("toastTitle");
    const toastMsg = document.getElementById("toastMsg");

    // global show toast method
    window.showToast = (title, msg, success) => {
        toastTitle.textContent = title;
        toastMsg.textContent = msg;
        if (success) {
            toastDiv.classList.add("bg-success", "text-white");
            toastDiv.classList.remove("bg-danger");
        } else {
            toastDiv.classList.add("bg-danger", "text-white");
            toastDiv.classList.remove("bg-success");
        }
        toastBs.show();
    };
});
