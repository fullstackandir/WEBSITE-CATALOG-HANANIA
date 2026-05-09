@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        @php
            $allImages = collect();
            
            // 1. Masukkan semua foto produk biasa
            foreach($product->images as $img) {
                $allImages->push([
                    'url' => asset('storage/' . $img->image_path),
                    'type' => 'gallery',
                    'name' => null,
                    'is_primary' => $img->is_primary
                ]);
            }
            
            // 2. Masukkan semua foto sample warna
            foreach($product->colors->whereNotNull('image_path') as $color) {
                $allImages->push([
                    'url' => asset('storage/' . $color->image_path),
                    'type' => 'color',
                    'name' => $color->name,
                    'is_primary' => false
                ]);
            }
            
            // Jika kosong, beri logo default
            if($allImages->isEmpty()) {
                $allImages->push([
                    'url' => asset('images/logo.png'),
                    'type' => 'gallery',
                    'name' => null,
                    'is_primary' => true
                ]);
            }
            
            $totalImages = $allImages->count();
        @endphp
        
        <div id="productCarousel" class="carousel slide card border-0 shadow-sm overflow-hidden mb-3 position-relative" data-bs-touch="true" data-bs-interval="false">
            <div class="carousel-inner">
                @foreach($allImages as $index => $item)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                         data-type="{{ $item['type'] }}" 
                         data-name="{{ $item['name'] }}"
                         data-index="{{ $index }}">
                        <img src="{{ $item['url'] }}" class="w-100 main-image-carousel" alt="{{ $product->name }}">
                    </div>
                @endforeach
            </div>

            <!-- Label peringatan warna -->
            <div id="active-color-label" class="position-absolute bottom-0 start-0 m-3 px-3 py-1 rounded-pill shadow-sm" style="opacity: 0; transition: opacity 0.3s; background-color: rgba(0,0,0,0.6); color: white; font-size: 0.85rem; pointer-events: none; z-index: 10;">
                <i class="bi bi-palette me-1"></i> Warna: <span id="active-color-text" class="fw-bold"></span>
            </div>
            
            <!-- Indicator Angka -->
            @if($totalImages > 0)
            <div class="position-absolute bottom-0 end-0 m-3 px-2 py-1 rounded shadow-sm" style="background-color: rgba(0,0,0,0.6); color: white; font-size: 0.8rem; pointer-events: none; z-index: 10;">
                <span id="carousel-counter">1</span>/{{ $totalImages }}
            </div>
            @endif
        </div>

        @if($totalImages > 0)
        <div class="d-flex flex-nowrap flex-md-wrap gap-2 mb-4 overflow-auto pb-2 custom-scrollbar" id="product-gallery">
            @foreach($allImages as $index => $item)
                <div class="gallery-item border rounded overflow-hidden js-gallery-btn gallery-item-wrapper {{ $index === 0 ? 'active-thumb' : '' }}" 
                     style="cursor:pointer;"
                     data-index="{{ $index }}">
                    <img src="{{ $item['url'] }}" class="w-100 h-100" style="object-fit: cover;">
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="col-md-7">
        <h1 class="fw-semibold mb-3 product-title">{{ $product->name }}</h1>

        <div class="card mb-4 shadow-sm border-0 price-card">
            <div class="card-header card-header-maroon fw-semibold py-3">
                <i class="bi bi-tags me-2"></i> Daftar Harga
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-borderless mb-0 price-table">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="ps-md-4 ps-3 py-3 align-middle text-muted td-label">Harga Ecer</td>
                            <td class="text-danger fw-bold align-middle price-text">
                                Rp {{ number_format($product->price->retail_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-muted small align-middle pe-md-4 pe-2 td-unit">per pcs</td>
                        </tr>
                        <tr>
                            <td class="ps-md-4 ps-3 py-3 align-middle text-muted td-label">Harga Grosir/Reseller</td>
                            <td class="fw-bold align-middle price-text">
                                Rp {{ number_format($product->price->reseller_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-muted small align-middle pe-md-4 pe-2 td-unit">
                                min. {{ $product->price->reseller_min_qty ?? 3 }} pcs
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-semibold mb-3" style="font-size: 1rem; color: #555;">Pilihan Warna</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse($product->colors as $color)
                    <div class="variant-btn {{ $color->image_path ? 'has-img' : '' }} transition js-color-btn" 
                         data-url="{{ $color->image_path ? asset('storage/' . $color->image_path) : '' }}"
                         data-name="{{ $color->name }}">
                        @if($color->image_path)
                            <img src="{{ asset('storage/' . $color->image_path) }}" class="variant-img" alt="{{ $color->name }}">
                        @endif
                        <span class="variant-text text-truncate">{{ $color->name }}</span>
                    </div>
                @empty
                    <div class="text-muted small">Belum ada varian warna.</div>
                @endforelse
            </div>

            <div class="mt-3 p-2 p-md-3 bg-light border-start border-4 border-maroon rounded shadow-sm info-box-mobile">
                <p class="mb-0 text-muted" style="font-size: 0.8rem; line-height: 1.5;">
                    <i class="bi bi-info-circle-fill text-maroon me-1"></i>
                    <strong>Info:</strong> Untuk menanyakan terkait ketersediaan stok warna di atas,silakan hubungi admin via Whatsapp di bawah ini,agar menandakan bahwa Anda telah mengunjungi Website ini.
                </p>
            </div>
        </div>

        <div class="mt-4 mb-2">
            @php
                $waText = "Assalamualaikum Hanania Hijab, bisa minta info detail terkait ketersediaan stok warna untuk produk *{$product->name}*?";
            @endphp
            <a href="https://wa.me/6282130284387?text={{ urlencode($waText) }}" 
               target="_blank" 
               class="btn btn-success w-100 py-2 shadow-sm fw-semibold rounded-pill d-flex justify-content-center align-items-center btn-wa-mobile">
                <i class="bi bi-whatsapp fs-5 me-2"></i> Tanya Stok & Order via WhatsApp
            </a>
        </div>
    </div>
</div>

<div class="row mt-4 mt-md-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-3 p-md-5 desc-card-body">
                <div class="d-flex align-items-center mb-3 mb-md-4">
                    <div style="width: 4px; height: 24px; background-color: #7B1C1C; border-radius: 4px;" class="me-2 me-md-3 desc-bar"></div>
                    <h5 class="fw-bold mb-0 desc-title" style="letter-spacing: 1px; color: #333;">DESKRIPSI PRODUK</h5>
                </div>
                <hr class="mb-3 mb-md-4" style="opacity: 0.1;">
                
                <div class="product-description-box text-secondary" style="font-size: 1.05rem; color: #444;">
                    {!! nl2br(e(trim($product->description))) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carouselEl = document.getElementById('productCarousel');
        const carousel = new bootstrap.Carousel(carouselEl);
        
        const galleryBtns = document.querySelectorAll('.js-gallery-btn');
        const colorBtns = document.querySelectorAll('.js-color-btn');
        const labelContainer = document.getElementById('active-color-label');
        const labelText = document.getElementById('active-color-text');
        const counterText = document.getElementById('carousel-counter');

        // Update indikator dan label warna setiap kali carousel bergeser
        carouselEl.addEventListener('slide.bs.carousel', function (e) {
            const nextItem = e.relatedTarget;
            const indexStr = nextItem.getAttribute('data-index');
            const type = nextItem.getAttribute('data-type');
            const name = nextItem.getAttribute('data-name');
            
            // Update counter angka (index base 0, jadi ditambah 1)
            if(counterText) {
                counterText.textContent = parseInt(indexStr) + 1;
            }

            // Update label warna
            if (type === 'color' && name) {
                labelText.textContent = name;
                labelContainer.style.opacity = '1';
            } else {
                labelContainer.style.opacity = '0';
            }

            // Update border thumbnail gallery dan otomatis scroll (penanda mengikuti posisi slide)
            galleryBtns.forEach(btn => {
                if (btn.getAttribute('data-index') === indexStr) {
                    btn.classList.add('active-thumb');
                    
                    // Auto scroll thumbnail supaya penanda selalu kelihatan di layar (seperti di Shopee)
                    const container = document.getElementById('product-gallery');
                    if(container && window.innerWidth <= 768) {
                        const scrollLeftTarget = btn.offsetLeft - (container.offsetWidth / 2) + (btn.offsetWidth / 2);
                        container.scrollTo({ left: scrollLeftTarget, behavior: 'smooth' });
                    }
                } else {
                    btn.classList.remove('active-thumb');
                }
            });

            // Update centang di pilihan warna
            colorBtns.forEach(btn => {
                if (type === 'color' && btn.getAttribute('data-name') === name) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });

        // Saat gambar di Gallery diklik
        galleryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                carousel.to(index);
            });
        });

        // Saat pilihan Warna diklik
        colorBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const colorName = this.getAttribute('data-name');
                const url = this.getAttribute('data-url');
                
                if (url && url.trim() !== '') {
                    // Cari foto di carousel yang punya nama warna yang sama
                    const targetItem = document.querySelector(`.carousel-item[data-name="${colorName}"]`);
                    if (targetItem) {
                        const idx = parseInt(targetItem.getAttribute('data-index'));
                        carousel.to(idx);
                        
                        // Scroll to top otomatis di mobile
                        if (window.innerWidth <= 768) {
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    }
                }
            });
        });
    });
</script>

<style>
    /* Responsivitas Foto Utama */
    .main-image-carousel {
        height: 500px;
        object-fit: cover;
    }
    .gallery-item-wrapper {
        flex: 0 0 auto;
        width: 85px;
        height: 85px;
    }
    .product-title {
        font-size: 2rem;
    }
    .price-text {
        font-size: 1.25rem;
    }
    @media (max-width: 768px) {
        .main-image-carousel {
            height: auto;
            max-height: 450px;
            object-fit: contain;
            background-color: #f8f9fa;
        }
        .gallery-item-wrapper {
            width: 65px;
            height: 65px;
        }
        /* Sembunyikan scrollbar untuk slider horizontal */
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .product-title {
            font-size: 1.05rem;
            font-weight: 500 !important;
            line-height: 1.4;
            color: #2c2c2c;
            margin-bottom: 0.5rem !important;
        }
        .btn-wa-mobile {
            font-size: 0.95rem !important;
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
            letter-spacing: 0.2px;
        }
        .info-box-mobile {
            margin-top: 1rem !important;
            padding: 0.75rem !important;
        }
        .price-text {
            font-size: 1.05rem;
        }
        .price-table td {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .td-label {
            font-size: 0.85rem;
        }
        .td-unit {
            font-size: 0.75rem;
        }
        .card-header-maroon {
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem;
        }
        .desc-card-body {
            padding: 1rem !important;
        }
        .desc-title {
            font-size: 0.85rem !important;
            letter-spacing: 0.5px !important;
            color: #444 !important;
        }
        .desc-bar {
            height: 16px !important;
            width: 3px !important;
        }
        .product-description-box {
            font-size: 0.85rem !important;
            line-height: 1.6;
            color: #555 !important;
        }
        .product-description-box br {
            margin: 0.8em !important;
        }
    }

    /* Transisi & Hover Gallery */
    .gallery-item { 
        border: 2px solid transparent; 
        transition: all 0.3s ease; 
        opacity: 0.55; 
    }
    .gallery-item:hover { 
        opacity: 0.9;
        transform: translateY(-2px); 
    }
    .active-thumb { 
        border-color: #7B1C1C !important; 
        border-width: 2px !important; 
        opacity: 1 !important; 
        box-shadow: 0 4px 12px rgba(123, 28, 28, 0.35); 
    }

    /* CSS Varian Warna Ala Shopee */
    .variant-btn {
        border: 1px solid #e0e0e0;
        background-color: #fff;
        color: #333;
        border-radius: 4px;
        padding: 6px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        position: relative;
        min-height: 38px;
        transition: all 0.2s;
        /* Cegah teks keblokir saat double-click cepat */
        user-select: none; 
    }
    .variant-btn.has-img { padding: 4px 12px 4px 4px; }
    .variant-btn:hover { border-color: #7B1C1C; color: #7B1C1C; }
    
    /* Efek Centang Active */
    .variant-btn.active { border-color: #7B1C1C; color: #7B1C1C; background-color: #fff5f5; }
    .variant-btn.active::after {
        content: ''; position: absolute; bottom: 0; right: 0; width: 0; height: 0;
        border-bottom: 15px solid #7B1C1C; border-left: 15px solid transparent;
    }
    .variant-btn.active::before {
        content: '✓'; position: absolute; bottom: -3px; right: 1px; color: white; font-size: 9px; font-weight: bold; z-index: 1;
    }
    .variant-img { width: 28px; height: 28px; object-fit: cover; border-radius: 2px; }
    .variant-text { font-size: 0.85rem; }

    /* Deskripsi Produk */
    .product-description-box { line-height: 1.8; }
    .product-description-box br { content: ""; margin: 1.5em; display: block; font-size: 24%; }
</style>
@endpush