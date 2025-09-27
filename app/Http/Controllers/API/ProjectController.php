<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/projects",
     * summary="Get list of projects",
     * tags={"Projects"},
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden"
     * )
     * )
     */


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ijinkan user dengan otorisasi "view project"
        Gate::authorize("view project");

        // Ambil semua proyek beserta relasi marketing dan client
        // Untuk menghindari N+1 problem
        $projects = Project::with(['marketing', 'client'])->latest()->get();

        return ProjectResource::collection($projects);
    }

    /**
     * @OA\Post(
     * path="/api/projects",
     * summary="Store new project",
     * tags={"Projects"},
     * security={{"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "client_id"},
     * @OA\Property(property="name", type="string", example="Proyek Renovasi Kantor"),
     * @OA\Property(property="description", type="string", example="Renovasi interior kantor pusat"),
     * @OA\Property(property="client_id", type="integer", example=1),
     * @OA\Property(property="due_date", type="string", format="date", example="2025-12-31")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Project created successfully"
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error"
     * )
     * )
     */
    public function store(StoreProjectRequest $request)
    {
        // Validasi sudah otomatis dijalankan oleh StoreProjectRequest
        $validatedData = $request->validated();

        // Tambahkan ID user marketing yang sedang login
        $validatedData['marketing_id'] = auth()->id();

        // Status awal proyek
        $validatedData['status'] = 'negotiation';

        $project = Project::create($validatedData);

        return response()->json([
            'message' => 'Proyek baru berhasil dibuat',
            'data' => $project
        ], 201); // 201 artinya 'Created'
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        Gate::authorize("view project");

        $project->load(['marketing', 'client', 'items', 'invoices']);
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validatedData = $request->validated();
        $project->update($validatedData);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Gate::authorize(ability: "delete project");

        $project->delete();

        return response()->json([
            'message' => 'Proyek berhasil dihapus'
        ], 200);
    }

    /**
     * Confirm material availability and start production.
     */
    public function confirmMaterials(Project $project)
    {
        Gate::authorize('confirm materials');

        // Pastikan proyek dalam status 'pending' sebelum dilanjutkan
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Proyek ini tidak dalam status menunggu konfirmasi bahan.'], 422);
        }

        $project->update(['status' => 'in_progress']);

        return response()->json([
            'message' => 'Ketersediaan bahan telah dikonfirmasi. Proyek sekarang dalam tahap produksi.',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Mark project as having passed quality control.
     */
    public function passQualityControl(Project $project)
    {
        // Kita gunakan permission yang sudah ada
        Gate::authorize('update project status');

        // Proyek harus dalam status 'in_progress' atau sudah lunas sebagian
        if ($project->status !== 'in_progress') {
            return response()->json(['message' => 'Proyek ini belum selesai diproduksi.'], 422);
        }

        // Status proyek berubah menjadi 'delivery', siap untuk dikirim
        $project->update(['status' => 'delivery']);

        return response()->json([
            'message' => 'Proyek telah lulus Quality Control dan siap untuk dikirim.',
            'data' => new ProjectResource($project),
        ]);
    }
}
