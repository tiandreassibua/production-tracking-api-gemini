<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;

class DashboardController extends Controller
{
    public function projectsOverview()
    {
        $activeProjects = Project::whereNotIn('status', ['completed', 'cancelled'])
            ->with('client') // Eager load client
            ->latest()
            ->get();

        $stats = [
            'total_active_projects' => $activeProjects->count(),
            'projects_in_progress' => $activeProjects->where('status', 'in_progress')->count(),
            'projects_in_delivery' => $activeProjects->where('status', 'delivery')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'projects' => ProjectResource::collection($activeProjects),
        ]);
    }
}
