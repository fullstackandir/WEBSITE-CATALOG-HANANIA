<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Hanania Hijab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --maroon: #7B1C1C; --maroon-dark: #5a1414; }
        body { background: #f5f0f0; }
        .login-card {
            max-width: 420px;
            margin: 80px auto;
            border-top: 4px solid var(--maroon);
        }
        .btn-maroon {
            background-color: var(--maroon);
            color: white;
            border: none;
        }
        .btn-maroon:hover { background-color: var(--maroon-dark); color: white; }
        .form-control:focus {
            border-color: var(--maroon);
            box-shadow: 0 0 0 .2rem rgba(123,28,28,.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow login-card">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <div style="font-size:2rem; color:var(--maroon);">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="fw-bold mt-1" style="color:var(--maroon);">Hanania Admin</h4>
                    <small class="text-muted">Masuk untuk mengelola produk</small>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="admin@hanania.com"
                               autofocus required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="••••••••" required>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="remember" id="remember">
                            <label class="form-check-label text-muted" for="remember">
                                Ingat saya
                            </label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-maroon btn-lg">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </button>
                    </div>
                </form>

            </div>
        </div>
        <p class="text-center text-muted mt-3">
            <small>
                <a href="{{ route('catalog.index') }}" class="text-muted">
                    <i class="bi bi-arrow-left"></i> Kembali ke Katalog
                </a>
            </small>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>