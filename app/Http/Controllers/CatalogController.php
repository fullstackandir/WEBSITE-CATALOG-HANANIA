<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'price', 'colors'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'ready') {
                $query->whereHas('colors', fn($q) => $q->where('stock', '>', 0));
            } elseif ($request->availability === 'habis') {
                $query->whereDoesntHave('colors', fn($q) => $q->where('stock', '>', 0));
            }
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('catalog.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['price', 'colors', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $waNumber = '6282130284387'; // ← GANTI dengan nomor WA bisnis kamu

        $waMessage = urlencode(
            "Halo kak, saya tertarik dengan produk *{$product->name}*.\n" .
            "Boleh info ketersediaan warna dan harganya? 😊"
        );

        return view('catalog.show', compact('product', 'waMessage', 'waNumber'));
    }
}