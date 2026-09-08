<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of all catalog products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('shape', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created catalog product (Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shape' => 'required|string|max:100',
            'color' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'model_3d' => 'nullable|file|max:51200',
            'stock' => 'nullable|integer|min:0',
            'best_seller' => 'boolean',
            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('model_3d')) {
            $file = $request->file('model_3d');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/models', $filename);
            $validated['model_3d_url'] = '/storage/models/' . $filename;
        }

        unset($validated['model_3d']);

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kacamata berhasil ditambahkan ke katalog!',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified catalog product.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk kacamata tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    /**
     * Update the specified catalog product (Admin).
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk kacamata tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'shape' => 'sometimes|required|string|max:100',
            'color' => 'sometimes|required|string|max:100',
            'price' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'model_3d' => 'nullable|file|max:51200',
            'stock' => 'nullable|integer|min:0',
            'best_seller' => 'boolean',
            'rating' => 'nullable|numeric|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('model_3d')) {
            if ($product->model_3d_url) {
                $oldPath = str_replace('/storage/models/', 'public/models/', $product->model_3d_url);
                Storage::delete($oldPath);
            }
            $file = $request->file('model_3d');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/models', $filename);
            $validated['model_3d_url'] = '/storage/models/' . $filename;
        }

        unset($validated['model_3d']);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data kacamata berhasil diperbarui!',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified catalog product (Admin).
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk kacamata tidak ditemukan.'
            ], 404);
        }

        if ($product->model_3d_url) {
            $path = str_replace('/storage/models/', 'public/models/', $product->model_3d_url);
            Storage::delete($path);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk kacamata berhasil dihapus dari katalog.'
        ]);
    }
}
