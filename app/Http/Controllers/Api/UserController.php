<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only).
     */
    public function index(Request $request)
    {
        // Check if user is admin (add your own admin check logic)
        // if (!auth()->user()->is_admin) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized access'
        //     ], 403);
        // }
        
        $query = User::query();
        
        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by created date
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Ordering
        $orderBy = $request->input('order_by', 'created_at');
        $orderDir = $request->input('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        // Pagination
        $perPage = $request->input('per_page', 20);
        $users = $query->paginate($perPage);
        
        // Hide sensitive information
        $users->getCollection()->transform(function ($user) {
            return $user->makeHidden(['password', 'remember_token']);
        });
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * Store a newly created user (Admin registration or public signup).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // removed confirmed rule
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        // Create authentication token (optional, for auto-login after registration)
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Hide sensitive data
        $userData = $user->makeHidden(['password', 'remember_token']);
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => $userData,
                'token' => $token, // Optional, return if you want auto-login
                'token_type' => 'Bearer'
            ]
        ], 201);
    }
    
    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        // If showing own profile, allow. Otherwise check permissions
        // if (auth()->id() != $id && !auth()->user()->is_admin) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized access'
        //     ], 403);
        // }
        
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Hide sensitive information
        $user->makeHidden(['password', 'remember_token']);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    /**
     * Update the specified user.
     */
  public function update(Request $request, string $id)
{
    $user = User::find($id);
    
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }
    
    $validator = Validator::make($request->all(), [
        'first_name' => 'sometimes|string|max:100',
        'last_name' => 'sometimes|string|max:100',
        'email' => [
            'sometimes',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($id),
        ],
        // Remove password_confirmation
        'password' => 'sometimes|string|min:8',
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }
    
    // Update basic information
    if ($request->has('first_name')) {
        $user->first_name = $request->first_name;
    }
    
    if ($request->has('last_name')) {
        $user->last_name = $request->last_name;
    }
    
    if ($request->has('email') && $request->email !== $user->email) {
        $user->email = $request->email;
    }
    
    // Update password if provided (no current password check)
    if ($request->has('password')) {
        $user->password = Hash::make($request->password);
    }
    
    $user->save();
    
    // Hide sensitive information
    $user->makeHidden(['password', 'remember_token']);
    
    return response()->json([
        'success' => true,
        'message' => 'User updated successfully',
        'data' => $user
    ]);
}
    
    /**
     * Remove the specified user (Admin only).
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Check if user is trying to delete themselves (optional restriction)
        // if (auth()->id() == $id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'You cannot delete your own account'
        //     ], 403);
        // }
        
        // Check if admin (uncomment when you have admin role)
        // if (!auth()->user()->is_admin) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized access'
        //     ], 403);
        // }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    
    /**
     * Get current authenticated user profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->makeHidden(['password', 'remember_token']);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    /**
     * Update current authenticated user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|string|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update basic information
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        
        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            // Optional: require email verification again
            // $user->email_verified_at = null;
        }
        
        // Update password if provided
        if ($request->has('password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        $user->makeHidden(['password', 'remember_token']);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
    
    /**
     * Search users by name or email.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $users = User::where('first_name', 'like', "%{$request->query}%")
                    ->orWhere('last_name', 'like', "%{$request->query}%")
                    ->orWhere('email', 'like', "%{$request->query}%")
                    ->limit(10)
                    ->get()
                    ->makeHidden(['password', 'remember_token']);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * Check if email exists (for registration validation).
     */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $exists = User::where('email', $request->email)->exists();
        
        return response()->json([
            'success' => true,
            'data' => [
                'email' => $request->email,
                'exists' => $exists,
                'available' => !$exists,
            ]
        ]);
    }
}