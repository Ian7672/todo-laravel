<!-- Modal Detail Task -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taskDetailModalLabel">
          <i class="bi bi-info-circle me-2"></i>Detail Task
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <!-- Loading Spinner -->
        <div id="detailLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Memuat...</span>
          </div>
          <div class="mt-2">Memuat detail task...</div>
        </div>
        
        <!-- Error Alert -->
        <div id="detailErrorAlert" class="alert alert-danger d-none" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <span id="detailErrorMessage">Gagal memuat detail task</span>
        </div>
        
        <!-- Task Detail Content -->
        <div id="taskDetailContent" class="d-none">
          <!-- Task Status Badge -->
          <div class="mb-3">
            <span id="taskStatusBadge" class="badge fs-6 px-3 py-2">
              <i id="taskStatusIcon" class="me-1"></i>
              <span id="taskStatusText">Status</span>
            </span>
          </div>
          
          <!-- Task Title -->
          <div class="mb-4">
            <h4 class="task-detail-title mb-2" id="taskDetailTitle">Judul Task</h4>
            <small class="text-muted">
              <i class="bi bi-hash me-1"></i>ID: <span id="taskDetailId">#001</span>
            </small>
          </div>
          
          <!-- Task Description -->
          <div class="mb-4">
            <h6 class="detail-section-title">
              <i class="bi bi-text-paragraph me-2"></i>Deskripsi
            </h6>
            <div class="detail-content">
              <div id="taskDetailDescription" class="task-description-full">
                Tidak ada deskripsi
              </div>
            </div>
          </div>
          
          <!-- Task Dates -->
          <div class="mb-4">
            <h6 class="detail-section-title">
              <i class="bi bi-calendar me-2"></i>Informasi Tanggal
            </h6>
            <div class="row">
              <div class="col-md-4">
                <div class="detail-item">
                  <label class="detail-label">
                    <i class="bi bi-calendar-plus text-success me-1"></i>Dibuat
                  </label>
                  <div id="taskDetailCreated" class="detail-value">-</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="detail-item">
                  <label class="detail-label">
                    <i class="bi bi-calendar-check text-info me-1"></i>Diperbarui
                  </label>
                  <div id="taskDetailUpdated" class="detail-value">-</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="detail-item">
                  <label class="detail-label">
                    <i class="bi bi-calendar-event text-warning me-1"></i>Deadline
                  </label>
                  <div id="taskDetailDeadline" class="detail-value">-</div>
                  <small id="taskDeadlineStatus" class="deadline-status"></small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Task Progress -->
          <div class="mb-4">
            <h6 class="detail-section-title">
              <i class="bi bi-graph-up me-2"></i>Progress
            </h6>
            <div class="detail-content">
              <div id="taskProgressBar" class="progress mb-2" style="height: 8px;">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
              </div>
              <div class="d-flex justify-content-between">
                <small id="taskProgressText" class="text-muted">Belum dimulai</small>
                <small id="taskProgressPercent" class="text-muted">0%</small>
              </div>
            </div>
          </div>
          
          <!-- Task Statistics -->
          <div class="mb-4">
            <h6 class="detail-section-title">
              <i class="bi bi-bar-chart me-2"></i>Statistik
            </h6>
            <div class="row">
              <div class="col-6">
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                  </div>
                  <div class="stat-content">
                    <div class="stat-number" id="taskDaysRemaining">-</div>
                    <div class="stat-label">Hari tersisa</div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-calendar-date"></i>
                  </div>
                  <div class="stat-content">
                    <div class="stat-number" id="taskTotalDays">-</div>
                    <div class="stat-label">Total hari</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Task Actions Summary -->
          <div class="mb-3">
            <h6 class="detail-section-title">
              <i class="bi bi-activity me-2"></i>Riwayat Aktivitas
            </h6>
            <div class="detail-content">
              <div class="activity-timeline">
                <div class="activity-item">
                  <div class="activity-icon created">
                    <i class="bi bi-plus-circle"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">Task dibuat</div>
                    <div class="activity-time" id="activityCreated">-</div>
                  </div>
                </div>
                <div class="activity-item" id="activityUpdatedItem" style="display: none;">
                  <div class="activity-icon updated">
                    <i class="bi bi-pencil-square"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">Task terakhir diperbarui</div>
                    <div class="activity-time" id="activityUpdated">-</div>
                  </div>
                </div>
                <div class="activity-item" id="activityCompletedItem" style="display: none;">
                  <div class="activity-icon completed">
                    <i class="bi bi-check-circle"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">Task diselesaikan</div>
                    <div class="activity-time" id="activityCompleted">-</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Tutup
        </button>
        <button type="button" class="btn btn-primary" id="editFromDetailBtn" data-bs-dismiss="modal">
          <i class="bi bi-pencil-square me-1"></i>Edit Task
        </button>
        <button type="button" class="btn btn-success" id="toggleFromDetailBtn">
          <i class="bi bi-check-circle me-1"></i>
          <span id="toggleButtonText">Tandai Selesai</span>
        </button>
      </div>
    </div>
  </div>
</div>