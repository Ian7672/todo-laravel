<!DOCTYPE html>
<html lang="id">
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Task Manager</title>
<link rel="stylesheet" href="css/index.css">

<style>
/* Custom styles for completed tasks */
.task-item.completed-task {
  background-color: #f8f9fa;
  border-left: 4px solid #198754;
  opacity: 0.8;
}

.task-item.completed-task .task-title-truncate {
  text-decoration: line-through;
  color: #6c757d;
}

.task-item.completed-task .task-description-preview {
  color: #6c757d;
  font-style: italic;
}

.completed-badge {
  background-color: #198754;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  margin-left: 8px;
}

.user-profile {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 10px;
  background-color: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #dee2e6;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #007bff;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  margin-right: 12px;
}

.user-info {
  flex-grow: 1;
}

.user-name {
  font-weight: 600;
  margin: 0;
  color: #333;
}

.user-email {
  font-size: 0.9rem;
  color: #6c757d;
  margin: 0;
}

.logout-section {
  margin-left: auto;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .user-profile {
    background-color: var(--surface, #333);
    border-color: var(--border, #555);
  }
  
  .user-name {
    color: var(--text, #fff);
  }
  
  .task-item.completed-task {
    background-color: rgba(255, 255, 255, 0.05);
  }
}
</style>

</head>
<body>
  <div class="container">

    <!-- User Profile Section -->
    @if (auth()->check())
    <div class="user-profile">
      <div class="user-avatar">
        {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
      </div>
      <div class="user-info">
        <p class="user-name">{{ auth()->user()->name ?? 'Pengguna' }}</p>
        <p class="user-email">{{ auth()->user()->email }}</p>
      </div>
      <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-sm" title="Logout">
            <i class="bi bi-box-arrow-right"></i> Keluar
          </button>
        </form>
      </div>
    </div>
    @endif

    <div>
<form method="GET" class="sort-form mb-3">
    <select name="sort_by" class="form-select" onchange="this.form.submit()">
        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
        <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Tanggal Diperbarui</option>
        <option value="deadline" {{ request('sort_by') === 'deadline' ? 'selected' : '' }}>Tanggal Deadline</option>
        <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Judul</option>
        <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
    </select>

    <select name="sort_dir" class="form-select" onchange="this.form.submit()">
        <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Naik</option>
        <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>Turun</option>
    </select>
</form>

    </div>

    <div class="search-box">
      <input type="text" id="search" placeholder="Cari tugas..." autocomplete="off" />
    </div>

    <div class="buttons-row">
      <button id="btnAddTask" data-bs-toggle="modal" data-bs-target="#addTaskModal" type="button">Tambah Tugas</button>
    </div>

    <ul id="taskList">
      @foreach ($tasks as $task)
<li class="task-item {{ $task->completed ? 'completed-task' : '' }}" data-id="{{ $task->id }}" data-completed="{{ $task->completed ? 'true' : 'false' }}">
    <div class="task-left">
        <div class="task-info">
            <input type="checkbox" class="toggle-completed" {{ $task->completed ? 'checked' : '' }} />
            <span class="task-title-truncate {{ $task->completed ? 'completed' : '' }}" title="{{ $task->title }}">
                {{ $task->title }}
            </span>
            @if($task->completed)
                <span class="completed-badge">Selesai</span>
            @endif
            <span class="more-btn" data-bs-toggle="modal" data-bs-target="#taskDetailModal" data-id="{{ $task->id }}">More</span>
        </div>
        @if($task->description)
        <span class="task-description-preview">{{ Str::limit($task->description, 50) }}</span>
        @endif
    </div>
    <div class="task-dates">
        <div>Dibuat: {{ \Carbon\Carbon::parse($task->created_at)->format('d M Y H:i') }}</div>
        <div>Diperbarui: {{ \Carbon\Carbon::parse($task->updated_at)->format('d M Y H:i') }}</div>
        <div>Deadline: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</div>
    </div>
    <div class="actions">
        <button class="edit-btn btn btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal"
            data-id="{{ $task->id }}" 
            data-title="{{ htmlspecialchars($task->title, ENT_QUOTES) }}"
            data-description="{{ htmlspecialchars($task->description ?? '', ENT_QUOTES) }}"
            data-deadline="{{ $task->deadline }}">Edit</button>
<form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;" onsubmit="return false;">
    @csrf
    @method('DELETE')
    <button type="button" class="delete-btn btn btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="{{ $task->id }}" data-title="{{ htmlspecialchars($task->title, ENT_QUOTES) }}">Hapus</button>
</form>


    </div>
</li>
      @endforeach
    </ul>

  </div>

  <!-- Modal Add Task -->
  <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background-color: var(--surface); color: var(--text);">
        <div class="modal-header">
          <h5 class="modal-title" id="addTaskModalLabel">Tambah Tugas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form id="addTaskForm" method="POST" action="/tasks">
          @csrf
          <div class="modal-body">
            <div id="addErrors" class="error-list"></div>
            <div class="mb-3">
              <label for="addTitle" class="form-label">Judul Tugas</label>
              <input type="text" class="form-control" id="addTitle" name="title" required />
            </div>
            <div class="mb-3">
              <label for="addDescription" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="addDescription" name="description" rows="3"></textarea>
            </div>
<div class="mb-3">
  <label for="addDeadline" class="form-label">Tanggal Deadline</label>
  <input type="date" name="deadline" id="addDeadline" class="form-control" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy" required>
</div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Tambah</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Task -->
  <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background-color: var(--surface); color: var(--text);">
        <div class="modal-header">
          <h5 class="modal-title" id="editTaskModalLabel">Edit Tugas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form id="editTaskForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div id="editErrors" class="error-list"></div>
            <div class="mb-3">
              <label for="editTitle" class="form-label">Judul Tugas</label>
              <input type="text" class="form-control" id="editTitle" name="title" required />
            </div>
            <div class="mb-3">
              <label for="editDescription" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
            </div>
<div class="mb-3">
  <label for="editDeadline" class="form-label">Tanggal Deadline</label>
  <input type="date" name="deadline" id="editDeadline" class="form-control" required>
</div>


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
<!-- Modal untuk menampilkan detail tugas -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--surface); color: var(--text);">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel">Detail Tugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <h5>Judul:</h5>
                <p id="taskFullTitle"></p>
                
                <div id="taskDescriptionContainer">
                    <h5>Deskripsi:</h5>
                    <div class="description-container">
                        <p id="taskDescription"></p>
                    </div>
                </div>
                
                <h5>Status:</h5>
                <p id="taskStatus"></p>
                
                <h5>Informasi Waktu:</h5>
                <p id="taskCreatedAt"></p> <!-- Tanggal Dibuat -->
                <p id="taskUpdatedAt"></p> <!-- Tanggal Diperbarui -->
                <p id="taskDeadline"></p> <!-- Tanggal Deadline -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--surface); color: var(--text);">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas ini "<span id="taskNameToDelete"></span>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>


<script>

// Function to get today's date in YYYY-MM-DD format
function getTodayDate() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}

// Function to set minimum date for date inputs
function setMinimumDate() {
  const today = getTodayDate();
  document.getElementById('addDeadline').min = today;
  document.getElementById('editDeadline').min = today;
}

// Function to reorder tasks - completed tasks go to bottom
function reorderTasks() {
  const taskList = document.getElementById('taskList');
  const tasks = Array.from(taskList.children);
  
  // Separate completed and incomplete tasks
  const incompleteTasks = tasks.filter(task => task.dataset.completed === 'false');
  const completedTasks = tasks.filter(task => task.dataset.completed === 'true');
  
  // Clear the list and append incomplete tasks first, then completed tasks
  taskList.innerHTML = '';
  incompleteTasks.forEach(task => taskList.appendChild(task));
  completedTasks.forEach(task => taskList.appendChild(task));
}

// Menangani tombol hapus di modal konfirmasi
const confirmDeleteButton = document.getElementById('confirmDeleteButton');
let taskIdToDelete = null;
let taskNameToDelete = '';

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        taskIdToDelete = this.getAttribute('data-id'); // Ambil ID tugas yang akan dihapus
        taskNameToDelete = this.getAttribute('data-title'); // Ambil nama tugas yang akan dihapus
        document.getElementById('taskNameToDelete').textContent = taskNameToDelete; // Tampilkan nama tugas di modal
    });
});

confirmDeleteButton.addEventListener('click', function() {
    if (taskIdToDelete) {
        // Kirim permintaan hapus ke server
        fetch(`/tasks/${taskIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => {
           // Refresh halaman setelah berhasil
    window.location.reload();
            if (response.ok) {
                // Hapus elemen tugas dari DOM
                const taskItem = document.querySelector(`li.task-item[data-id="${taskIdToDelete}"]`);
                if (taskItem) {
                    taskItem.remove();
                }
                // Tutup modal konfirmasi
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
            } else {
               // Refresh halaman setelah berhasil
    window.location.reload();
                //gagal hapus
            }
        })
        .catch(() => {
           // Refresh halaman setelah berhasil
    window.location.reload();
            //gagal hapus
        });
    }
});




  // Script yang perlu ditambahkan untuk form tambah task
document.getElementById('addTaskForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = this;
  const url = form.action;
  const formData = new FormData(form);

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: formData,
    });
    
    if (!response.ok) {
      if (response.status === 422) {
        const data = await response.json();
        let errorHtml = '<ul>';
        for (const key in data.errors) {
          data.errors[key].forEach(msg => { errorHtml += `<li>${msg}</li>`; });
        }
        errorHtml += '</ul>';
        document.getElementById('addErrors').innerHTML = errorHtml;
      } else {
        alert('Terjadi kesalahan server. Silakan coba lagi.');
      }
      return;
    }
    
    // Refresh halaman setelah berhasil
    window.location.reload();
  } catch {
    alert('Gagal menyimpan tugas baru. Silakan coba lagi.');
  }
});

function mores() {
  // Memeriksa elemen .task-title-truncate
  document.querySelectorAll('.task-title-truncate').forEach(titleElem => {
    const fullTextTitle = titleElem.textContent.trim();
    const moreBtn = titleElem.nextElementSibling?.nextElementSibling; // Skip completed badge if exists

    // Memeriksa elemen .task-description-preview yang bersangkutan
    const descElem = titleElem.closest('li').querySelector('.task-description-preview');
    const fullTextDesc = descElem ? descElem.textContent.trim() : '';

    // Logika untuk menampilkan tombol More
    if (fullTextTitle.length > 20 || fullTextDesc.length > 20) {
      const truncatedTitle = fullTextTitle.length > 20 ? fullTextTitle.slice(0, 20) + '…' : fullTextTitle;
      titleElem.textContent = truncatedTitle;
      titleElem.setAttribute('data-fulltext', fullTextTitle); // simpan teks lengkap

      // Tampilkan tombol More
      if (moreBtn && moreBtn.classList.contains('more-btn')) {
        moreBtn.style.display = 'inline';
      }
    } else {
      // Sembunyikan tombol More jika tidak ada pemotongan
      if (moreBtn && moreBtn.classList.contains('more-btn')) {
        moreBtn.style.display = 'none';
      }
    }
  });
}

// Panggil fungsi saat halaman dimuat
mores();


// Modal Task Detail: isi form sesuai data element button click
const taskDetailModal = document.getElementById('taskDetailModal');
taskDetailModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget; // Tombol yang diklik
    const taskId = button.getAttribute('data-id'); // Ambil ID tugas

    // Lakukan fetch untuk mendapatkan detail tugas dari server
    fetch(`/tasks/${taskId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('taskFullTitle').textContent = data.title;
            
            // Handle description field
            const descriptionContainer = document.getElementById('taskDescriptionContainer');
            const descriptionText = document.getElementById('taskDescription');
            
            if (data.description && data.description.trim() !== '') {
                descriptionContainer.style.display = 'block';
                descriptionText.textContent = data.description;
            } else {
                descriptionContainer.style.display = 'none';
            }
            
            document.getElementById('taskStatus').textContent = data.completed ? 'Selesai' : 'Belum Selesai';
            document.getElementById('taskCreatedAt').textContent = `Dibuat: ${new Date(data.created_at).toLocaleString()}`; 
            document.getElementById('taskUpdatedAt').textContent = `Diperbarui: ${new Date(data.updated_at).toLocaleString()}`; 
            document.getElementById('taskDeadline').textContent = `Deadline: ${new Date(data.deadline).toLocaleDateString()}`; 
        })
        .catch(error => {
            console.error('Error fetching task details:', error);
        });
});

// Menangani checkbox toggle completed
document.querySelectorAll('input.toggle-completed').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const li = this.closest('li.task-item');
      const taskId = li.dataset.id;
      const completed = this.checked;

      // Update data attribute
      li.dataset.completed = completed ? 'true' : 'false';

      // Toggle class completed untuk efek teks langsung
      const titleElem = li.querySelector('.task-title-truncate');
      const taskInfo = li.querySelector('.task-info');
      
      if (completed) {
        titleElem.classList.add('completed');
        li.classList.add('completed-task');
        
        // Add completed badge if not exists
        let badge = taskInfo.querySelector('.completed-badge');
        if (!badge) {
          badge = document.createElement('span');
          badge.className = 'completed-badge';
          badge.textContent = 'Selesai';
          titleElem.insertAdjacentElement('afterend', badge);
        }
      } else {
        titleElem.classList.remove('completed');
        li.classList.remove('completed-task');
        
        // Remove completed badge
        const badge = taskInfo.querySelector('.completed-badge');
        if (badge) {
          badge.remove();
        }
      }

      // AJAX PATCH update status completed di server
      fetch(`/tasks/${taskId}/toggle`, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ completed }),
      })
      .then(() => {
        // Reorder tasks after successful update
        setTimeout(reorderTasks, 300); // Small delay for smooth transition
      })
      .catch(() => {
        // Jika gagal, rollback UI checkbox dan kelas completed
        this.checked = !completed;
        li.dataset.completed = completed ? 'false' : 'true';
        
        if (completed) {
          titleElem.classList.remove('completed');
          li.classList.remove('completed-task');
          const badge = taskInfo.querySelector('.completed-badge');
          if (badge) badge.remove();
        } else {
          titleElem.classList.add('completed');
          li.classList.add('completed-task');
          if (!taskInfo.querySelector('.completed-badge')) {
            const badge = document.createElement('span');
            badge.className = 'completed-badge';
            badge.textContent = 'Selesai';
            titleElem.insertAdjacentElement('afterend', badge);
          }
        }
        alert('Gagal memperbarui status tugas');
      });
    });
  });


  
  // Modal Edit Task: isi form sesuai data element button click
const editModal = document.getElementById('editTaskModal');
editModal.addEventListener('show.bs.modal', function(event) {
  const button = event.relatedTarget;
  if (!button) return;
  const id = button.getAttribute('data-id');
  const title = button.getAttribute('data-title');
  const description = button.getAttribute('data-description') || '';
  const deadline = button.getAttribute('data-deadline');

  // Format tanggal deadline menjadi yyyy-mm-dd untuk input type="date"
  let formattedDeadline = '';
  if (deadline) {
    const deadlineDate = new Date(deadline);
    const yyyy = deadlineDate.getFullYear();
    const mm = String(deadlineDate.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
    const dd = String(deadlineDate.getDate()).padStart(2, '0');
    formattedDeadline = `${yyyy}-${mm}-${dd}`; // Format untuk input date
  }

  const form = editModal.querySelector('form');
  form.action = `/tasks/${id}`;
  form.querySelector('#editTitle').value = title;
  form.querySelector('#editDescription').value = description;
  form.querySelector('#editDeadline').value = formattedDeadline;

  document.getElementById('editErrors').innerHTML = '';
  
  // Set minimum date for edit modal
  setMinimumDate();
});







  // Form edit via AJAX
  document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const url = form.action;
    const formData = new FormData(form);

    const token = form.querySelector('input[name="_token"]').value;
    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput ? methodInput.value : 'PUT';

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'X-HTTP-Method-Override': method,
          'Accept': 'application/json',
        },
        body: formData,
      });
      if (!response.ok) {
        if (response.status === 422) {
          const data = await response.json();
          let errorHtml = '<ul>';
          for (const key in data.errors) {
            data.errors[key].forEach(msg => { errorHtml += `<li>${msg}</li>`; });
          }
          errorHtml += '</ul>';
          document.getElementById('editErrors').innerHTML = errorHtml;
        } else {
          alert('Terjadi kesalahan server. Silakan coba lagi.');
        }
        return;
      }
      const data = await response.json();
      const li = document.querySelector(`li.task-item[data-id="${data.id}"]`);
      if (li) {
        li.querySelector('.task-title-truncate').textContent = data.title;
        
        // Update description preview in the list
        let descPreview = li.querySelector('.task-description-preview');
        if (data.description) {
          if (!descPreview) {
            descPreview = document.createElement('span');
            descPreview.className = 'task-description-preview';
            li.querySelector('.task-left').appendChild(descPreview);
          }
          descPreview.textContent = data.description.length > 50 ? 
            data.description.substring(0, 50) + '...' : data.description;
        } else if (descPreview) {
          descPreview.remove();
        }
        
        li.querySelector('.task-dates').innerHTML = `
          <div>Dibuat: ${new Date(data.created_at).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })}</div>
          <div>Diperbarui: ${new Date(data.updated_at).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })}</div>
          <div>Deadline: ${new Date(data.deadline).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })}</div>
        `;
        
        // Update data attributes for edit button
        const editBtn = li.querySelector('.edit-btn');
        editBtn.setAttribute('data-title', data.title);
        editBtn.setAttribute('data-description', data.description || '');
        editBtn.setAttribute('data-deadline', data.deadline);
      }
      bootstrap.Modal.getInstance(editModal).hide();
        // Panggil fungsi mores() setelah menyimpan perubahan
        mores();
    } catch {
      alert('Gagal menyimpan perubahan. Silakan coba lagi.');
    }
  });

  // Pencarian realtime filter on task list
  const searchInput = document.getElementById('search');
  const taskList = document.getElementById('taskList');
  searchInput.addEventListener('input', function() {
    const term = this.value.toLowerCase();
    [...taskList.children].forEach(li => {
      const title = li.querySelector('.task-title-truncate').textContent.toLowerCase();
      const description = li.querySelector('.task-description-preview')?.textContent.toLowerCase() || '';
      li.style.display = title.includes(term) || description.includes(term) ? '' : 'none';
    });
  });




  // Untuk mengubah format tampilan tanggal pada dialog tambah (setelah DOM selesai dimuat)
document.addEventListener('DOMContentLoaded', function() {
  // Set minimum date for date inputs
  setMinimumDate();
  
  // Format tanggal untuk dialog tambah
  const addDeadlineInput = document.getElementById('addDeadline');
  if (addDeadlineInput) {
    // Set default value ke tanggal hari ini dengan format YYYY-MM-DD
    const today = getTodayDate();
    addDeadlineInput.value = today;
    
    // Tambahkan placeholder untuk menunjukkan format yang diinginkan
    addDeadlineInput.setAttribute('placeholder', 'dd/mm/yyyy');
  }
  
  // Atur locale untuk semua input tanggal
  document.querySelectorAll('input[type="date"]').forEach(input => {
    input.setAttribute('lang', 'id');
  });
  
  // Initial task reordering on page load
  reorderTasks();
});

// Also set minimum date when modals are opened
document.getElementById('addTaskModal').addEventListener('show.bs.modal', function() {
  setMinimumDate();
});