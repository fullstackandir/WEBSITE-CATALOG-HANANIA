@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
{{-- Hero Header & Search --}}
<div class="catalog-hero rounded-4 mb-5 position-relative overflow-hidden shadow-lg mt-3">
    <div class="hero-bg position-absolute w-100 h-100 top-0 start-0"></div>
    <div class="position-relative z-1 p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
        <div class="text-white text-center text-md-start">
            <h1 class="fw-bold display-6 mb-2 hero-title" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: 1px;">Katalog Produk</h1>
            <p class="mb-0 hero-subtitle" style="opacity: 0.85; font-size: 1.05rem;">Temukan hijab elegan pilihan Anda untuk setiap momen.</p>
        </div>
        
        <div class="hero-search-wrapper w-100" style="max-width: 450px;">
            <form method="GET" class="position-relative">
                <input type="text" name="search" class="form-control form-control-lg rounded-pill border-0 shadow-sm pe-5 ps-4 search-input" 
                       placeholder="Cari hijab favoritmu..." value="{{ request('search') }}"
                       style="font-size: 0.95rem; height: 54px;">
                <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y rounded-circle me-2 border-0 d-flex justify-content-center align-items-center shadow-sm btn-search-hero">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            @if(request()->has('search') && request('search') != '')
                <div class="mt-3 text-center text-md-end">
                    <a href="{{ route('catalog.index') }}" class="badge rounded-pill bg-white text-maroon text-decoration-none px-3 py-2 shadow-sm">
                        <i class="bi bi-x-circle me-1"></i> Reset Pencarian ({{ $products->total() }} hasil)
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Horizontal Scroll Kategori Populer --}}
@php $currentSearch = request('search') ? strtolower(request('search')) : ''; @endphp
<div class="mb-4 d-flex align-items-center custom-scrollbar" style="overflow-x: auto; white-space: nowrap; padding-bottom: 0.5rem; -webkit-overflow-scrolling: touch;">
    <a href="{{ route('catalog.index') }}" class="btn {{ empty($currentSearch) ? 'btn-maroon border-0 text-white' : 'btn-white border text-muted' }} rounded-pill px-4 py-2 me-2 shadow-sm fw-semibold" style="font-size: 0.9rem;">Semua</a>
    <a href="{{ route('catalog.index', ['search' => 'Pashmina']) }}" class="btn {{ str_contains($currentSearch, 'pashmina') ? 'btn-maroon border-0 text-white' : 'btn-white border text-muted' }} rounded-pill px-4 py-2 me-2 shadow-sm fw-semibold" style="font-size: 0.9rem;">Pashmina</a>
    <a href="{{ route('catalog.index', ['search' => 'Bergo']) }}" class="btn {{ str_contains($currentSearch, 'bergo') ? 'btn-maroon border-0 text-white' : 'btn-white border text-muted' }} rounded-pill px-4 py-2 me-2 shadow-sm fw-semibold" style="font-size: 0.9rem;">Bergo</a>
    <a href="{{ route('catalog.index', ['search' => 'Segi Empat']) }}" class="btn {{ str_contains($currentSearch, 'segi empat') ? 'btn-maroon border-0 text-white' : 'btn-white border text-muted' }} rounded-pill px-4 py-2 me-2 shadow-sm fw-semibold" style="font-size: 0.9rem;">Segi Empat</a>
    <a href="{{ route('catalog.index', ['search' => 'Ciput']) }}" class="btn {{ str_contains($currentSearch, 'ciput') ? 'btn-maroon border-0 text-white' : 'btn-white border text-muted' }} rounded-pill px-4 py-2 me-2 shadow-sm fw-semibold" style="font-size: 0.9rem;">Ciput</a>
</div>

{{-- Section Title --}}
<div class="d-flex align-items-center justify-content-between mb-3 mt-4">
    <h5 class="fw-bold mb-0 d-flex align-items-center" style="color: #2c2c2c;">
        <span class="text-maroon me-2"></span> Rekomendasi Untukmu
    </h5>
</div>

{{-- Grid Produk ala Shopee --}}
@if($products->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">Produk yang Anda cari tidak ditemukan.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-maroon px-4">Lihat Semua Produk Yang Tersedia</a>
    </div>
@else
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
        @foreach($products as $product)
        <div class="col">
            <a href="{{ route('catalog.show', $product->slug) }}" class="text-decoration-none text-dark">
                <div class="card product-card h-100 border-0 bg-white">
                    {{-- Foto Produk - RESPONSIVE SQUARE --}}
                    <div class="position-relative w-100 image-wrapper" style="padding-bottom: 110%;"> 
                        @if($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}"
                                 class="position-absolute top-0 start-0 w-100 h-100 product-img"
                                 style="object-fit: cover;" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-muted opacity-25" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        {{-- Hover Overlay Action (hanya terlihat di Desktop) --}}
                        <div class="overlay-action position-absolute w-100 h-100 top-0 start-0 d-none d-md-flex align-items-center justify-content-center">
                            <span class="btn btn-light rounded-pill px-4 py-2 shadow-sm fw-semibold text-maroon hover-btn">
                                Lihat Detail
                            </span>
                        </div>
                    </div>

                    {{-- Body Kartu --}}
                    <div class="card-body p-3 p-md-4 text-center d-flex flex-column justify-content-between position-relative z-1 bg-white" style="border-radius: 0 0 16px 16px;">
                        <div>
                            <h6 class="card-title mb-2 fw-bold product-title-text" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h6>

                            @if($product->price)
                                <p class="text-maroon fw-semibold mb-0 product-price-text">
                                    Rp {{ number_format($product->price->retail_price, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="mt-5 mb-5 d-flex justify-content-center custom-pagination">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- Floating WhatsApp Action Button --}}
<a href="https://wa.me/6282130284387?text=Assalamualaikum%20Hanania%20Hijab,%20saya%20ingin%20bertanya%20seputar%20katalog..." 
   target="_blank"
   class="btn-floating-wa shadow-lg d-flex align-items-center justify-content-center text-white text-decoration-none"
   title="Chat Admin via WhatsApp">
    <i class="bi bi-whatsapp fs-3"></i>
</a>

<style>
    /* === Kategori & General === */
    .btn-white {
        background-color: white;
    }
    .btn-white:hover {
        background-color: #f8f9fa;
    }
    .custom-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .custom-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .btn-floating-wa {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: #25D366;
        border-radius: 50%;
        z-index: 999;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
    }
    .btn-floating-wa:hover {
        transform: scale(1.15) rotate(-5deg);
        box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4) !important;
    }

    /* === Custom Pagination === */
    .custom-pagination .page-link {
        color: #7B1C1C;
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid #eee;
        transition: all 0.3s ease;
    }
    .custom-pagination .page-link:hover {
        background-color: #7B1C1C;
        color: white;
        border-color: #7B1C1C;
        transform: translateY(-2px);
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #7B1C1C;
        border-color: #7B1C1C;
        color: white;
        box-shadow: 0 4px 10px rgba(123, 28, 28, 0.2);
    }
    .custom-pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #fff;
        border-color: #eee;
    }

    /* === Hero Section === */
    .catalog-hero {
        background: linear-gradient(135deg, #7B1C1C 0%, #4A0E0E 100%);
    }
    .hero-bg {
        background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.04"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }
    .btn-search-hero {
        background-color: #7B1C1C;
        color: white;
        width: 44px;
        height: 44px;
        transition: all 0.2s;
    }
    .btn-search-hero:hover {
        background-color: #5a1414;
        transform: scale(1.05);
    }
    .text-maroon {
        color: #7B1C1C !important;
    }

    /* === Product Cards === */
    .product-card {
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(123, 28, 28, 0.12) !important;
    }
    .image-wrapper {
        border-radius: 16px 16px 0 0;
        overflow: hidden;
    }
    .product-img {
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .product-card:hover .product-img {
        transform: scale(1.08);
    }
    .overlay-action {
        background: rgba(0,0,0,0.15);
        opacity: 0;
        transition: all 0.3s ease;
    }
    .hover-btn {
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s ease;
    }
    .product-card:hover .overlay-action {
        opacity: 1;
    }
    .product-card:hover .hover-btn {
        transform: translateY(0);
        opacity: 1;
    }
    .product-title-text {
        color: #2c2c2c;
        font-size: 1rem;
        letter-spacing: 0.2px;
        /* Line Clamp 2 lines ala Shopee */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
        line-height: 1.4;
        min-height: 2.8em; /* Menjaga tinggi kartu tetap sama meski judul hanya 1 baris */
    }
    .product-price-text {
        font-size: 1.15rem;
    }
    @media (max-width: 768px) {
        .catalog-hero {
            border-radius: 12px !important;
            margin-top: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        .catalog-hero > .z-1 {
            padding: 1.5rem 1.25rem !important; /* Override p-4 */
            gap: 1.25rem !important;
        }
        .hero-title {
            font-size: 1.6rem !important;
        }
        .hero-subtitle {
            font-size: 0.85rem !important;
        }
        .search-input {
            height: 46px !important;
            font-size: 0.85rem !important;
        }
        .btn-search-hero {
            width: 36px !important;
            height: 36px !important;
        }
        
        /* Product Cards Mobile Optimization */
        .product-card {
            border-radius: 10px !important;
        }
        .image-wrapper {
            border-radius: 10px 10px 0 0 !important;
        }
        .card-body {
            border-radius: 0 0 10px 10px !important;
            padding: 0.6rem !important; /* Override p-3 */
        }
        .product-title-text {
            font-size: 0.8rem;
            margin-bottom: 0.25rem !important;
            line-height: 1.35;
            min-height: 2.7em;
        }
        .product-price-text {
            font-size: 0.95rem;
        }
        .btn-floating-wa {
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
        }
        .btn-floating-wa i {
            font-size: 1.6rem !important;
        }
    }
</style>
@endsection