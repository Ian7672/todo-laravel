document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox toggle
    const checkboxes = document.querySelectorAll('.checkbox-form input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            // Kita HAPUS preventDefault() agar checkbox berubah secara visual dulu (native behavior)
            // e.preventDefault(); 
            
            const form = this.closest('.checkbox-form');
            // Pastikan kita mengambil ID yang benar. HTML menggunakan data-id pada li
            const taskItem = form.closest('.task-item');
            const taskId = taskItem.dataset.id;
            const isChecked = this.checked; // Status baru setelah diklik (true/false)
            
            // Update UI immediately (Optimistic UI)
            const taskTitle = taskItem.querySelector('.task-title-truncate');
            const statusBadge = taskItem.querySelector('.status-badge');
            const taskDates = taskItem.querySelector('.task-dates'); // Perbaikan selektor (sebelumnya getElementsByClassName returns collection)
            
            if (isChecked) {
                taskTitle.classList.add('completed');
                taskItem.classList.add('completed-task'); // Tambahan class untuk styling parent
                if (statusBadge) statusBadge.style.display = 'inline';
                if (taskDates) taskDates.style.opacity = '0.5';
            } else {
                taskTitle.classList.remove('completed');
                taskItem.classList.remove('completed-task');
                if (statusBadge) statusBadge.style.display = 'none';
                if (taskDates) taskDates.style.opacity = '1';
            }
            
            // Send AJAX request
            fetch(`/tasks/${taskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    completed: isChecked
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Reorder tasks only if successful
                    // Optional: Delay reorder slightly for visual feedback
                    setTimeout(() => {
                        reorderTasks();
                    }, 300);
                } else {
                    throw new Error(data.message || 'Gagal memperbarui status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox state if failed
                this.checked = !isChecked; // Kembalikan ke status sebelumnya
                
                // Revert UI changes
                if (!isChecked) { // Kita revert ke checked
                    taskTitle.classList.add('completed');
                    taskItem.classList.add('completed-task');
                    if (statusBadge) statusBadge.style.display = 'inline';
                    if (taskDates) taskDates.style.opacity = '0.5';
                } else { // Kita revert ke unchecked
                    taskTitle.classList.remove('completed');
                    taskItem.classList.remove('completed-task');
                    if (statusBadge) statusBadge.style.display = 'none';
                    if (taskDates) taskDates.style.opacity = '1';
                }
                alert('Gagal menyimpan status tugas. Periksa koneksi internet Anda.');
            });
        });
    });
    
    // Function to reorder tasks (completed tasks go to bottom)
    function reorderTasks() {
        const taskList = document.getElementById('taskList');
        if (!taskList) return;
        
        const taskItems = Array.from(taskList.querySelectorAll('.task-item'));
        
        // Sort tasks: incomplete first, then completed
        taskItems.sort((a, b) => {
            const aCheckbox = a.querySelector('input[type="checkbox"]');
            const bCheckbox = b.querySelector('input[type="checkbox"]');
            
            const aCompleted = aCheckbox ? aCheckbox.checked : false;
            const bCompleted = bCheckbox ? bCheckbox.checked : false;
            
            if (aCompleted === bCompleted) return 0;
            return aCompleted ? 1 : -1; // Completed (true) goes to bottom
        });
        
        // Reappend sorted items (ini akan memindahkan elemen di DOM)
        taskItems.forEach(item => {
            taskList.appendChild(item);
        });
    }
    
    // Initial reorder on page load
    reorderTasks();
});
