document.addEventListener('DOMContentLoaded', function () {
    let formToDelete = null;
    let modalInstance = null;

    // Ketika tombol hapus diklik
    // Use event delegation or re-attach listeners if DOM changes, 
    // but for now simple querySelectorAll is fine as long as we don't add tasks dynamically without reload.
    // If tasks are added dynamically, we need to handle that. 
    // Assuming for now tasks are rendered on server side or we reload on add.
    
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            const btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
            const title = btn.dataset.title;
            formToDelete = btn.closest('form');

            document.getElementById('deleteTaskTitle').textContent = title;

            const modalEl = document.getElementById('confirmDeleteModal');
            // Check if modal instance already exists
            modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalEl);
            }
            modalInstance.show();
        }
    });

    // Ketika tombol konfirmasi diklik
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (formToDelete) {
            const url = formToDelete.action;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Hide modal
                if (modalInstance) {
                    modalInstance.hide();
                }

                // Remove the task item from DOM
                const taskItem = formToDelete.closest('.task-item');
                if (taskItem) {
                    taskItem.style.transition = 'opacity 0.5s';
                    taskItem.style.opacity = '0';
                    setTimeout(() => {
                        taskItem.remove();
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus task. Silakan coba lagi.');
            });
        }
    });
});
