<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetCode;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get user profile information
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'eco_score' => $user->eco_score,
                    'language' => $user->language,
                    'bio' => $user->bio,
                    'phone' => $user->phone,
                    'profile_image' => $user->profile_image,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users', 'username')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'language' => 'sometimes|in:en,de',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'bio' => $request->bio,
                'phone' => $request->phone,
                'language' => $request->language ?? $user->language,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'eco_score' => $user->eco_score,
                    'language' => $user->language,
                    'bio' => $user->bio,
                    'phone' => $user->phone,
                    'profile_image' => $user->profile_image,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Delete old avatar if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('profile-pictures', 'public');

            // Update user profile_image
            $user->update(['profile_image' => $avatarPath]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'profile_image' => $avatarPath,
                'profile_image_url' => Storage::disk('public')->url($avatarPath)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->update(['profile_image' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request password reset code
     */
    public function requestPasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found or not active'
                ], 404);
            }

            // Generate and save reset code
            $resetCode = PasswordResetCode::createForEmail($user->email);

            // Send email with code
            Mail::to($user->email)->send(new PasswordResetCodeMail(
                $user->name,
                $user->email,
                $resetCode->code
            ));

            return response()->json([
                'success' => true,
                'message' => 'Password reset code sent to your email address',
                'email' => $user->email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify password reset code
     */
    public function verifyResetCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $isValid = PasswordResetCode::verifyCode($request->email, $request->code);

            if ($isValid) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code verified successfully',
                    'email' => $request->email
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired code'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password with verified code
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:4',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if code was recently verified (within last 5 minutes)
            $recentlyUsedCode = PasswordResetCode::where('email', $request->email)
                ->where('code', $request->code)
                ->where('used', true)
                ->where('used_at', '>=', now()->subMinutes(5))
                ->first();

            if (!$recentlyUsedCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code not verified or verification expired. Please verify the code first.'
                ], 400);
            }

            // Find user and update password
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete all reset codes for this email
            PasswordResetCode::where('email', $request->email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get public profile data for any user
     */
    public function getPublicProfile(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Return public profile data
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'profile_image' => $user->profile_image,
                'eco_score' => $user->eco_score,
                'role' => $user->role,
                'created_at' => $user->created_at
            ];

            // Get actual posts count
            $postsCount = $user->posts()->published()->count();

            return response()->json([
                'success' => true,
                'user' => $profileData,
                'posts_count' => $postsCount
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load profile'
            ], 500);
        }
    }
}
