<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Lấy tất cả blog
    public function index()
    {
        return response()->json(Blog::all());
    }

    // Lấy blog theo ID
    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }
        return response()->json($blog);
    }

    // Lấy blog theo slug
    public function showBySlug($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }
        return response()->json($blog);
    }

    // Tạo blog mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'nullable|array',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|integer',
            'views' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
        ]);

        $blog = Blog::create($validated);
        return response()->json($blog, 201);
    }

    // Cập nhật blog
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:blogs,slug,' . $id,
            'excerpt' => 'sometimes|string',
            'content' => 'sometimes|string',
            'author' => 'nullable|array',
            'category' => 'sometimes|string',
            'tags' => 'nullable|array',
            'image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|integer',
            'views' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
        ]);

        $blog->update($validated);
        return response()->json($blog);
    }

    // Xóa blog
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $blog->delete();
        return response()->json(['message' => 'Blog đã được xóa thành công']);
    }

    // 📈 Tăng view count khi ai xem bài viết
    public function incrementView($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        // Tăng view lên 1
        $blog->increment('views');

        return response()->json([
            'message' => 'View count đã được cập nhật',
            'views' => $blog->views
        ]);
    }

    // 📈 Tăng view count bằng slug
    public function incrementViewBySlug($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        // Tăng view lên 1
        $blog->increment('views');

        return response()->json([
            'message' => 'View count đã được cập nhật',
            'views' => $blog->views
        ]);
    }
}
