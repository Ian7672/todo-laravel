<!-- Modal Edit Task -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTaskModalLabel">
          <i class="bi bi-pencil-square me-2"></i>Edit Task
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="editTaskForm" method="POST" action="">
        @csrf
        @method('PUT')
        
        <div class="modal-body">
          <!-- Alert for validation errors -->
          <div id="editErrorAlert" class="alert alert-danger d-none" role="alert">
            <ul id="editErrorList" class="mb-0"></ul>
          </div>
          
          <!-- Task Title -->
          <div class="mb-3">
            <label for="editTaskTitle" class="form-label">
              <i class="bi bi-card-text me-1"></i>Judul Task <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control" 
              id="editTaskTitle" 
              name="title" 
              placeholder="Masukkan judul task..."
              required
              maxlength="255"
            >
            <div class="form-text">Maksimal 255 karakter</div>
          </div>
          
          <!-- Task Description -->
          <div class="mb-3">
            <label for="editTaskDescription" class="form-label">
              <i class="bi bi-text-paragraph me-1"></i>Deskripsi
            </label>
            <textarea 
              class="form-control" 
              id="editTaskDescription" 
              name="description" 
              rows="4"
              placeholder="Masukkan deskripsi task (opsional)..."
              maxlength="1000"
            ></textarea>
            <div class="form-text">Maksimal 1000 karakter</div>
          </div>
          
          <!-- Deadline -->
          <div class="mb-3">
            <label for="editTaskDeadline" class="form-label">
              <i class="bi bi-calendar-event me-1"></i>Deadline <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              class="form-control" 
              id="editTaskDeadline" 
              name="deadline" 
              required
            >
            <div class="form-text">Pilih tanggal deadline untuk task ini</div>
          </div>
          
          <!-- Task Status -->
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-check-circle me-1"></i>Status Task
            </label>
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                id="editTaskCompleted" 
                name="completed"
                value="1"
              >
              <label class="form-check-label" for="editTaskCompleted">
                Tandai sebagai selesai
              </label>
            </div>
          </div>
          
          <!-- Task Info (Read-only) -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted">
                  <i class="bi bi-calendar-plus me-1"></i>Dibuat
                </label>
                <input 
                  type="text" 
                  class="form-control-plaintext" 
                  id="editTaskCreated" 
                  readonly
                >
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted">
                  <i class="bi bi-calendar-check me-1"></i>Terakhir Diperbarui
                </label>
                <input 
                  type="text" 
                  class="form-control-plaintext" 
                  id="editTaskUpdated" 
                  readonly
                >
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>