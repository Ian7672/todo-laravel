<!-- Modal Add Task -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTaskModalLabel">
          <i class="bi bi-plus-circle me-2"></i>Tambah Tugas
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      
      <form id="addTaskForm" method="POST" action="/tasks">
        @csrf
        <div class="modal-body">
          <div id="addErrors" class="error-list"></div>
          
          <!-- Task Title -->
          <div class="mb-3">
            <label for="addTitle" class="form-label">
              <i class="bi bi-card-text me-1"></i>Judul Tugas <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control" 
              id="addTitle" 
              name="title" 
              placeholder="Masukkan judul tugas..."
              required 
              maxlength="255"
            />
            <div class="form-text">Maksimal 255 karakter</div>
          </div>

          <!-- Task Description -->
          <div class="mb-3">
            <label for="addDescription" class="form-label">
              <i class="bi bi-text-paragraph me-1"></i>Deskripsi
            </label>
            <textarea 
              class="form-control" 
              id="addDescription" 
              name="description" 
              rows="4" 
              placeholder="Masukkan deskripsi tugas (opsional)..."
              maxlength="1000"
            ></textarea>
            <div class="form-text">Maksimal 1000 karakter</div>
          </div>

          <!-- Deadline -->
          <div class="mb-3">
            <label for="addDeadline" class="form-label">
              <i class="bi bi-calendar-event me-1"></i>Tanggal Deadline <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              name="deadline" 
              id="addDeadline" 
              class="form-control" 
              required
            >
            <div class="form-text">Pilih tanggal deadline untuk tugas ini</div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
