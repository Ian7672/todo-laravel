// File: js/components/edit.js

document.addEventListener('DOMContentLoaded', function() {
    const editTaskModal = document.getElementById('editTaskModal');
    const editTaskForm = document.getElementById('editTaskForm');
    const editErrorAlert = document.getElementById('editErrorAlert');
    const editErrorList = document.getElementById('editErrorList');
    
    // CSRF Token untuk request AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Event listener untuk tombol edit
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
            const button = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
            const taskId = button.getAttribute('data-id');
            
            if (taskId) {
                loadTaskData(taskId);
            }
        }
    });
    
    // Function untuk load data task
    async function loadTaskData(taskId) {
        try {
            showLoading(true);
            
            const response = await fetch(`/tasks/${taskId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal memuat data task');
            }
            
            const data = await response.json();
            populateEditForm(data.task, taskId);
            hideErrorAlert();
            
        } catch (error) {
            console.error('Error loading task data:', error);
            showErrorAlert(['Gagal memuat data task. Silakan coba lagi.']);
        } finally {
            showLoading(false);
        }
    }
    
    // Function untuk mengisi form dengan data task
    function populateEditForm(task, taskId) {
        // Set form action URL
        editTaskForm.setAttribute('action', `/tasks/${taskId}`);
        
        // Isi field-field form
        document.getElementById('editTaskTitle').value = task.title || '';
        document.getElementById('editTaskDescription').value = task.description || '';
        
        // Format tanggal untuk input date (YYYY-MM-DD)
        if (task.deadline) {
            const deadlineDate = new Date(task.deadline);
            const formattedDeadline = deadlineDate.toISOString().split('T')[0];
            document.getElementById('editTaskDeadline').value = formattedDeadline;
        }
        
        // Set checkbox status
        document.getElementById('editTaskCompleted').checked = task.completed == 1;
        
        // Set info tanggal (read-only fields)
        if (task.created_at) {
            const createdDate = new Date(task.created_at);
            document.getElementById('editTaskCreated').value = formatDateTime(createdDate);
        }
        
        if (task.updated_at) {
            const updatedDate = new Date(task.updated_at);
            document.getElementById('editTaskUpdated').value = formatDateTime(updatedDate);
        }
    }
    
    // Event listener untuk submit form
    editTaskForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(editTaskForm);
        const actionUrl = editTaskForm.getAttribute('action');
        
        try {
            showLoading(true);
            hideErrorAlert();
            
            const response = await fetch(actionUrl, {
                method: 'POST', // Laravel akan mendeteksi _method: PUT
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                if (data.errors) {
                    // Validation errors
                    const errorMessages = Object.values(data.errors).flat();
                    showErrorAlert(errorMessages);
                } else {
                    showErrorAlert([data.message || 'Terjadi kesalahan saat menyimpan task']);
                }
                return;
            }
            
            // Success - tutup modal dan refresh halaman atau update UI
            bootstrap.Modal.getInstance(editTaskModal).hide();
            
            // Show success message (bisa menggunakan toast atau alert)
            showSuccessMessage('Task berhasil diperbarui!');
            
            // Refresh halaman atau update UI secara dinamis
            setTimeout(() => {
                location.reload(); // Atau implementasi update UI tanpa reload
            }, 1000);
            
        } catch (error) {
            console.error('Error updating task:', error);
            showErrorAlert(['Terjadi kesalahan jaringan. Silakan coba lagi.']);
        } finally {
            showLoading(false);
        }
    });
    
    // Function untuk menampilkan loading state
    function showLoading(show) {
        const submitButton = editTaskForm.querySelector('button[type="submit"]');
        const submitText = submitButton.querySelector('i').nextSibling;
        
        if (show) {
            submitButton.disabled = true;
            submitText.textContent = ' Menyimpan...';
        } else {
            submitButton.disabled = false;
            submitText.textContent = ' Simpan Perubahan';
        }
    }
    
    // Function untuk menampilkan error alert
    function showErrorAlert(errors) {
        editErrorList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            editErrorList.appendChild(li);
        });
        editErrorAlert.classList.remove('d-none');
    }
    
    // Function untuk menyembunyikan error alert
    function hideErrorAlert() {
        editErrorAlert.classList.add('d-none');
        editErrorList.innerHTML = '';
    }
    
    // Function untuk menampilkan success message
    function showSuccessMessage(message) {
        // Implementasi toast atau alert success
        // Bisa menggunakan Bootstrap toast atau library lain
        
        // Contoh sederhana dengan alert (bisa diganti dengan toast)
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }
    
    // Function untuk format tanggal dan waktu
    function formatDateTime(date) {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        
        return date.toLocaleDateString('id-ID', options);
    }
    
    // Reset form ketika modal ditutup
    editTaskModal.addEventListener('hidden.bs.modal', function() {
        editTaskForm.reset();
        hideErrorAlert();
        editTaskForm.setAttribute('action', '');
    });
    
    // Validasi input secara real-time
    document.getElementById('editTaskTitle').addEventListener('input', function() {
        const maxLength = 255;
        const currentLength = this.value.length;
        
        if (currentLength > maxLength) {
            this.value = this.value.substring(0, maxLength);
        }
    });
    
    document.getElementById('editTaskDescription').addEventListener('input', function() {
        const maxLength = 1000;
        const currentLength = this.value.length;
        
        if (currentLength > maxLength) {
            this.value = this.value.substring(0, maxLength);
        }
    });
    
    // Set minimum date untuk deadline (hari ini)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('editTaskDeadline').setAttribute('min', today);
});