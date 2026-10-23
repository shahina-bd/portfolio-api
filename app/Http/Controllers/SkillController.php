<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display all technical skills.
     */
    public function index(): JsonResponse
    {
        $skills = Skill::all();

        return response()->json($skills);
    }

    /**
     * Store a new skill (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
        ]);

        $skill = Skill::create($validated);

        return response()->json([
            'message' => 'Skill added successfully.',
            'skill' => $skill,
        ], 201);
    }

    /**
     * Update an existing skill (Admin only).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::find($id);

        if (! $skill) {
            return response()->json([
                'message' => 'Skill not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'category' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
        ]);

        $skill->update($validated);

        return response()->json([
            'message' => 'Skill updated successfully.',
            'skill' => $skill,
        ]);
    }

    /**
     * Delete a skill (Admin only).
     */
    public function destroy(int $id): JsonResponse
    {
        $skill = Skill::find($id);

        if (! $skill) {
            return response()->json([
                'message' => 'Skill not found.',
            ], 404);
        }

        $skill->delete();

        return response()->json([
            'message' => 'Skill deleted successfully.',
        ]);
    }
}
