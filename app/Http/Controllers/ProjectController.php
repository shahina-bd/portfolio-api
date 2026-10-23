<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of all portfolio projects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query();

        if ($request->has('featured')) {
            $query->where('featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
        }

        $projects = $query->latest()->get();

        return response()->json($projects);
    }

    /**
     * Display details of a specific project by id or slug.
     */
    public function show(string|int $id): JsonResponse
    {
        $project = is_numeric($id)
            ? Project::find($id)
            : Project::where('slug', $id)->first();

        if (! $project) {
            return response()->json([
                'message' => 'Project not found.',
            ], 404);
        }

        return response()->json($project);
    }

    /**
     * Create a new project (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'description' => 'required|string',
            'image' => 'nullable|string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string',
            'github_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'featured' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $count = 1;
            while (Project::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }
            $validated['slug'] = $slug;
        }

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Project created successfully.',
            'project' => $project,
        ], 201);
    }

    /**
     * Update an existing project (Admin only).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json([
                'message' => 'Project not found.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:projects,slug,' . $project->id,
            'description' => 'sometimes|required|string',
            'image' => 'nullable|string|max:255',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string',
            'github_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'featured' => 'nullable|boolean',
        ]);

        if (array_key_exists('slug', $validated) && empty($validated['slug']) && ! empty($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => $project,
        ]);
    }

    /**
     * Delete a project (Admin only).
     */
    public function destroy(int $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json([
                'message' => 'Project not found.',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }
}
