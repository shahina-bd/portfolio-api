<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Retrieve profile information.
     */
    public function show(): JsonResponse
    {
        $profile = PersonalInfo::first();

        if (! $profile) {
            return response()->json([
                'message' => 'Profile information not found.',
            ], 404);
        }

        return response()->json($profile);
    }
}
