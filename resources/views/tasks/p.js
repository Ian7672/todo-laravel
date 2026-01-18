
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

function mbtn() {

}

function mores() {
  // Memeriksa elemen .task-title-truncate
  document.querySelectorAll('.task-title-truncate').forEach(titleElem => {
    const fullTextTitle = titleElem.textContent.trim();
    const moreBtn = titleElem.nextElementSibling; // Pastikan ini tombol More

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
       sortTasksByCompletion();
      const li = this.closest('li.task-item');
      const taskId = li.dataset.id;
      const completed = this.checked;

      // Toggle class completed untuk efek teks langsung
      const titleElem = li.querySelector('.task-title-truncate');
      if (completed) {
        titleElem.classList.add('completed');
      } else {
        titleElem.classList.remove('completed');
      }

      // AJAX PATCH update status completed di server
      fetch(`/tasks/${taskId}/toggle`, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ completed }),
      }).catch(() => {
        // Jika gagal, rollback UI checkbox dan kelas completed
        this.checked = !completed;
        if (completed) titleElem.classList.remove('completed');
        else titleElem.classList.add('completed');
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
  // Format tanggal untuk dialog tambah
  const addDeadlineInput = document.getElementById('addDeadline');
  if (addDeadlineInput) {
    // Set default value ke tanggal hari ini dengan format YYYY-MM-DD
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    addDeadlineInput.value = `${yyyy}-${mm}-${dd}`;
    
    // Tambahkan placeholder untuk menunjukkan format yang diinginkan
    addDeadlineInput.setAttribute('placeholder', 'dd/mm/yyyy');
  }
  
  // Atur locale untuk semua input tanggal
  document.querySelectorAll('input[type="date"]').forEach(input => {
    input.setAttribute('lang', 'id');
  });
});