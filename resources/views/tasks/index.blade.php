<!DOCTYPE html>
<html lang="id">
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Task Manager</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="css/tasks.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <div class="container">
    @include('components.profile')
    
    <div>
      @include('components.filter')
    </div>

    @include('components.search')
    @include('components.tasks')
  </div>

  @include('modal.add')
  @include('modal.edit')
  @include('modal.detail')

  <script src="{{ asset('js/components/date.js') }}"></script>
  <script src="{{ asset('js/components/search.js') }}"></script>
  <script src="{{ asset('js/components/delete.js') }}"></script>
  <script src="{{ asset('js/components/edit.js') }}"></script>
  <script src="{{ asset('js/components/detail.js') }}"></script>
  <script src="{{ asset('js/components/toggle.js') }}"></script>
</body>
</html>