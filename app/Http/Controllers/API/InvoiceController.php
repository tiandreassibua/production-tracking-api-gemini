<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice; // <-- Pastikan ada
use Illuminate\Http\Request; // <-- Pastikan ada
use Illuminate\Support\Facades\Gate; // <-- Pastikan ada
use Illuminate\Support\Facades\Storage; // <-- Pastikan ada
use Illuminate\Support\Facades\Validator; // <-- Pastikan ada

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $validatedData = $request->validated();

        // Tambahkan user keuangan yang sedang login
        $validatedData['finance_id'] = auth()->id();
        // Buat nomor invoice unik (contoh: INV-20250926-001)
        $validatedData['invoice_number'] = 'INV-' . now()->format('Ymd') . '-' . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);
        $validatedData['status'] = 'pending';

        $invoice = Invoice::create($validatedData);

        return response()->json([
            'message' => 'Invoice baru berhasil dibuat.',
            'data' => new InvoiceResource($invoice)
        ], 201);
    }

    /**
     * Upload payment proof for an invoice.
     */
    public function uploadPaymentProof(Request $request, Invoice $invoice)
    {
        Gate::authorize('upload payment proof');

        $validator = Validator::make($request->all(), [
            'payment_proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload file ke S3
        $file = $request->file('payment_proof_file');
        $filePath = $file->store('payment-proofs', 's3');
        Storage::disk('s3')->setVisibility($filePath, 'public');
        $fileUrl = Storage::disk('s3')->url($filePath);

        // Update invoice dengan URL bukti bayar
        $invoice->update(['payment_proof_path' => $fileUrl]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil di-upload. Menunggu verifikasi.',
            'data' => new InvoiceResource($invoice->fresh()),
        ]);
    }

    /**
     * Verify a payment and mark invoice as paid.
     */
    public function verifyPayment(Invoice $invoice)
    {
        Gate::authorize('verify payment');

        if (is_null($invoice->payment_proof_path)) {
            return response()->json(['message' => 'Bukti pembayaran belum di-upload.'], 422);
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json([
            'message' => 'Pembayaran telah diverifikasi. Invoice lunas.',
            'data' => new InvoiceResource($invoice),
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
