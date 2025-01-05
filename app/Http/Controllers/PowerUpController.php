<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\PowerUp;
use App\Models\PowerUpUsage;

class PowerUpController extends Controller
{
    public function getAllPowerUps()
    {
        $powerUps = PowerUp::all(['id', 'name', 'deskripsi', 'effect', 'duration']); // Ambil semua power-up dengan kolom terkait

        return response()->json([
            'success' => true,
            'data' => $powerUps,
        ], 200);
    }

    public function storePowerUpUsage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'power_up_id' => 'required|exists:power_ups,id',
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $usage = PowerUpUsage::create([
                'user_id' => $request->user_id,
                'power_up_id' => $request->power_up_id,
                'quiz_id' => $request->quiz_id,
                'used_at' => now(), // Waktu penggunaan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Power-Up usage recorded successfully',
                'data' => $usage,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record Power-Up usage',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
