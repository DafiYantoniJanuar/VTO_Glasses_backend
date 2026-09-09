<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display all reviews and statistical summary for a specific product.
     */
    public function index($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk kacamata tidak ditemukan.'
            ], 404);
        }

        $reviews = Review::with('user:id,name,email')
            ->where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->get();

        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : (float) ($product->rating ?: 5.0);

        // Breakdown distribution 5..1 stars
        $breakdown = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
        foreach ($reviews as $rev) {
            $star = (int) $rev->rating;
            if (isset($breakdown[$star])) {
                $breakdown[$star]++;
            }
        }

        // Fit breakdown summary
        $fitStats = [
            'Sangat Pas' => 0,
            'Sedikit Sempit' => 0,
            'Sedikit Longgar' => 0,
        ];
        foreach ($reviews as $rev) {
            if ($rev->fit_feedback && isset($fitStats[$rev->fit_feedback])) {
                $fitStats[$rev->fit_feedback]++;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'reviews' => $reviews,
                'summary' => [
                    'average_rating' => $avgRating,
                    'total_reviews' => $totalReviews,
                    'breakdown' => $breakdown,
                    'fit_stats' => $fitStats,
                ]
            ]
        ]);
    }

    /**
     * Check if the authenticated user has purchased this product and is eligible to review.
     */
    public function checkEligibility(Request $request, $productId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'is_authenticated' => false,
                'has_purchased' => false,
                'has_reviewed' => false,
                'can_review' => false,
                'my_review' => null,
            ]);
        }

        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        // Check if user has purchased this product in any completed order
        $orders = Order::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('email', $user->email);
        })->get();

        $hasPurchased = false;
        foreach ($orders as $order) {
            $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['id']) && (int) $item['id'] === (int) $productId) {
                        $hasPurchased = true;
                        break 2;
                    }
                }
            }
        }

        // Admin can always review for testing, otherwise requires purchase
        $isAdmin = ($user->role === 'admin' || $user->email === 'admin@vtogla.com');
        $eligible = ($hasPurchased || $isAdmin);

        return response()->json([
            'is_authenticated' => true,
            'has_purchased' => $hasPurchased,
            'has_reviewed' => $existingReview !== null,
            'can_review' => $eligible && ($existingReview === null),
            'is_admin' => $isAdmin,
            'my_review' => $existingReview,
        ]);
    }

    /**
     * Store a newly created verified review in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan masuk untuk menulis ulasan.'
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
            'fit_feedback' => 'nullable|string|max:50',
            'vto_accuracy' => 'nullable|string|max:50',
        ]);

        $productId = $validated['product_id'];

        // Prevent duplicate reviews
        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah pernah memberikan ulasan untuk kacamata ini.'
            ], 422);
        }

        // Verified buyer check
        $orders = Order::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('email', $user->email);
        })->get();

        $hasPurchased = false;
        foreach ($orders as $order) {
            $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['id']) && (int) $item['id'] === (int) $productId) {
                        $hasPurchased = true;
                        break 2;
                    }
                }
            }
        }

        $isAdmin = ($user->role === 'admin' || $user->email === 'admin@vtogla.com');

        if (!$hasPurchased && !$isAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pembeli terverifikasi yang dapat menulis ulasan untuk produk ini.'
            ], 403);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'fit_feedback' => $validated['fit_feedback'] ?? 'Sangat Pas',
            'vto_accuracy' => $validated['vto_accuracy'] ?? 'Sesuai AR',
        ]);

        // Auto update product rating and review count
        $this->syncProductRating($productId);

        $review->load('user:id,name,email');

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan Anda berhasil dipublikasikan!',
            'data' => $review
        ], 201);
    }

    /**
     * Update an existing review (Edit Ulasan).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan masuk untuk mengedit ulasan.'
            ], 401);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ulasan tidak ditemukan.'
            ], 404);
        }

        $isAdmin = ($user->role === 'admin' || $user->email === 'admin@vtogla.com');

        // Only the author or admin can edit
        if ($review->user_id !== $user->id && !$isAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda hanya dapat mengedit ulasan milik Anda sendiri.'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
            'fit_feedback' => 'nullable|string|max:50',
            'vto_accuracy' => 'nullable|string|max:50',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'fit_feedback' => $validated['fit_feedback'] ?? $review->fit_feedback,
            'vto_accuracy' => $validated['vto_accuracy'] ?? $review->vto_accuracy,
        ]);

        // Auto update product rating
        $this->syncProductRating($review->product_id);

        $review->load('user:id,name,email');

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan berhasil diperbarui!',
            'data' => $review
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ulasan tidak ditemukan.'
            ], 404);
        }

        $isAdmin = ($user->role === 'admin' || $user->email === 'admin@vtogla.com');

        if ($review->user_id !== $user->id && !$isAdmin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat menghapus ulasan milik pengguna lain.'
            ], 403);
        }

        $productId = $review->product_id;
        $review->delete();

        // Auto update product rating and review count
        $this->syncProductRating($productId);

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan berhasil dihapus.'
        ]);
    }

    /**
     * Helper to recalculate and synchronize product rating and review count.
     */
    private function syncProductRating($productId)
    {
        $avg = Review::where('product_id', $productId)->avg('rating');
        $count = Review::where('product_id', $productId)->count();

        Product::where('id', $productId)->update([
            'rating' => $count > 0 ? round($avg, 1) : 4.5,
            'reviews' => $count
        ]);
    }
}
