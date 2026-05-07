@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="fw-bold mb-0">Katalog Produk</h2>
        <p class="text-muted mb-0">Temukan hijab pilihan Anda</p>
    </div>
</div>

{{-- Filter & Search --}}
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-7">
        <input type="text" name="search" class="form-control"
               placeholder="🔍 Cari produk..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <select name="availability" class="form-select">
            <option value="">Semua Stok</option>
            <option value="ready" @selected(request('availability') === 'ready')>✅ Ready</option>
            <option value="habis" @selected(request('availability') === 'habis')>❌ Habis</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-maroon w-100">Cari</button>
    </div>
    @if(request()->hasAny(['search', 'availability']))
        <div class="col-12">
            <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-maroon">
                <i class="bi bi-x"></i> Reset Filter
            </a>
            <small class="text-muted ms-2">{{ $products->total() }} produk ditemukan</small>
        </div>
    @endif
</form>

{{-- Grid Produk --}}
@if($products->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">Produk tidak ditemukan.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-maroon">Lihat Semua</a>
    </div>
@else
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        @foreach($products as $product)
        <div class="col">
            <a href="{{ route('catalog.show', $product->slug) }}" class="text-decoration-none text-dark">
                <div class="card product-card h-100">
                    @if($product->primaryImage)
                        <img src="{{ $product->primaryImage->url }}"
                             class="card-img-top"
                             style="height:200px; object-fit:cover;"
                             alt="{{ $product->name }}">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center"
                             style="height:200px;">
                            <i class="bi bi-image text-white fs-1"></i>
                        </div>
                    @endif

                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 fw-semibold" style="font-size:.9rem;">
                            {{ $product->name }}
                        </h6>

                        @if($product->price)
                            <p class="text-danger fw-bold mb-1" style="font-size:.95rem;">
                                Rp {{ number_format($product->price->retail_price, 0, ',', '.') }}
                            </p>
                        @endif

                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @foreach($product->colors->take(6) as $color)
                                <span class="color-dot"
                                      title="{{ $color->name }}"
                                      style="background-color: {{ $color->hex_code ?? '#cccccc' }}">
                                </span>
                            @endforeach
                            @if($product->colors->count() > 6)
                                <small class="text-muted align-self-center">
                                    +{{ $product->colors->count() - 6 }}
                                </small>
                            @endif
                        </div>

                        @if($product->isAvailable())
                            <span class="badge bg-success">Ready</span>
                        @else
                            <span class="badge bg-danger">Habis</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endif
@endsection