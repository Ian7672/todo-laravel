// Elemen input pencarian dan daftar tugas
const searchInput = document.getElementById('search');
const taskList = document.getElementById('taskList');

// Tambahkan event listener untuk pencarian realtime
searchInput.addEventListener('input', function () {
  const searchTerm = this.value.toLowerCase();

  // Loop semua elemen <li> dalam daftar tugas
  [...taskList.children].forEach(li => {
    const title = li.querySelector('.task-title-truncate')?.textContent.toLowerCase() || '';
    const description = li.querySelector('.task-description-preview')?.textContent.toLowerCase() || '';

    // Tampilkan hanya jika judul atau deskripsi mengandung kata pencarian
    if (title.includes(searchTerm) || description.includes(searchTerm)) {
      li.style.display = '';
    } else {
      li.style.display = 'none';
    }
  });
});
