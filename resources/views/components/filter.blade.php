<!-- resources/views/components/filter.blade.php -->

<form method="GET" class="sort-form mb-3">
    <select name="sort_by" class="form-select" onchange="this.form.submit()">
        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
        <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Tanggal Diperbarui</option>
        <option value="deadline" {{ request('sort_by') === 'deadline' ? 'selected' : '' }}>Tanggal Deadline</option>
        <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Judul</option>
    </select>

    <select name="sort_dir" class="form-select" onchange="this.form.submit()">
        <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Naik</option>
        <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>Turun</option>
    </select>
</form>