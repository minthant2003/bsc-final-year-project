document.addEventListener("DOMContentLoaded", () => {
    const deleteConfirmModal = new bootstrap.Modal(document.getElementById("deleteConfirmModal"));

    // show delete confirm modal
    window.showDeleteConfirmModal = () => {
        deleteConfirmModal.show();
    }
    // hide delete confirm modal
    window.hideDeleteConfirmModal = () => {
        deleteConfirmModal.hide();
    }
});
