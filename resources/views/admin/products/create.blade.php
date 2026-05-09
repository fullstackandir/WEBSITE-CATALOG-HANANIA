@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Tambah Produk Baru</h3>
        <small class="text-muted">Isi semua informasi produk di bawah ini</small>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-maroon">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
    @csrf

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
                               value="{{ old('name') }}"
                               placeholder="contoh: Hijab Voile Polos Premium"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="contoh: Bahan voile lembut, tidak transparan, cocok untuk sehari-hari...">{{ old('description') }}</textarea>
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
                                       value="{{ old('retail_price') }}"
                                       placeholder="0" min="0" required>
                                @error('retail_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga Grosir/Reseller</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="reseller_price"
                                       class="form-control"
                                       value="{{ old('reseller_price') }}"
                                       placeholder="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min. Beli Grosir/Reseller</label>
                            <div class="input-group">
                                <input type="number" name="reseller_min_qty"
                                       class="form-control"
                                       value="{{ old('reseller_min_qty', 3) }}"
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
                        <div class="color-row border rounded p-3 mb-3 position-relative">
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm btn-remove-color position-absolute"
                                    style="top:10px; right:10px;" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Nama Warna</label>
                                    <input type="text" name="colors[0][name]"
                                           class="form-control"
                                           placeholder="contoh: Hitam" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Foto Sample Warna</label>
                                    <div class="color-dropzone border rounded text-center p-3"
                                         style="border: 2px dashed #dee2e6 !important; cursor:pointer; min-height:80px;"
                                         data-index="0"
                                         onclick="document.getElementById('color-file-0').click()"
                                         ondragover="event.preventDefault(); this.style.borderColor='#7B1C1C';"
                                         ondragleave="this.style.borderColor='#dee2e6';"
                                         ondrop="handleColorDrop(event, 0)">
                                        <div class="dropzone-content-0">
                                            <i class="bi bi-image text-muted fs-4"></i>
                                            <p class="text-muted small mb-0">Drag & drop atau klik untuk upload</p>
                                        </div>
                                    </div>
                                    <input type="file" id="color-file-0"
                                           name="color_images[0]"
                                           class="d-none" accept="image/*"
                                           onchange="previewColorImage(this, 0)">
                                </div>
                            </div>
                        </div>
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
                               name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">
                            <span class="fw-semibold">Produk Aktif</span><br>
                            <small class="text-muted">Tampil di halaman katalog</small>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Upload Foto Produk --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold card-header-maroon">📷 Foto Produk</div>
                <div class="card-body">
                    <div id="drop-zone"
                         class="border rounded text-center p-4 mb-3"
                         style="border: 2px dashed #7B1C1C !important; cursor:pointer; transition: background .2s;"
                         onclick="document.getElementById('image-input').click()"
                         ondragover="event.preventDefault(); this.style.backgroundColor='#fdf0f0';"
                         ondragleave="this.style.backgroundColor='';"
                         ondrop="handleProductDrop(event)">
                        <i class="bi bi-cloud-upload fs-1" style="color:#7B1C1C;"></i>
                        <p class="fw-semibold mb-0 mt-1" style="color:#7B1C1C;">Drag & Drop foto di sini</p>
                        <p class="text-muted small mb-0">atau klik untuk pilih file</p>
                        <p class="text-muted mt-1 mb-0" style="font-size:.75rem;">JPG, PNG, WEBP — Maks. 2MB/foto</p>
                    </div>
                    <input type="file" name="product_images[]" class="d-none"
                           multiple accept="image/*" id="image-input"
                           onchange="addFiles(Array.from(this.files))">
                    <div id="image-preview" class="d-flex flex-wrap gap-2"></div>
                    <small class="text-muted d-block mt-2 d-none" id="preview-hint">
                        <i class="bi bi-star-fill text-warning"></i>
                        Foto pertama = foto utama di katalog
                    </small>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-maroon btn-lg">
                    <i class="bi bi-save me-1"></i> Simpan Produk
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
// FOTO PRODUK
let uploadedFiles = [];

function addFiles(newFiles) {
    newFiles.forEach(file => {
        const isDuplicate = uploadedFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDuplicate && file.type.startsWith('image/')) {
            uploadedFiles.push(file);
        }
    });
    renderPreviews();
    syncToInput();
}

function renderPreviews() {
    const preview = document.getElementById('image-preview');
    const hint    = document.getElementById('preview-hint');
    preview.innerHTML = '';
    hint.classList.toggle('d-none', uploadedFiles.length === 0);

    uploadedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'position-relative';
            div.innerHTML = `
                <img src="${e.target.result}"
                     style="width:75px; height:75px; object-fit:cover;"
                     class="rounded border">
                ${i === 0 ? '<span class="badge bg-warning text-dark position-absolute top-0 start-0" style="font-size:9px;">Utama</span>' : ''}
                <button type="button"
                        class="btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center position-absolute top-0 end-0"
                        style="width:20px; height:20px; font-size:10px;"
                        onclick="removeFile(${i})">
                    <i class="bi bi-x"></i>
                </button>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    uploadedFiles.splice(index, 1);
    renderPreviews();
    syncToInput();
}

function syncToInput() {
    const input = document.getElementById('image-input');
    const dt = new DataTransfer();
    uploadedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

function handleProductDrop(event) {
    event.preventDefault();
    document.getElementById('drop-zone').style.backgroundColor = '';
    addFiles(Array.from(event.dataTransfer.files));
}

// WARNA
let colorIndex = 1;

document.getElementById('add-color').addEventListener('click', function () {
    const container = document.getElementById('colors-container');
    const html = `
        <div class="color-row border rounded p-3 mb-3 position-relative">
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
                    <div class="color-dropzone border rounded text-center p-3"
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
        e.target.closest('.color-row').remove();
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
                     style="max-height:80px; max-width:100%; border-radius:6px; object-fit:contain;">
                <p class="text-success small mb-0 mt-1">
                    <i class="bi bi-check-circle"></i> ${input.files[0].name}
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
</script>
@endpush