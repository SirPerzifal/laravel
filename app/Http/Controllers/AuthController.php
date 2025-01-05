<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Get additional data based on role
            if ($user->role_id == 1) { // Mahasiswa
                $roleData = Mahasiswa::where('user_id', $user->id)->first();
            } elseif ($user->role_id == 2) { // Dosen
                $roleData = Dosen::where('user_id', $user->id)->first();
            } else {
                $roleData = null;
            }
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'role_data' => $roleData,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500); // Tangani error dalam format JSON
        }

    }

    public function changePassword(Request $request)
    {
        try {
            // Debugging awal untuk request payload
            \Log::info('Change password request received', $request->all());
    
            // Ambil user dari token autentikasi
            $user = Auth::user();
            if (!$user) {
                \Log::error('Unauthenticated user tried to change password');
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
    
            // Debug user yang terautentikasi
            \Log::info('Authenticated user', ['id' => $user->id, 'email' => $user->email]);
    
            // Validasi input
            $validated = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|confirmed|min:8',
            ]);
    
            // Verifikasi password lama
            if (!Hash::check($validated['current_password'], $user->password)) {
                \Log::warning('Incorrect current password', ['user_id' => $user->id]);
                return response()->json(['message' => 'Current password is incorrect'], 401);
            }
    
            // Update password
            $user->update([
                'password' => bcrypt($validated['new_password']),
            ]);
    
            \Log::info('Password successfully changed', ['user_id' => $user->id]);
    
            // Kembalikan respons sukses
            return response()->json(['message' => 'Password changed successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in change password', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in change password', [
                'exception' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }    

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Revoke all tokens for the user

        return response()->json(['message' => 'Logged out successfully']);
    }

}
