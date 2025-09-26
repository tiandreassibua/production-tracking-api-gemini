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
     * Store a newly created resource in storage.
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
}
