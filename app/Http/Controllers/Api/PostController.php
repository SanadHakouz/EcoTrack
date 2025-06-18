<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Get all posts for community feed (latest first)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $posts = Post::published()
                ->latest()
                ->with([
                    'user:id,name,username,profile_image',
                    'topLevelComments' => function($query) {
                        $query->latest()->take(3); // Show only 3 recent comments initially
                    }
                ])
                ->withCount(['reactions', 'comments'])
                ->paginate(10);

            // Add user's reaction to each post
            $user = $request->user();
            $posts->getCollection()->transform(function ($post) use ($user) {
                $post->user_reaction = $user ? $post->getUserReaction($user->id) : null;
                $post->reaction_counts = $post->getReactionCountsByType();
                return $post;
            });

            return response()->json([
                'success' => true,
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get posts for a specific user
     */
    public function getUserPosts(Request $request, $userId): JsonResponse
    {
        try {
            $posts = Post::where('user_id', $userId)
                ->published()
                ->latest()
                ->with([
                    'user:id,name,username,profile_image',
                    'topLevelComments' => function($query) {
                        $query->latest()->take(3);
                    }
                ])
                ->withCount(['reactions', 'comments'])
                ->paginate(10);

            // Add user's reaction to each post
            $user = $request->user();
            $posts->getCollection()->transform(function ($post) use ($user) {
                $post->user_reaction = $user ? $post->getUserReaction($user->id) : null;
                $post->reaction_counts = $post->getReactionCountsByType();
                return $post;
            });

            return response()->json([
                'success' => true,
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load user posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new post
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
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
            $imagePath = null;

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('post-images', 'public');
            }

            $post = Post::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->content,
                'image' => $imagePath,
                'is_published' => $request->get('is_published', true),
            ]);

            // Load relationships for response
            $post->load('user:id,name,username,profile_image');
            $post->reaction_counts = $post->getReactionCountsByType();
            $post->user_reaction = null;

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'post' => $post
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific post with comments
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        try {
            $post->load([
                'user:id,name,username,profile_image',
                'topLevelComments.user:id,name,username,profile_image',
                'topLevelComments.replies.user:id,name,username,profile_image'
            ]);

            $user = $request->user();
            $post->user_reaction = $user ? $post->getUserReaction($user->id) : null;
            $post->reaction_counts = $post->getReactionCountsByType();

            return response()->json([
                'success' => true,
                'post' => $post
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a post (only by owner)
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this post'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['title', 'content']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }
                $updateData['image'] = $request->file('image')->store('post-images', 'public');
            }

            $post->update($updateData);
            $post->load('user:id,name,username,profile_image');

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'post' => $post
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a post (only by owner)
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        // Check if user owns the post
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post'
            ], 403);
        }

        try {
            // Delete associated image
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle reaction on a post
     */
    public function toggleReaction(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:' . implode(',', PostReaction::AVAILABLE_TYPES)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reaction type',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $reactionType = $request->type;

            // Check if user already has a reaction on this post
            $existingReaction = $post->reactions()->where('user_id', $user->id)->first();

            if ($existingReaction) {
                if ($existingReaction->type === $reactionType) {
                    // Same reaction - remove it
                    $existingReaction->delete();
                    $message = 'Reaction removed';
                    $action = 'removed';
                } else {
                    // Different reaction - update it
                    $existingReaction->update(['type' => $reactionType]);
                    $message = 'Reaction updated';
                    $action = 'updated';
                }
            } else {
                // No existing reaction - create new one
                PostReaction::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => $reactionType
                ]);
                $message = 'Reaction added';
                $action = 'added';
            }

            // Update post reaction count
            $post->updateReactionCount();

            // Get updated reaction counts and user's current reaction
            $post->refresh();
            $userReaction = $post->getUserReaction($user->id);
            $reactionCounts = $post->getReactionCountsByType();

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $action,
                'user_reaction' => $userReaction,
                'reaction_counts' => $reactionCounts,
                'total_reactions' => $post->reactions_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle reaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a comment to a post
     */
    public function addComment(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id'
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

            $comment = PostComment::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'parent_id' => $request->parent_id,
                'content' => $request->content
            ]);

            $comment->load('user:id,name,username,profile_image');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for a post
     */
    public function getComments(Request $request, Post $post): JsonResponse
    {
        try {
            $comments = $post->topLevelComments()
                ->with(['user:id,name,username,profile_image', 'replies.user:id,name,username,profile_image'])
                ->oldest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'comments' => $comments->items(),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
