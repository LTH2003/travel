<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCommentController extends Controller
{
    // 📖 Lấy tất cả comment của một bài blog
    public function getComments($blogId)
    {
        $blog = Blog::find($blogId);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $comments = BlogComment::where('blog_id', $blogId)
            ->where('parent_id', null) // Chỉ lấy comment gốc
            ->where('is_approved', true)
            ->with(['user', 'replies.user']) // Load user info và replies
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $comments,
            'count' => $comments->count(),
        ]);
    }

    // 💬 Tạo comment mới
    public function store(Request $request, $blogId)
    {
        $blog = Blog::find($blogId);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:5000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $user = Auth::user();

        $comment = BlogComment::create([
            'blog_id' => $blogId,
            'user_id' => $user?->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => true, // Tự động approve (có thể thay đổi thành false)
        ]);

        $comment->load('user');

        return response()->json([
            'status' => true,
            'message' => 'Bình luận đã được tạo thành công',
            'data' => $comment,
        ], 201);
    }

    // ✏️ Cập nhật comment
    public function update(Request $request, $commentId)
    {
        $comment = BlogComment::find($commentId);
        if (!$comment) {
            return response()->json(['message' => 'Bình luận không tồn tại'], 404);
        }

        $user = Auth::user();
        if ($comment->user_id !== $user?->id && $user?->role !== 'admin') {
            return response()->json(['message' => 'Không có quyền chỉnh sửa'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:5000',
        ]);

        $comment->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Bình luận đã được cập nhật',
            'data' => $comment,
        ]);
    }

    // 🗑️ Xóa comment
    public function destroy($commentId)
    {
        $comment = BlogComment::find($commentId);
        if (!$comment) {
            return response()->json(['message' => 'Bình luận không tồn tại'], 404);
        }

        $user = Auth::user();
        if ($comment->user_id !== $user?->id && $user?->role !== 'admin') {
            return response()->json(['message' => 'Không có quyền xóa'], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Bình luận đã được xóa',
        ]);
    }

    // Lấy comments theo slug (alternative method)
    public function getCommentsBySlug($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        return $this->getComments($blog->id);
    }
}
