<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Danh sách blog
    public function index()
    {
        $blogs = Blog::paginate(15);
        return view('admin.blogs.index', compact('blogs'));
    }

    // Hiển thị form tạo blog mới
    public function create()
    {
        return view('admin.blogs.create');
    }

    // Lưu blog mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'nullable|string',
            'category' => 'required|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|integer',
        ]);

        // Chuyển đổi tags từ string sang array
        if ($validated['tags'] ?? null) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        if ($validated['author'] ?? null) {
            $validated['author'] = ['name' => $validated['author']];
        }

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Tạo bài blog thành công');
    }

    // Hiển thị chi tiết blog
    public function show(Blog $blog)
    {
        return view('admin.blogs.show', compact('blog'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    // Cập nhật blog
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs,slug,' . $blog->id,
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'nullable|string',
            'category' => 'required|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'read_time' => 'nullable|integer',
        ]);

        // Chuyển đổi tags từ string sang array
        if ($validated['tags'] ?? null) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        if ($validated['author'] ?? null) {
            $validated['author'] = ['name' => $validated['author']];
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Cập nhật bài blog thành công');
    }

    // Xóa blog
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Xóa bài blog thành công');
    }
}
