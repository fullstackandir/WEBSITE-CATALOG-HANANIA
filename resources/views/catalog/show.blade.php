@extends('layouts.app')
@section('title', $product->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>

{{-- ═══════════════════════════════════════════════════════════════
     BARIS ATAS: Foto kiri + Info kanan
════════════════════════════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- Kolom Gambar --}}
    <div class="col-md-5">

        {{--
            ✅ FIX UTAMA:
            #main-image selalu dirender.
            Prioritas foto awal:
            1. Foto produk utama (is_primary=true)
            2. Foto produk pertama
            3. Foto warna pertama (jika tidak ada product_images sama sekali)
            4. Placeholder kosong
        --}}
        @php
            $mainImageUrl = null;

            if ($product->images->isNotEmpty()) {
                $primary = $product->images->firstWhere('is_primary', true)
                           ?? $product->images->first();
                $mainImageUrl = $primary->url;
            } elseif ($product->colors->whereNotNull('image_path')->isNotEmpty()) {
                $firstColorWithImage = $product->colors->whereNotNull('image_path')->first();
                $mainImageUrl = asset('storage/' . $firstColorWithImage->image_path);
            }
        @endphp

        @if($mainImageUrl)
            <img id="main-image"
                 src="{{ $mainImageUrl }}"
                 class="img-fluid rounded shadow-sm w-100 mb-2"
                 style="object-fit:contain; background:#f5f5f5; max-height:420px; transition: opacity .2s;">
        @else
            {{-- Tidak ada foto sama sekali, tetap render elemen agar switchImage tidak error --}}
            <div id="main-image-placeholder"
                 class="bg-secondary rounded d-flex align-items-center justify-content-center mb-2"
                 style="height:300px;">
                <i class="bi bi-image text-white" style="font-size:3rem;"></i>
            </div>
            {{-- Hidden img tetap ada agar JS tidak error --}}
            <img id="main-image" src="" class="d-none w-100 rounded shadow-sm mb-2"
                 style="object-fit:contain; background:#f5f5f5; max-height:420px;">
        @endif

        {{-- Thumbnail foto produk (jika ada) --}}
        @if($product->images->isNotEmpty())
        <div class="d-flex flex-wrap gap-2">
            @foreach($product->images as $image)
                <img src="{{ $image->url }}"
                     class="rounded border thumb-img"
                     style="width:72px; height:72px; object-fit:cover; cursor:pointer;
                            {{ ($image->is_primary || (!$product->images->contains('is_primary', true) && $loop->first)) ? 'border-color:#7B1C1C !important; border-width:2px !important;' : '' }}"
                     onclick="switchImage('{{ $image->url }}', this)"
                     title="{{ $product->name }}">
            @endforeach
        </div>
        @endif

    </div>

    {{-- Kolom Info --}}
    <div class="col-md-7">
        <h2 class="fw-bold mb-3">{{ $product->name }}</h2>

        {{-- 1. Daftar Harga --}}
        @if($product->price)
        <div class="card mb-3">
            <div class="card-header fw-semibold text-white card-header-maroon">
                💰 Daftar Harga
            </div>
            <table class="table table-sm mb-0">
                <tbody>
                    <tr>
                        <td width="40%">Harga Normal</td>
                        <td class="text-danger fw-bold">
                            Rp {{ number_format($product->price->retail_price, 0, ',', '.') }}
                        </td>
                        <td class="text-muted small">per pcs</td>
                    </tr>
                    <tr>
                        <td>Harga Grosir</td>
                        <td class="fw-bold">
                            Rp {{ number_format($product->price->reseller_price, 0, ',', '.') }}
                        </td>
                        <td class="text-muted small">
                            min. {{ $product->price->reseller_min_qty }} pcs
                        </td>
                    </tr>
                    <tr>
                        
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- 2. Warna & Stok --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold text-white card-header-maroon">
                🎨 Warna & Stok
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($product->colors as $color)
                    <div class="col-6 col-md-4">
                        @if($color->image_path)
                            {{--
                                ✅ FIX: onclick sekarang selalu panggil switchImage.
                                Jika product tidak punya images, placeholder disembunyikan
                                dan #main-image ditampilkan dengan foto warna.
                            --}}
                            <div class="border rounded overflow-hidden color-card"
                                 style="cursor:pointer; transition: box-shadow .2s;"
                                 onmouseover="this.style.boxShadow='0 0 0 2px #7B1C1C'"
                                 onmouseout="this.style.boxShadow=''"
                                 onclick="switchImage('{{ asset('storage/' . $color->image_path) }}', null)"
                                 title="Klik untuk lihat foto warna {{ $color->name }}">
                                <img src="{{ asset('storage/' . $color->image_path) }}"
                                     class="w-100"
                                     style="height:80px; object-fit:cover;">
                                <div class="px-2 py-1 text-center" style="background:#fafafa;">
                                    <div class="fw-semibold small">{{ $color->name }}</div>
                                    <span class="badge {{ $color->status_class }}" style="font-size:.7rem;">
                                        {{ $color->status }}
                                        @if($color->stock > 0)({{ $color->stock }})@endif
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2 p-2 border rounded">
                                <span class="color-dot flex-shrink-0"
                                      style="background-color: {{ $color->hex_code ?? '#cccccc' }}">
                                </span>
                                <div>
                                    <div class="fw-semibold small">{{ $color->name }}</div>
                                    <span class="badge {{ $color->status_class }}" style="font-size:.7rem;">
                                        {{ $color->status }}
                                        @if($color->stock > 0)({{ $color->stock }})@endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if($product->colors->whereNotNull('image_path')->count() > 0)
                    <p class="text-muted small mt-2 mb-0">
                        <i class="bi bi-hand-index"></i> Klik foto warna untuk lihat tampilan model.
                    </p>
                @endif
            </div>
        </div>

        {{-- 3. Tombol WhatsApp --}}
        <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}"
           class="btn btn-success btn-lg w-100 mb-2" target="_blank">
            <i class="bi bi-whatsapp me-2"></i>Tanya via WhatsApp
        </a>
        <p class="text-center text-muted small">
            <i class="bi bi-info-circle"></i>
            Pesan otomatis sudah disiapkan, tinggal kirim!
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     BARIS BAWAH: Deskripsi Produk
════════════════════════════════════════════════════════════════ --}}
@if($product->description)
<div class="card">
    <div class="card-header" style="background:#f8f8f8; border-bottom: 1px solid #eee;">
        <span class="fw-bold" style="font-size:1rem;">Deskripsi Produk</span>
    </div>
    <div class="card-body" style="line-height:1.9; font-size:.95rem; white-space:pre-line; color:#333;">
        {{ $product->description }}
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
/**
 * switchImage — ganti foto utama saat thumbnail atau warna diklik.
 *
 * FIX: Sekarang juga menangani kasus di mana produk tidak punya
 * product_images (hanya foto warna). Dalam kasus itu, #main-image
 * awalnya tersembunyi (d-none) dan placeholder ditampilkan.
 * Setelah warna diklik pertama kali, placeholder disembunyikan
 * dan #main-image ditampilkan.
 */
function switchImage(url, el) {
    const mainImg = document.getElementById('main-image');
    const placeholder = document.getElementById('main-image-placeholder');

    // Sembunyikan placeholder jika ada, tampilkan #main-image
    if (placeholder) {
        placeholder.classList.add('d-none');
    }
    if (mainImg) {
        mainImg.classList.remove('d-none');
        mainImg.src = url;
    }

    // Reset semua border thumbnail produk
    document.querySelectorAll('.thumb-img').forEach(t => {
        t.style.borderColor = '';
        t.style.borderWidth = '1px';
    });

    // Highlight thumbnail yang diklik (hanya thumbnail produk, bukan kartu warna)
    if (el && el.classList.contains('thumb-img')) {
        el.style.borderColor = '#7B1C1C';
        el.style.borderWidth = '2px';
    }
}
</script>
@endpush