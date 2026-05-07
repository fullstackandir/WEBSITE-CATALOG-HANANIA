@extends('layouts.app')
@section('title', 'Admin - Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Manajemen Produk</h3>
        <small class="text-muted">Total: {{ $products->total() }} produk</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-maroon" target="_blank">
            <i class="bi bi-eye"></i> Lihat Katalog
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-maroon">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-maroon">
                <tr>
                    <th>Foto</th>
                    <th>Nama Produk</th>
                    <th>Harga Ecer</th>
                    <th>Warna</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}"
                                 style="width:55px; height:55px; object-fit:cover;"
                                 class="rounded">
                        @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                 style="width:55px; height:55px;">
                                <i class="bi bi-image text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td class="text-danger fw-bold">
                        @if($product->price)
                            Rp {{ number_format($product->price->retail_price, 0, ',', '.') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $product->colors->count() }} warna</td>
                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin hapus produk \'{{ $product->name }}\'?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada produk.
                        <a href="{{ route('admin.products.create') }}">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $products->links() }}</div>
@endsection