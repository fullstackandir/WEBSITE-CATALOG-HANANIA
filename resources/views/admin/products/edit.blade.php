@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Edit Produk</h3>
        <small class="text-muted">{{ $product->name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('catalog.show', $product->slug) }}" class="btn btn-outline-maroon" target="_blank">
            <i class="bi bi-eye"></i> Lihat di Katalog
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-maroon">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<form id="edit-product-form"
      action="{{ route('admin.products.update', $product) }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div id="deleted-colors-container"></div>

    <div class="row g-4">

        {{-- KOLOM KIRI --}}
        <div class="col-md-8">

            {{-- Info Dasar --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">
                    📦 Informasi Produk
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Produk <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Harga --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">
                    💰 Harga
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Harga Ecer <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="retail_price"
                                       class="form-control @error('retail_price') is-invalid @enderror"
                                       value="{{ old('retail_price', $product->price->retail_price ?? '') }}"
                                       min="0" required>
                                @error('retail_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga Grosir/Reseller</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="reseller_price" class="form-control"
                                       value="{{ old('reseller_price', $product->price->reseller_price ?? '') }}"
                                       min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min. Beli Grosir/Reseller</label>
                            <div class="input-group">
                                <input type="number" name="reseller_min_qty" class="form-control"
                                       value="{{ old('reseller_min_qty', $product->price->reseller_min_qty ?? 3) }}"
                                       min="1" required>
                                <span class="input-group-text">pcs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warna --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon d-flex justify-content-between align-items-center">
                    🎨 Pilihan Warna
                    <button type="button" class="btn btn-sm btn-light" id="add-color">
                        <i class="bi bi-plus-lg"></i> Tambah Warna
                    </button>
                </div>
                <div class="card-body">
                    <div id="colors-container">
                        @foreach($product->colors as $index => $color)
                        <div class="color-row border rounded p-3 mb-3 position-relative">

                            <input type="hidden" name="colors[{{ $index }}][id]" value="{{ $color->id }}">
                            <input type="hidden" name="colors[{{ $index }}][image_path]" value="{{ $color->image_path }}">

                            <button type="button"
                                    class="btn btn-outline-danger btn-sm btn-remove-color position-absolute"
                                    style="top:10px; right:10px;"
                                    {{ $product->colors->count() === 1 ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>

                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Nama Warna</label>
                                    <input type="text" name="colors[{{ $index }}][name]"
                                           class="form-control"
                                           value="{{ old("colors.$index.name", $color->name) }}"
                                           placeholder="contoh: Hitam" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">
                                        Foto Sample Warna
                                        @if($color->image_path)
                                            <span class="text-muted fw-normal">(upload baru untuk mengganti)</span>
                                        @endif
                                    </label>
                                    <div class="color-dropzone border rounded text-center p-2"
                                         style="border: 2px dashed #dee2e6 !important; cursor:pointer; min-height:80px; position:relative;"
                                         data-index="{{ $index }}"
                                         onclick="document.getElementById('color-file-{{ $index }}').click()"
                                         ondragover="event.preventDefault(); this.style.borderColor='#7B1C1C';"
                                         ondragleave="this.style.borderColor='#dee2e6';"
                                         ondrop="handleColorDrop(event, {{ $index }})">

                                        @if($color->image_path)
                                        <div class="dropzone-content-{{ $index }}">
                                            <img src="{{ asset('storage/' . $color->image_path) }}"
                                                 style="max-height:100px; max-width:100%; border-radius:6px; object-fit:contain;">
                                            <p class="text-muted small mb-0 mt-1">
                                                <i class="bi bi-arrow-repeat"></i> Klik atau drag untuk ganti foto
                                            </p>
                                        </div>
                                        @else
                                        <div class="dropzone-content-{{ $index }}">
                                            <i class="bi bi-image text-muted fs-4"></i>
                                            <p class="text-muted small mb-0">Drag & drop atau klik untuk upload</p>
                                        </div>
                                        @endif

                                    </div>
                                    <input type="file" id="color-file-{{ $index }}"
                                           name="color_images[{{ $index }}]"
                                           class="d-none" accept="image/*"
                                           onchange="previewColorImage(this, {{ $index }})">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-md-4">

            {{-- Status --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">⚙️ Pengaturan</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="is_active" id="is_active"
                               {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <span class="fw-semibold">Produk Aktif</span><br>
                            <small class="text-muted">Tampil di halaman katalog</small>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Foto yang sudah ada --}}
            @if($product->images->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">🖼️ Foto Produk Saat Ini</div>
                <div class="card-body">
                    <div id="existing-images-grid" class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $image)
                        <div class="position-relative existing-image-wrap" data-image-id="{{ $image->id }}">
                            <img src="{{ $image->url }}"
                                 style="width:75px; height:75px; object-fit:cover;"
                                 class="rounded border">
                            @if($image->is_primary)
                                <span class="badge bg-warning text-dark position-absolute top-0 start-0"
                                      style="font-size:9px;">Utama</span>
                            @endif
                            <button type="button"
                                    class="btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center position-absolute top-0 end-0"
                                    style="width:20px; height:20px; font-size:10px;"
                                    onclick="deleteImage({{ $image->id }}, '{{ route('admin.products.images.destroy', [$product, $image]) }}', this)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> Klik X untuk hapus foto
                    </small>
                </div>
            </div>
            @endif

            {{-- Upload Foto Produk Baru --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">📷 Tambah Foto Produk</div>
                <div class="card-body">
                    <div id="product-dropzone"
                         class="border rounded p-3 text-center mb-3"
                         style="border: 2px dashed #dee2e6 !important; cursor:pointer; min-height:100px;"
                         onclick="document.getElementById('product-image-input').click()"
                         ondragover="event.preventDefault(); this.style.borderColor='#7B1C1C'; this.style.background='#fff5f5';"
                         ondragleave="this.style.borderColor='#dee2e6'; this.style.background='';"
                         ondrop="handleProductDrop(event)">
                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                        <p class="text-muted small mb-0 mt-1">Drag & drop atau klik untuk upload</p>
                        <p class="text-muted mb-0" style="font-size:.75rem;">JPG, PNG, WEBP — Maks. 2MB/foto</p>
                    </div>
                    {{-- DIHAPUS onchange="previewProductImages(this)" KARENA PAKAI JS LISTENER --}}
                    <input type="file" name="product_images[]" class="d-none"
                           multiple accept="image/*" id="product-image-input">
                    <div id="product-image-preview" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-maroon btn-lg">
                    <i class="bi bi-save me-1"></i> Update Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-maroon">
                    Batal
                </a>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
let colorIndex = {{ $product->colors->count() }};

function deleteImage(imageId, url, btn) {
    if (!confirm('Hapus foto ini?')) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || '{{ csrf_token() }}',
            'X-HTTP-Method-Override': 'DELETE',
        },
        body: JSON.stringify({ _method: 'DELETE' }),
    })
    .then(res => {
        if (res.ok || res.redirected) {
            const wrap = btn.closest('.existing-image-wrap');
            if (wrap) wrap.remove();
        } else {
            alert('Gagal menghapus foto. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x"></i>';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-x"></i>';
    });
}

document.getElementById('add-color').addEventListener('click', function () {
    const container = document.getElementById('colors-container');
    const html = `
        <div class="color-row border rounded p-3 mb-3 position-relative">
            <input type="hidden" name="colors[${colorIndex}][image_path]" value="">
            <button type="button"
                    class="btn btn-outline-danger btn-sm btn-remove-color position-absolute"
                    style="top:10px; right:10px;">
                <i class="bi bi-trash"></i>
            </button>
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label fw-semibold small">Nama Warna</label>
                    <input type="text" name="colors[${colorIndex}][name]"
                           class="form-control" placeholder="contoh: Putih" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Foto Sample Warna</label>
                    <div class="color-dropzone border rounded text-center p-2"
                         style="border: 2px dashed #dee2e6 !important; cursor:pointer; min-height:80px;"
                         data-index="${colorIndex}"
                         onclick="document.getElementById('color-file-${colorIndex}').click()"
                         ondragover="event.preventDefault(); this.style.borderColor='#7B1C1C';"
                         ondragleave="this.style.borderColor='#dee2e6';"
                         ondrop="handleColorDrop(event, ${colorIndex})">
                        <div class="dropzone-content-${colorIndex}">
                            <i class="bi bi-image text-muted fs-4"></i>
                            <p class="text-muted small mb-0">Drag & drop atau klik untuk upload</p>
                        </div>
                    </div>
                    <input type="file" id="color-file-${colorIndex}"
                           name="color_images[${colorIndex}]"
                           class="d-none" accept="image/*"
                           onchange="previewColorImage(this, ${colorIndex})">
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    colorIndex++;
    updateRemoveButtons();
});

document.getElementById('colors-container').addEventListener('click', function (e) {
    if (e.target.closest('.btn-remove-color')) {
        const row = e.target.closest('.color-row');

        const colorIdInput = row.querySelector('input[name$="[id]"]');
        if (colorIdInput && colorIdInput.value) {
            const deletedContainer = document.getElementById('deleted-colors-container');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'deleted_colors[]';
            hiddenInput.value = colorIdInput.value;
            deletedContainer.appendChild(hiddenInput);
        }

        row.remove();
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.color-row');
    rows.forEach(row => {
        row.querySelector('.btn-remove-color').disabled = rows.length === 1;
    });
}

function previewColorImage(input, index) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const content = document.querySelector(`.dropzone-content-${index}`);
        if (content) {
            content.innerHTML = `
                <img src="${e.target.result}"
                     style="max-height:100px; max-width:100%; border-radius:6px; object-fit:contain;">
                <p class="text-success small mb-0 mt-1">
                    <i class="bi bi-check-circle"></i> ${input.files[0].name} (akan disimpan)
                </p>`;
        }
    };
    reader.readAsDataURL(input.files[0]);
}

function handleColorDrop(event, index) {
    event.preventDefault();
    event.currentTarget.style.borderColor = '#dee2e6';
    const file = event.dataTransfer.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    const input = document.getElementById(`color-file-${index}`);
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    previewColorImage(input, index);
}

// === LOGIKA FOTO PRODUK (Diperbarui menggunakan Array) ===
let uploadedProductFiles = [];

document.getElementById('product-image-input').addEventListener('change', function() {
    addProductFiles(Array.from(this.files));
});

function addProductFiles(newFiles) {
    newFiles.forEach(file => {
        const isDuplicate = uploadedProductFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDuplicate && file.type.startsWith('image/')) {
            uploadedProductFiles.push(file);
        }
    });
    renderProductPreviews();
    syncProductInput();
}

function renderProductPreviews() {
    const preview = document.getElementById('product-image-preview');
    preview.innerHTML = '';
    
    uploadedProductFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'position-relative mt-2';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:75px; height:75px; object-fit:cover;" class="rounded border">
                <button type="button" class="btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center position-absolute top-0 end-0" 
                        style="width:20px; height:20px; font-size:10px; transform: translate(30%, -30%);" 
                        onclick="removeProductFile(${i})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    const dz = document.getElementById('product-dropzone');
    if(uploadedProductFiles.length > 0) {
        dz.querySelector('p').textContent = `${uploadedProductFiles.length} foto siap ditambahkan`;
    } else {
        dz.querySelector('p').textContent = "Drag & drop atau klik untuk upload";
    }
}

function removeProductFile(index) {
    uploadedProductFiles.splice(index, 1);
    renderProductPreviews();
    syncProductInput();
}

function syncProductInput() {
    const input = document.getElementById('product-image-input');
    const dt = new DataTransfer();
    uploadedProductFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function handleProductDrop(event) {
    event.preventDefault();
    const dz = document.getElementById('product-dropzone');
    dz.style.borderColor = '#dee2e6';
    dz.style.background = '';
    
    addProductFiles(Array.from(event.dataTransfer.files));
}
</script>
@endpush