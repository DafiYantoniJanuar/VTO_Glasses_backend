<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get list of favorited products for current authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Guest user or unauthenticated'
            ]);
        }

        $favorites = Favorite::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('product')
            ->filter();

        return response()->json([
            'success' => true,
            'data' => $favorites->values()
        ]);
    }

    /**
     * Toggle favorite status of a product (Add/Remove).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan untuk menambahkan ke favorit.'
            ], 401);
        }

        $productId = $request->product_id;
        $existing = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'is_favorited' => false,
                'message' => 'Produk telah dihapus dari favorit.'
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);
            return response()->json([
                'success' => true,
                'is_favorited' => true,
                'message' => 'Produk berhasil ditambahkan ke favorit.'
            ]);
        }
    }

    /**
     * Remove product from favorites.
     */
    public function destroy(Request $request, $productId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan.'
            ], 401);
        }

        Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk telah dihapus dari favorit.'
        ]);
    }
}
