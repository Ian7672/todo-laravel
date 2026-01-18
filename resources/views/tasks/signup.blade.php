<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar</title>
<link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="form-container">
        <h2>Daftar Akun</h2>
        <form action="{{ route('signup') }}" method="POST">
            @csrf
            <label>Username</label>
            <input type="text" name="username" required value="{{ old('username') }}">
            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror

            <label>Email</label>
            <input type="email" name="email" required value="{{ old('email') }}">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label>Password</label>
            <input type="password" name="password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">Daftar</button>
        </form>
        <a href="{{ route('signin') }}" class="link">Sudah punya akun? Login</a>
    </div>
</body>
</html>
