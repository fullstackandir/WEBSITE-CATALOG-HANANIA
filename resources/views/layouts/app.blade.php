<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Hanania')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --maroon: #7B1C1C;
            --maroon-dark: #5a1414;
            --maroon-light: #9e2a2a;
        }

        /* Navbar */
        .navbar-maroon {
            background-color: var(--maroon) !important;
        }
        .navbar-maroon .navbar-brand:hover {
            opacity: 0.85;
        }
        .navbar-maroon .btn-outline-light:hover {
            background-color: var(--maroon-light);
            border-color: white;
        }

        /* Logo bulat */
        .navbar-logo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.5);
        }

        /* Tombol utama */
        .btn-maroon {
            background-color: var(--maroon);
            color: white;
            border: none;
        }
        .btn-maroon:hover {
            background-color: var(--maroon-dark);
            color: white;
        }
        .btn-outline-maroon {
            border: 1px solid var(--maroon);
            color: var(--maroon);
            background: transparent;
        }
        .btn-outline-maroon:hover {
            background-color: var(--maroon);
            color: white;
        }

        /* Card produk */
        .product-card {
            transition: transform .2s, box-shadow .2s;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(123, 28, 28, 0.15);
        }

        /* Warna dot */
        .color-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #dee2e6;
        }

        /* Badge */
        .badge-ready { background-color: #198754; }
        .badge-habis { background-color: #dc3545; }

        /* Table header admin */
        .table-maroon thead {
            background-color: var(--maroon);
            color: white;
        }

        /* Card header */
        .card-header-maroon {
            background-color: var(--maroon);
            color: white;
        }

        /* Pagination active */
        .page-item.active .page-link {
            background-color: var(--maroon);
            border-color: var(--maroon);
        }
        .page-link {
            color: var(--maroon);
        }
        .page-link:hover {
            color: var(--maroon-dark);
        }

        /* Focus outline */
        .form-control:focus, .form-select:focus {
            border-color: var(--maroon-light);
            box-shadow: 0 0 0 .2rem rgba(123,28,28,.15);
        }

        footer {
            border-top: 2px solid var(--maroon) !important;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-maroon shadow-sm">
        <div class="container">
            {{-- Logo + Nama --}}
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('catalog.index') }}">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Hanania Hijab Logo"
                     class="navbar-logo"
                     onerror="this.style.display='none'">
                <div>
                    <div style="font-size:1.1rem; line-height:1.1;">Hanania Hijab</div>
                    <div style="font-size:0.65rem; font-weight:400; opacity:0.85; letter-spacing:1px;">
                        YOUR HIJAB SOLUTION
                    </div>
                </div>
            </a>

            {{-- Auth button dipindah ke dalam Navbar agar rapi di kanan --}}
            <div class="d-flex align-items-center ms-auto">
                @auth
                    {{-- Tombol Logout HANYA muncul di halaman yang URL-nya berawalan /admin --}}
                    @if(request()->is('admin*'))
                        <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
            
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-muted py-4 mt-4">
        <small>© {{ date('Y') }} <strong style="color:var(--maroon)">Hanania Hijab</strong> — YOUR HIJAB SOLUTION — All rights reserved</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>