<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContributorRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContributorRequestController extends Controller
{
    /**
     * Get all contributor requests (Admin/Super Admin only)
     */
    public function index()
    {
        try {
            $requests = ContributorRequest::with('user', 'reviewedByUser')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => [
                            'id' => $request->user->id,
                            'name' => $request->user->name ?? $request->user->full_name,
                            'email' => $request->user->email,
                        ],
                        'status' => $request->status,
                        'message' => $request->message,
                        'admin_notes' => $request->admin_notes,
                        'reviewed_by' => $request->reviewedByUser ? [
                            'id' => $request->reviewedByUser->id,
                            'name' => $request->reviewedByUser->name ?? $request->reviewedByUser->full_name,
                        ] : null,
                        'reviewed_at' => $request->reviewed_at,
                        'created_at' => $request->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $requests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch contributor requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a request to become contributor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Check if user already has pending request
            $existingRequest = ContributorRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki request kontributor yang pending'
                ], 400);
            }

            // Check if user is already contributor
            $contributorRole = \App\Models\Role::where('name', 'contributor')->first();
            if ($user->role_id === $contributorRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah menjadi kontributor'
                ], 400);
            }

            $contributorRequest = ContributorRequest::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'message' => $request->message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request kontributor berhasil dikirim, tunggu persetujuan admin',
                'data' => [
                    'id' => $contributorRequest->id,
                    'status' => $contributorRequest->status,
                    'message' => $contributorRequest->message,
                    'created_at' => $contributorRequest->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit contributor request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has pending request
     */
    public function checkPending()
    {
        try {
            $user = Auth::user();
            $pendingRequest = ContributorRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            return response()->json([
                'success' => true,
                'has_pending' => $pendingRequest ? true : false,
                'data' => $pendingRequest ? [
                    'id' => $pendingRequest->id,
                    'status' => $pendingRequest->status,
                    'message' => $pendingRequest->message,
                    'created_at' => $pendingRequest->created_at,
                ] : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check pending request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve contributor request (Admin/Super Admin only)
     */
    public function approve(Request $request, $id)
    {
        try {
            $contributorRequest = ContributorRequest::findOrFail($id);

            if ($contributorRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request hanya bisa di-approve jika masih pending'
                ], 400);
            }

            // Get contributor role
            $contributorRole = \App\Models\Role::where('name', 'contributor')->firstOrFail();

            // Update user role to contributor
            $user = $contributorRequest->user;
            $user->update(['role_id' => $contributorRole->id]);

            // Update request status
            $contributorRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request kontributor berhasil di-approve',
                'data' => [
                    'id' => $contributorRequest->id,
                    'status' => $contributorRequest->status,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->full_name,
                        'role' => 'contributor',
                    ],
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contributor request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve contributor request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject contributor request (Admin/Super Admin only)
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|min:5|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contributorRequest = ContributorRequest::findOrFail($id);

            if ($contributorRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request hanya bisa di-reject jika masih pending'
                ], 400);
            }

            // Update request status
            $contributorRequest->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request kontributor berhasil di-reject',
                'data' => [
                    'id' => $contributorRequest->id,
                    'status' => $contributorRequest->status,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contributor request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject contributor request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
