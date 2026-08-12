<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders for the admin.
     */
    public function index(Request $request)
    {
        $orders = Order::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created order from checkout request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $user = $request->user();

        $order = Order::create([
            'user_id' => $user ? $user->id : null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'items' => $request->items,
            'subtotal' => $request->subtotal,
            'tax' => $request->tax,
            'total' => $request->total,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil disimpan di database!',
            'data' => $order
        ], 201);
    }
}
