@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Kolom Gambar Utama -->
    <div class="col-md-5 mb-4">
        @php
            // Logika Fallback Gambar: Primary -> First Product Image -> First Color Image -> Placeholder
            $primaryImage = $product->images->where('is_primary', true)->first() 
                            ?? $product->images->first() 
                            ?? $product->colors->whereNotNull('image_path')->first();
            
            $mainImageUrl = $primaryImage && isset($primaryImage->image_path)
                            ? asset('storage/' . $primaryImage->image_path)
                            : ($primaryImage && isset($primaryImage->url) ? $primaryImage->url : asset('images/logo.png')); // Fallback placeholder
        @endphp
        
        <div class="card border-0 shadow-sm overflow-hidden">
            <img id="main-image" src="{{ $mainImageUrl }}" class="w-100" style="height: 500px; object-fit: cover;" alt="{{ $product->name }}">
        </div>
    </div>

    <!-- Kolom Detail Produk -->
    <div class="col-md-7">
        <h1 class="h2 fw-bold mb-3">{{ $product->name }}</h1>
        <p class="text-secondary mb-4" style="line-height: 1.6; white-space: pre-wrap;">{{ $product->description }}</p>

        <!-- Tabel Harga (Ecer & Grosir saja) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header card-header-maroon fw-semibold py-3">
                <i class="bi bi-tags me-2"></i> Daftar Harga
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-borderless mb-0">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="ps-4 py-3 align-middle text-muted">Harga Ecer</td>
                            <td class="text-danger fw-bold fs-5 align-middle">
                                Rp {{ number_format($product->price->retail_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-muted small align-middle pe-4">per pcs</td>
                        </tr>
                        <tr>
                            <td class="ps-4 py-3 align-middle text-muted">Harga Grosir</td>
                            <td class="fw-bold fs-5 align-middle">
                                Rp {{ number_format($product->price->reseller_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-muted small align-middle pe-4">
                                min. {{ $product->price->reseller_min_qty ?? 3 }} pcs
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pilihan Warna (Tanpa Badge Stok) -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3">Pilihan Warna</h5>
            <div class="d-flex flex-wrap gap-3">
                @forelse($product->colors as $color)
                    @if($color->image_path)
                        {{-- Warna dengan foto --}}
                        <div class="border rounded overflow-hidden color-card shadow-sm transition" style="cursor:pointer; width: 85px;"
                             onclick="switchImage('{{ asset('storage/' . $color->image_path) }}', this)">
                            <img src="{{ asset('storage/' . $color->image_path) }}"
                                 class="w-100 border-bottom" style="height:80px; object-fit:cover;" alt="{{ $color->name }}">
                            <div class="px-2 py-2 text-center bg-light">
                                <div class="fw-semibold text-truncate" style="font-size: 0.75rem;" title="{{ $color->name }}">
                                    {{ $color->name }}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Warna tanpa foto --}}
                        <div class="d-flex align-items-center justify-content-center px-3 py-2 border rounded bg-light shadow-sm">
                            <span class="fw-semibold" style="font-size: 0.85rem;">{{ $color->name }}</span>
                        </div>
                    @endif
                @empty
                    <div class="text-muted small">Belum ada varian warna.</div>
                @endforelse
            </div>
        </div>

        <!-- Tombol Pesan via WhatsApp -->
        <div class="mt-5">
            @php
                // Format pesan WhatsApp agar rapih saat masuk ke WA Admin
                $waText = "Halo Admin Hanania Hijab, saya tertarik untuk order produk *{$product->name}*. Boleh info lebih lanjut?";
            @endphp
            <a href="https://wa.me/6281234567890?text={{ urlencode($waText) }}" 
               target="_blank" 
               class="btn btn-success btn-lg w-100 py-3 shadow-sm fw-bold">
                <i class="bi bi-whatsapp me-2"></i> Pesan via WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script ganti gambar saat warna diklik
    function switchImage(url, element) {
        // Ganti gambar utama
        document.getElementById('main-image').src = url;

        // Reset highlight border pada semua pilihan warna
        document.querySelectorAll('.color-card').forEach(card => {
            card.classList.remove('border-maroon', 'border-2');
        });

        // Tambahkan border maroon ke warna yang sedang aktif
        if (element) {
            element.classList.add('border-maroon', 'border-2');
        }
    }
</script>

<style>
    /* Tambahan agar efek hover transisi lebih halus */
    .color-card.transition {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .color-card.transition:hover {
        transform: translateY(-2px);
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush