<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::where('customer_id', auth()->guard('api')->user()->id)->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Invoices: ' . auth()->guard('api')->user()->name,
            'data'    => $invoices
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($snap_token)
    {
        $invoice = Invoice::where('customer_id', auth()->guard('api')->user()->id)->where('snap_token', $snap_token)->latest()->first();

        return response()->json([
            'success' => true,
            'message' => 'Detail Invoices: ' . auth()->guard('api')->user()->name,
            'data'    => $invoice,
            'product' => $invoice->orders
        ], 200);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
