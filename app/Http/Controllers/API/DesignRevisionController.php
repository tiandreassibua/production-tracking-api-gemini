<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\DesignRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DesignRevisionController extends Controller
{
    public function uploadRevisedFile(Request $request, DesignRevision $revision)
    {
        // 1. Otorisasi: Cek izin 'manage revision' & kepemilikan tugas
        Gate::authorize('manage revision');
        if (auth()->id() !== $revision->design->studio_id) {
            return response()->json(['message' => 'Ini bukan tugas desain Anda.'], 403);
        }

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'revised_file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Upload file ke S3
        $file = $request->file('revised_file');
        $filePath = $file->store('designs/revisions', 's3');
        Storage::disk('s3')->setVisibility($filePath, 'public');
        $fileUrl = Storage::disk('s3')->url($filePath);

        // 4. Update catatan revisi dengan path file baru
        $revision->update(['file_path' => $fileUrl]);

        // 5. Update status desain utama kembali ke 'pending_review'
        $revision->design->update(['status' => 'pending_review']);

        return response()->json([
            'message' => 'File revisi berhasil di-upload.',
            'data' => new DesignResource($revision->design->fresh()),
        ]);
    }
}
