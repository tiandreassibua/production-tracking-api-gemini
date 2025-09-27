<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectItemRequest;
use App\Models\ProjectItem;
use App\Models\Project;

class ProjectItemController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectItemRequest $request, ProjectItem $projectItem)
    {
        $projectItem->update($request->validated());

        // Ambil parent project
        $project = $projectItem->project;
        // Hitung rata-rata progress dari semua item milik project ini
        $averageProgress = $project->items()->avg('progress');
        // Update progress project utama
        $project->update(['progress' => round($averageProgress)]);

        return response()->json([
            'message' => 'Progress item berhasil diperbarui.',
            'data' => $projectItem,
        ]);
    }
}
