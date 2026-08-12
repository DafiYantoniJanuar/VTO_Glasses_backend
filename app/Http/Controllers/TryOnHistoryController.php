<?php

namespace App\Http\Controllers;

use App\Models\TryOnHistory;
use Illuminate\Http\Request;

class TryOnHistoryController extends Controller
{
    /**
     * Display a listing of try-on histories for current authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $history = TryOnHistory::with('product')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Store a newly created try-on history record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = $request->user();
        
        $history = TryOnHistory::create([
            'user_id' => $user ? $user->id : null,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Try-on history recorded successfully.',
            'data' => $history
        ], 210); // Custom success code or 201
    }
}
