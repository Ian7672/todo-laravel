// File: js/components/detail.js

document.addEventListener('DOMContentLoaded', function() {
    const taskDetailModal = document.getElementById('taskDetailModal');
    const detailLoading = document.getElementById('detailLoading');
    const detailErrorAlert = document.getElementById('detailErrorAlert');
    const detailErrorMessage = document.getElementById('detailErrorMessage');
    const taskDetailContent = document.getElementById('taskDetailContent');
    const editFromDetailBtn = document.getElementById('editFromDetailBtn');
    const toggleFromDetailBtn = document.getElementById('toggleFromDetailBtn');
    
    let currentTaskData = null;
    
    // CSRF Token untuk request AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Event listener untuk tombol More/Detail
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('more-btn') || e.target.closest('.more-btn')) {
            const button = e.target.classList.contains('more-btn') ? e.target : e.target.closest('.more-btn');
            const taskId = button.getAttribute('data-id');
            
            if (taskId) {
                loadTaskDetail(taskId);
            }
        }
    });
    
    // Function untuk load detail task
    async function loadTaskDetail(taskId) {
        try {
            showLoading(true);
            hideError();
            
            const response = await fetch(`/tasks/${taskId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal memuat detail task');
            }
            
            const data = await response.json();
            currentTaskData = data.task;
            populateTaskDetail(data.task);
            
        } catch (error) {
            console.error('Error loading task detail:', error);
            showError('Gagal memuat detail task. Silakan coba lagi.');
        } finally {
            showLoading(false);
        }
    }
    
    // Function untuk mengisi detail task
    function populateTaskDetail(task) {
        // Task ID dan Title
        document.getElementById('taskDetailId').textContent = `#${String(task.id).padStart(3, '0')}`;
        document.getElementById('taskDetailTitle').textContent = task.title || 'Tanpa Judul';
        
        // Status Badge
        updateStatusBadge(task.completed);
        
        // Description
        const descriptionElement = document.getElementById('taskDetailDescription');
        if (task.description && task.description.trim()) {
            descriptionElement.innerHTML = formatDescription(task.description);
        } else {
            descriptionElement.innerHTML = '<em class="text-muted">Tidak ada deskripsi untuk task ini</em>';
        }
        
        // Dates
        if (task.created_at) {
            document.getElementById('taskDetailCreated').textContent = formatDateTime(new Date(task.created_at));
            document.getElementById('activityCreated').textContent = formatRelativeTime(new Date(task.created_at));
        }
        
        if (task.updated_at) {
            document.getElementById('taskDetailUpdated').textContent = formatDateTime(new Date(task.updated_at));
            
            // Show updated activity if different from created
            if (task.updated_at !== task.created_at) {
                document.getElementById('activityUpdatedItem').style.display = 'block';
                document.getElementById('activityUpdated').textContent = formatRelativeTime(new Date(task.updated_at));
            }
        }
        
        if (task.deadline) {
            const deadlineDate = new Date(task.deadline);
            document.getElementById('taskDetailDeadline').textContent = formatDate(deadlineDate);
            updateDeadlineStatus(deadlineDate, task.completed);
        }
        
        // Progress dan Statistics
        updateProgressAndStats(task);
        
        // Activity untuk completed
        if (task.completed) {
            document.getElementById('activityCompletedItem').style.display = 'block';
            // Assuming completed time is updated_at when task is completed
            document.getElementById('activityCompleted').textContent = formatRelativeTime(new Date(task.updated_at));
        }
        
        // Update tombol actions
        updateActionButtons(task);
        
        // Setup event listeners untuk tombol
        setupActionButtons(task);
    }
    
    // Function untuk update status badge
    function updateStatusBadge(completed) {
        const badge = document.getElementById('taskStatusBadge');
        const icon = document.getElementById('taskStatusIcon');
        const text = document.getElementById('taskStatusText');
        
        if (completed) {
            badge.className = 'badge bg-success fs-6 px-3 py-2';
            icon.className = 'bi bi-check-circle me-1';
            text.textContent = 'Selesai';
        } else {
            badge.className = 'badge bg-warning fs-6 px-3 py-2';
            icon.className = 'bi bi-clock me-1';
            text.textContent = 'Dalam Progress';
        }
    }
    
    // Function untuk update deadline status
    function updateDeadlineStatus(deadlineDate, completed) {
        const now = new Date();
        const statusElement = document.getElementById('taskDeadlineStatus');
        const timeDiff = deadlineDate.getTime() - now.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        if (completed) {
            statusElement.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Selesai tepat waktu</span>';
        } else if (daysDiff < 0) {
            statusElement.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Terlambat ${Math.abs(daysDiff)} hari</span>`;
        } else if (daysDiff === 0) {
            statusElement.innerHTML = '<span class="text-warning"><i class="bi bi-clock me-1"></i>Deadline hari ini</span>';
        } else if (daysDiff <= 3) {
            statusElement.innerHTML = `<span class="text-warning"><i class="bi bi-clock me-1"></i>Deadline dalam ${daysDiff} hari</span>`;
        } else {
            statusElement.innerHTML = `<span class="text-info"><i class="bi bi-calendar-check me-1"></i>${daysDiff} hari lagi</span>`;
        }
    }
    
    // Function untuk update progress dan statistics
    function updateProgressAndStats(task) {
        const progressBar = document.querySelector('#taskProgressBar .progress-bar');
        const progressText = document.getElementById('taskProgressText');
        const progressPercent = document.getElementById('taskProgressPercent');
        const daysRemainingElement = document.getElementById('taskDaysRemaining');
        const totalDaysElement = document.getElementById('taskTotalDays');
        
        const now = new Date();
        const createdDate = new Date(task.created_at);
        const deadlineDate = new Date(task.deadline);
        
        const totalDays = Math.ceil((deadlineDate.getTime() - createdDate.getTime()) / (1000 * 3600 * 24));
        const daysRemaining = Math.ceil((deadlineDate.getTime() - now.getTime()) / (1000 * 3600 * 24));
        
        totalDaysElement.textContent = totalDays > 0 ? totalDays : 1;
        
        let progress = 0;
        let progressTextValue = 'Belum dimulai';
        let progressClass = 'bg-secondary';
        
        if (task.completed) {
            progress = 100;
            progressTextValue = 'Selesai';
            progressClass = 'bg-success';
            daysRemainingElement.textContent = '0';
        } else {
            const daysPassed = totalDays - daysRemaining;
            progress = totalDays > 0 ? Math.max(0, Math.min(100, (daysPassed / totalDays) * 100)) : 0;
            
            if (daysRemaining < 0) {
                progressTextValue = 'Terlambat';
                progressClass = 'bg-danger';
                daysRemainingElement.textContent = '0';
            } else if (daysRemaining === 0) {
                progressTextValue = 'Deadline hari ini';
                progressClass = 'bg-warning';
                daysRemainingElement.textContent = '0';
            } else {
                progressTextValue = 'Dalam progress';
                progressClass = 'bg-primary';
                daysRemainingElement.textContent = daysRemaining;
            }
        }
        
        progressBar.style.width = `${progress}%`;
        progressBar.className = `progress-bar ${progressClass}`;
        progressText.textContent = progressTextValue;
        progressPercent.textContent = `${Math.round(progress)}%`;
    }
    
    // Function untuk update action buttons
    function updateActionButtons(task) {
        const toggleBtn = document.getElementById('toggleFromDetailBtn');
        const toggleText = document.getElementById('toggleButtonText');
        
        if (task.completed) {
            toggleBtn.className = 'btn btn-outline-secondary';
            toggleBtn.querySelector('i').className = 'bi bi-arrow-clockwise me-1';
            toggleText.textContent = 'Tandai Belum Selesai';
        } else {
            toggleBtn.className = 'btn btn-success';
            toggleBtn.querySelector('i').className = 'bi bi-check-circle me-1';
            toggleText.textContent = 'Tandai Selesai';
        }
    }
    
    // Function untuk setup action buttons
    function setupActionButtons(task) {
        // Edit button
        editFromDetailBtn.onclick = function() {
            // Trigger edit modal
            setTimeout(() => {
                const editBtn = document.querySelector(`[data-bs-target="#editTaskModal"][data-id="${task.id}"]`);
                if (editBtn) {
                    editBtn.click();
                }
            }, 300);
        };
        
        // Toggle button
        toggleFromDetailBtn.onclick = function() {
            toggleTaskStatus(task.id);
        };
    }
    
    // Function untuk toggle status task
    async function toggleTaskStatus(taskId) {
        try {
            const response = await fetch(`/tasks/${taskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal mengubah status task');
            }
            
            const data = await response.json();
            
            // Update current task data
            currentTaskData.completed = data.completed ? 1 : 0;
            currentTaskData.updated_at = new Date().toISOString();
            
            // Re-populate detail
            populateTaskDetail(currentTaskData);
            
            // Show success message
            showSuccessToast(data.message || 'Status task berhasil diubah');
            
            // Optional: Reload page after delay untuk update list
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        } catch (error) {
            console.error('Error toggling task status:', error);
            showErrorToast('Gagal mengubah status task');
        }
    }
    
    // Helper functions
    function formatDescription(description) {
        return description.replace(/\n/g, '<br>');
    }
    
    function formatDateTime(date) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        return date.toLocaleDateString('id-ID', options);
    }
    
    function formatDate(date) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            timeZone: 'Asia/Jakarta'
        };
        return date.toLocaleDateString('id-ID', options);
    }
    
    function formatRelativeTime(date) {
        const now = new Date();
        const diffTime = now.getTime() - date.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
        const diffMinutes = Math.floor(diffTime / (1000 * 60));
        
        if (diffDays > 0) {
            return `${diffDays} hari yang lalu`;
        } else if (diffHours > 0) {
            return `${diffHours} jam yang lalu`;
        } else if (diffMinutes > 0) {
            return `${diffMinutes} menit yang lalu`;
        } else {
            return 'Baru saja';
        }
    }
    
    function showLoading(show) {
        if (show) {
            detailLoading.classList.remove('d-none');
            taskDetailContent.classList.add('d-none');
        } else {
            detailLoading.classList.add('d-none');
            taskDetailContent.classList.remove('d-none');
        }
    }
    
    function showError(message) {
        detailErrorMessage.textContent = message;
        detailErrorAlert.classList.remove('d-none');
        taskDetailContent.classList.add('d-none');
    }
    
    function hideError() {
        detailErrorAlert.classList.add('d-none');
    }
    
    function showSuccessToast(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }
    
    function showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }
    
    // Reset modal ketika ditutup
    taskDetailModal.addEventListener('hidden.bs.modal', function() {
        currentTaskData = null;
        hideError();
        showLoading(true);
    });
});