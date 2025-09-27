<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery; // <-- Pastikan ada
use Illuminate\Http\Request; // <-- Pastikan ada
use Illuminate\Support\Facades\Gate; // <-- Pastikan ada
use Illuminate\Support\Facades\Storage; // <-- Pastikan ada
use Illuminate\Support\Facades\Validator; // <-- Pastikan ada

class DeliveryController extends Controller
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
    public function store(StoreDeliveryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['ppic_id'] = auth()->id();
        $validatedData['status'] = 'preparing';

        $delivery = Delivery::create($validatedData);

        return response()->json([
            'message' => 'Jadwal pengiriman berhasil dibuat.',
            'data' => new DeliveryResource($delivery)
        ], 201);
    }

    /**
     * Upload handover document for a delivery.
     */
    public function uploadHandoverDocument(Request $request, Delivery $delivery)
    {
        // 1. Otorisasi
        Gate::authorize('manage delivery');
        if (auth()->id() !== $delivery->ppic_id) {
            return response()->json(['message' => 'Ini bukan tugas pengiriman Anda.'], 403);
        }

        // 2. Validasi file
        $validator = Validator::make($request->all(), [
            'handover_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // max 5MB
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Upload file ke S3
        $file = $request->file('handover_document');
        $filePath = $file->store('handover-documents', 's3');
        Storage::disk('s3')->setVisibility($filePath, 'public');
        $fileUrl = Storage::disk('s3')->url($filePath);

        // 4. Update data pengiriman
        $delivery->update([
            'handover_document_path' => $fileUrl,
            'status' => 'handover_completed',
            'delivered_at' => now(),
        ]);

        // 5. FINAL: Update status proyek utama menjadi 'completed'
        $delivery->project->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Dokumen serah terima berhasil di-upload. Proyek telah selesai.',
            'data' => new DeliveryResource($delivery->fresh()),
        ]);
    }
}
