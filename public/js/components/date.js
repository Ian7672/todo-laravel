// Atur tanggal minimum dan nilai default untuk input tanggal
document.addEventListener('DOMContentLoaded', function () {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  const todayString = `${yyyy}-${mm}-${dd}`;

  // Input tanggal di form tambah
  const addDeadline = document.getElementById('addDeadline');
  if (addDeadline) {
    addDeadline.setAttribute('min', todayString);
    addDeadline.value = todayString;
  }

  // Input tanggal di form edit
  const editDeadline = document.getElementById('editDeadline');
  if (editDeadline) {
    editDeadline.setAttribute('min', todayString);
  }
});
