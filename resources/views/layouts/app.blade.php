<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Task Manager') }}</title>
  <!-- Bootstrap 4 CDN (no SRI here to avoid integrity mismatch blocking the resource) -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="{{ route('tasks.index') }}">{{ config('app.name', 'Task Manager') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link btn btn-sm btn-primary text-white" href="{{ route('tasks.create') }}">Create Task</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      @if(session('success'))
        <div class="alert alert-success" role="status" aria-live="polite">{{ session('success') }}</div>
      @endif

      <!-- JS alerts (for optimistic updates / AJAX errors) -->
      <div id="jsAlertContainer" aria-live="polite" aria-atomic="true" class="mb-3"></div>

      @yield('content')
    </div>

  <!-- jQuery, Popper.js, and Bootstrap JS (CDN) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @yield('scripts')
  </body>
</html>

