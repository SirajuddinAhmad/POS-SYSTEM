<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'in') {
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'nullable|exists:categories,id',
                'code' => 'required|string|max:50|unique:products,code',
                'barcode' => 'nullable|string|max:50',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'unit' => 'required|string|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'is_active' => 'nullable|boolean',
            ], [
                'code.unique' => 'Kode SKU/Produk sudah terdaftar, silakan gunakan kode lain.',
                'code.required' => 'Kode produk wajib diisi.',
                'name.required' => 'Nama produk wajib diisi.',
                'cost_price.required' => 'Harga modal/beli wajib diisi.',
                'selling_price.required' => 'Harga jual wajib diisi.',
                'stock.required' => 'Jumlah stok awal wajib diisi.',
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $validated['min_stock'] = $validated['min_stock'] ?? 5;
            $validated['is_active'] = $request->has('is_active');

            $product = Product::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk "' . $product->name . '" BERHASIL ditambahkan ke katalog!',
                    'data' => $product
                ], 200);
            }

            return redirect()->route('products.index')
                ->with('swal_success', 'Produk "' . $product->name . '" BERHASIL ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = implode('<br>', $e->validator->errors()->all());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL menambahkan produk!<br>' . $errors
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'GAGAL menambahkan produk!<br>' . $errors);

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL menambahkan produk! Error: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'GAGAL menambahkan produk! Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        try {
            $validated = $request->validate([
                'category_id' => 'nullable|exists:categories,id',
                'code' => 'required|string|max:50|unique:products,code,' . $product->id,
                'barcode' => 'nullable|string|max:50',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'unit' => 'required|string|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'is_active' => 'nullable|boolean',
            ], [
                'code.unique' => 'Kode SKU/Produk sudah digunakan oleh produk lain.',
                'name.required' => 'Nama produk wajib diisi.',
                'cost_price.required' => 'Harga modal wajib diisi.',
                'selling_price.required' => 'Harga jual wajib diisi.',
            ]);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $validated['min_stock'] = $validated['min_stock'] ?? 5;
            $validated['is_active'] = $request->has('is_active');

            $product->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk "' . $product->name . '" BERHASIL diperbarui!',
                    'data' => $product
                ], 200);
            }

            return redirect()->route('products.index')
                ->with('swal_success', 'Produk "' . $product->name . '" BERHASIL diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = implode('<br>', $e->validator->errors()->all());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL mengedit produk!<br>' . $errors
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'GAGAL mengedit produk!<br>' . $errors);

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL mengedit produk! Error: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'GAGAL mengedit produk! Error: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;

            // Delete associated image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk "' . $productName . '" BERHASIL dihapus dari sistem.'
                ], 200);
            }

            return redirect()->route('products.index')
                ->with('swal_success', 'Produk "' . $productName . '" BERHASIL dihapus!');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL menghapus produk! Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('swal_error', 'GAGAL menghapus produk! Error: ' . $e->getMessage());
        }
    }
}
