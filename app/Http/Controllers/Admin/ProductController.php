<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['primaryImage', 'price', 'colors'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ── 1. Validasi ──────────────────────────────────────────────────────
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'is_active'         => 'nullable|in:0,1,true,false,on,off',
            'retail_price'      => 'required|numeric|min:0',
            'reseller_price'    => 'required|numeric|min:0',
            'reseller_min_qty'  => 'required|integer|min:1',
            'colors'            => 'required|array|min:1',
            'colors.*.name'     => 'required|string|max:100',
            'colors.*.stock'    => 'required|integer|min:0',
            'color_images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'product_images'    => 'nullable|array',
            'product_images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ── 2. Buat produk ───────────────────────────────────────────────────
        $product = Product::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        // ── 3. Simpan harga ──────────────────────────────────────────────────
        $product->price()->create([
            'retail_price'      => $request->retail_price,
            'reseller_price'    => $request->reseller_price,
            'reseller_min_qty'  => $request->reseller_min_qty,
            
        ]);
        
        // ── 4. Simpan warna + foto sample per warna ──────────────────────────
        foreach ($request->colors as $index => $colorData) {
    $color = $product->colors()->create([
        'name'  => $colorData['name'],
        'stock' => $colorData['stock'],
    ]);

    if ($request->hasFile("color_images.$index")) {
        $path = $request->file("color_images.$index")
            ->store('colors', 'public');
        $color->update(['image_path' => $path]);
        \Log::info("Color image saved: index=$index, path=$path");
    } else {
        \Log::warning("No file for color index: $index");
    }
}

        // ── 5. Simpan foto produk utama ──────────────────────────────────────
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $i => $imageFile) {
                $path = $imageFile->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['price', 'colors', 'images']);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * FIX: Gunakan upsert (update/create/delete) untuk warna,
     * bukan hapus-semua-lalu-recreate, agar mapping color_images[] tetap akurat.
     */
    public function update(Request $request, Product $product)
    {
        // ── 1. Validasi ──────────────────────────────────────────────────────
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'is_active'         => 'nullable|in:0,1,true,false,on,off',
            'retail_price'      => 'required|numeric|min:0',
            'reseller_price'    => 'required|numeric|min:0',
            'reseller_min_qty'  => 'required|integer|min:1',
            'colors'            => 'required|array|min:1',
            'colors.*.name'     => 'required|string|max:100',
            'colors.*.stock'    => 'required|integer|min:0',
            'color_images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'product_images'    => 'nullable|array',
            'product_images.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ── 2. Update produk ─────────────────────────────────────────────────
        $product->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        // ── 3. Update harga ──────────────────────────────────────────────────
        $product->price()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'retail_price'      => $request->retail_price,
                'reseller_price'    => $request->reseller_price,
                'reseller_min_qty'  => $request->reseller_min_qty,
                
            ]
        );

        // ── 4. Update warna — UPSERT (bukan hapus+recreate) ──────────────────
        //
        // Strategi:
        // a) Warna dengan ID (dari DB) → update nama, stok, dan foto jika ada upload baru
        // b) Warna tanpa ID (baru dari form) → create
        // c) Warna yang dihapus user (dikirim via deleted_colors[]) → hapus dari DB
        //
        $submittedColorIds = [];

        foreach ($request->colors as $index => $colorData) {
            $colorId = $colorData['id'] ?? null;

            if ($colorId) {
                // -- Warna lama: update --
                $color = $product->colors()->find($colorId);
                if ($color) {
                    $color->update([
                        'name'  => $colorData['name'],
                        'stock' => $colorData['stock'],
                    ]);

                    // Upload foto baru jika ada
                    if ($request->hasFile("color_images.$index")) {
                        // Hapus foto lama dari storage
                        if ($color->image_path) {
                            Storage::disk('public')->delete($color->image_path);
                        }
                        $path = $request->file("color_images.$index")
                            ->store('colors', 'public');
                        $color->update(['image_path' => $path]);
                    }

                    $submittedColorIds[] = (int) $colorId;
                }
            } else {
                // -- Warna baru: create --
                $color = $product->colors()->create([
                    'name'  => $colorData['name'],
                    'stock' => $colorData['stock'],
                ]);

                if ($request->hasFile("color_images.$index")) {
                    $path = $request->file("color_images.$index")
                        ->store('colors', 'public');
                    $color->update(['image_path' => $path]);
                }

                $submittedColorIds[] = $color->id;
            }
        }

        // -- Hapus warna yang dikirim via deleted_colors[] --
        $deletedColorIds = $request->input('deleted_colors', []);
        if (!empty($deletedColorIds)) {
            $colorsToDelete = $product->colors()->whereIn('id', $deletedColorIds)->get();
            foreach ($colorsToDelete as $c) {
                if ($c->image_path) {
                    Storage::disk('public')->delete($c->image_path);
                }
                $c->delete();
            }
        }

        // ── 5. Tambahkan foto produk baru (foto lama tidak dihapus di sini) ──
        if ($request->hasFile('product_images')) {
            $existingCount = $product->images()->count();

            foreach ($request->file('product_images') as $i => $imageFile) {
                $path = $imageFile->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $existingCount === 0 && $i === 0,
                    'sort_order' => $existingCount + $i,
                ]);
            }

            if (!$product->images()->where('is_primary', true)->exists()) {
                $product->images()->orderBy('sort_order')->first()
                    ?->update(['is_primary' => true]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        foreach ($product->colors as $color) {
            if ($color->image_path) {
                Storage::disk('public')->delete($color->image_path);
            }
        }
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Remove a single product image from storage and database.
     */
    public function destroyImage(Product $product, ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->orderBy('sort_order')->first()
                ?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}