@extends('layouts.app')

@section('title', 'Login Admin - Hanania Hijab')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header card-header-maroon text-center py-3">
                <h4 class="mb-0 fw-bold">Login Admin</h4>
            </div>
            <div class="card-body p-4">
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    {{-- Input Username --}}
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               required 
                               autofocus 
                               placeholder="Masukkan username">
                        
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Input Password dengan ikon Hide/Unhide --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Masukkan password">
                            
                            {{-- Tombol Toggle Mata --}}
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                            
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-maroon w-100 py-2 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                    </button>
                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk Hide/Unhide Password
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            // Ubah ke text agar terlihat
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash'); // Ganti ikon mata dicoret
        } else {
            // Ubah kembali ke password agar tersembunyi
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye'); // Ganti ikon mata biasa
        }
    }
</script>
@endpush