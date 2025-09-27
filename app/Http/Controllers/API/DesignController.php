<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Design;
use App\Models\Project;
use App\Models\DesignRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Penting: Import Storage Facade
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\DesignResource; // <-- IMPORT
use Illuminate\Support\Facades\Gate; // <-- IMPORT


class DesignController extends Controller
{
    /**
     * Handle the initial design file upload from Marketing to a Project.
     */
    public function uploadInitialDesign(Request $request, Project $project)
    {
        // 1. Otorisasi: Pastikan yang upload adalah marketing penanggung jawab proyek
        if (auth()->id() !== $project->marketing_id) {
            return response()->json(['message' => 'Anda tidak berwenang untuk proyek ini.'], 403);
        }

        // 2. Validasi Input
        $validator = Validator::make($request->all(), [
            'initial_file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240', // max 10MB
            'studio_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Cek apakah proyek sudah punya desain
        if ($project->design) {
            return response()->json(['message' => 'Proyek ini sudah memiliki data desain.'], 409); // 409 Conflict
        }

        // 4. Proses Upload ke S3
        $file = $request->file('initial_file');
        // 'designs/initial' adalah nama folder di dalam bucket S3
        $filePath = $file->store('designs/initial', 's3');

        // 5. Atur visibilitas file menjadi 'public' agar bisa diakses via URL
        Storage::disk('s3')->setVisibility($filePath, 'public');

        // 6. Dapatkan URL publik dari file yang diupload
        $fileUrl = Storage::disk('s3')->url($filePath);

        // 7. Simpan URL ke database
        $design = Design::create([
            'project_id' => $project->id,
            'studio_id' => $request->studio_id,
            'initial_file_path' => $fileUrl, // Simpan URL lengkap
            'status' => 'pending_review',
        ]);

        return response()->json([
            'message' => 'File desain awal berhasil di-upload ke S3.',
            'data' => $design
        ], 201);
    }

    /**
     * Get design tasks assigned to the authenticated Studio user.
     */
    public function myTasks()
    {
        // Otorisasi: Cek apakah user punya izin
        Gate::authorize('view assigned designs');

        $user = auth()->user();

        // Ambil semua data desain yang studio_id-nya adalah ID user yang login
        // Gunakan 'with' untuk eager loading agar lebih efisien
        $designs = Design::where('studio_id', $user->id)
            ->with('project.client')
            ->latest()
            ->get();

        // Format outputnya menggunakan Resource Collection
        return DesignResource::collection($designs);
    }

    /**
     * Handle the final design file upload from Studio.
     */
    public function uploadFinalDesign(Request $request, Design $design)
    {
        // 1. Otorisasi: Cek izin & kepemilikan tugas
        Gate::authorize('upload final design');

        if (auth()->id() !== $design->studio_id) {
            return response()->json(['message' => 'Ini bukan tugas desain Anda.'], 403);
        }

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'final_file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Proses Upload ke S3
        $file = $request->file('final_file');
        // Simpan di folder yang berbeda untuk memisahkan file awal dan final
        $filePath = $file->store('designs/final', 's3');

        Storage::disk('s3')->setVisibility($filePath, 'public');
        $fileUrl = Storage::disk('s3')->url($filePath);

        // 4. Update data di database
        $design->update([
            'final_file_path' => $fileUrl,
            'status' => 'pending_review', // Status berubah, menunggu approval dari client/marketing
        ]);

        return response()->json([
            'message' => 'File desain final berhasil di-upload.',
            'data' => new DesignResource($design)
        ]);
    }

    public function approveDesign(Design $design)
    {
        // 1. Otorisasi: Cek izin & pastikan user adalah marketing PIC proyek ini
        Gate::authorize('approve design');

        if (auth()->id() !== $design->project->marketing_id) {
            return response()->json(['message' => 'Anda bukan penanggung jawab proyek ini.'], 403);
        }

        // 2. Validasi Logika: Pastikan ada file final yang di-upload sebelum approve
        if (is_null($design->final_file_path)) {
            return response()->json(['message' => 'Tidak bisa menyetujui desain yang belum di-upload oleh studio.'], 422);
        }

        // 3. Update Status Desain
        $design->update([
            'status' => 'approved',
            'approved_at' => now(), // Catat waktu persetujuan
        ]);

        // 4. Update Status Proyek ke Tahap Selanjutnya!
        $design->project->update([
            'status' => 'pending', // Status proyek berubah menjadi 'pending' (menunggu cek bahan di gudang)
        ]);

        return response()->json([
            'message' => 'Desain telah disetujui. Proyek dilanjutkan ke tahap selanjutnya.',
            'data' => new DesignResource($design),
        ]);
    }

    /**
     * Request a revision for a design.
     */
    public function requestRevision(Request $request, Design $design)
    {
        // 1. Otorisasi
        Gate::authorize('request revision');
        if (auth()->id() !== $design->project->marketing_id) {
            return response()->json(['message' => 'Anda bukan penanggung jawab proyek ini.'], 403);
        }

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'client_notes' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Update status desain utama
        $design->update(['status' => 'revision_required']);

        // 4. Buat catatan revisi baru
        $revision = DesignRevision::create([
            'design_id' => $design->id,
            'client_notes' => $request->client_notes,
            // Hitung ini revisi ke berapa
            'revision_number' => $design->revisions()->count() + 1,
        ]);

        return response()->json([
            'message' => 'Permintaan revisi telah dicatat. Menunggu upload dari studio.',
            'data' => $revision,
        ], 201);
    }
}
