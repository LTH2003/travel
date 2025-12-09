<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    // 📖 Danh sách bình luận
    public function index(Request $request)
    {
        $query = BlogComment::with(['user', 'blog']);

        // Filter theo status approved
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved === 'true');
        }

        // Filter theo blog
        if ($request->filled('blog_id')) {
            $query->where('blog_id', $request->blog_id);
        }

        // Tìm kiếm theo content hoặc user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sắp xếp mới nhất trước
        $comments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thống kê
        $stats = [
            'total' => BlogComment::count(),
            'approved' => BlogComment::where('is_approved', true)->count(),
            'pending' => BlogComment::where('is_approved', false)->count(),
        ];

        // Danh sách blogs để filter
        $blogs = Blog::select('id', 'title')->orderBy('title')->get();

        return view('admin.blog-comments.index', compact('comments', 'stats', 'blogs'));
    }

    // 📝 Xem chi tiết bình luận
    public function show($id)
    {
        $comment = BlogComment::with(['user', 'blog'])->findOrFail($id);
        return view('admin.blog-comments.show', compact('comment'));
    }

    // ✏️ Chỉnh sửa bình luận
    public function edit($id)
    {
        $comment = BlogComment::findOrFail($id);
        return view('admin.blog-comments.edit', compact('comment'));
    }

    // 💾 Cập nhật bình luận
    public function update(Request $request, $id)
    {
        $comment = BlogComment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:5000',
            'is_approved' => 'required|boolean',
        ]);

        $comment->update($validated);

        return redirect()->route('admin.blog-comments.index')
                       ->with('success', 'Bình luận đã được cập nhật');
    }

    // 🗑️ Xóa bình luận
    public function destroy($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->delete();

        return redirect()->route('admin.blog-comments.index')
                       ->with('success', 'Bình luận đã được xóa');
    }

    // 👍 Approve multiple comments
    public function approveBulk(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        BlogComment::whereIn('id', $ids)->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Các bình luận đã được duyệt');
    }

    // 👎 Reject multiple comments
    public function rejectBulk(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        BlogComment::whereIn('id', $ids)->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Các bình luận đã bị từ chối');
    }

    // 🗑️ Delete multiple comments
    public function deleteBulk(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        BlogComment::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Các bình luận đã được xóa');
    }
}
