<!-- resources/views/components/profile.blade.php -->

@if (auth()->check())
<div class="user-profile">
  <div class="user-avatar">
    {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
  </div>
  <div class="user-info">
    <p class="user-name">{{ auth()->user()->username ?? 'Pengguna' }}</p>
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
