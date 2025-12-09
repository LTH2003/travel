import { useState, useEffect, useMemo } from 'react';
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Calendar,
  Clock,
  Eye,
  Search,
  Tag,
} from "lucide-react";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { blogApi } from "@/api/blogApi";
import { useTitle } from "@/hooks/useTitle";

export default function Blog() {
  useTitle("Blog Du Lịch TravelVN - Cẩm nang và Kinh nghiệm du lịch");

  const [blogs, setBlogs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("Tất cả");
  const [sortBy, setSortBy] = useState("newest");

  // 🧭 Gọi API
  useEffect(() => {
    blogApi
      .getAll()
      .then((res) => setBlogs(res.data as any[]))
      .catch((err) => setError(err.message))
      .then(() => setLoading(false));
  }, []);

  // 🧩 Lấy danh mục từ dữ liệu API (memoized)
  const categories = useMemo(
    () => ["Tất cả", ...Array.from(new Set(blogs.map((b) => b.category)))],
    [blogs]
  );

  // 🧮 Filter & Sort (memoized - only recalculate when dependencies change)
  const filteredPosts = useMemo(() => {
    return blogs
      .filter((post) => {
        // Nếu có searchTerm thì filter, nếu không thì show tất cả
        let matchesSearch = true;
        if (searchTerm.trim()) {
          matchesSearch =
            post.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
            post.excerpt?.toLowerCase().includes(searchTerm.toLowerCase()) ||
            post.tags?.some((tag: string) =>
              tag.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        const matchesCategory =
          selectedCategory === "Tất cả" || post.category === selectedCategory;

        return matchesSearch && matchesCategory;
      })
      .sort((a, b) => {
        switch (sortBy) {
          case "oldest":
            return (
              new Date(a.publishedAt).getTime() -
              new Date(b.publishedAt).getTime()
            );
          case "popular":
            return b.views - a.views;
          case "shortest":
            return a.readTime - b.readTime;
          case "longest":
            return b.readTime - a.readTime;
          default:
            return (
              new Date(b.publishedAt).getTime() -
              new Date(a.publishedAt).getTime()
            );
        }
      });
  }, [blogs, searchTerm, selectedCategory, sortBy]);

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("vi-VN", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  };

  const formatViews = (views: number) => {
    if (views >= 1000) return (views / 1000).toFixed(1) + "k";
    return views.toString();
  };

  if (loading)
    return (
      <div className="text-center py-20 text-gray-500">Đang tải dữ liệu...</div>
    );

  if (error)
    return (
      <div className="text-center py-20 text-red-500">
        Lỗi tải dữ liệu: {error}
      </div>
    );

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      {/* Hero Section */}
      <div className="bg-gradient-to-r from-green-600 to-blue-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">
            Blog Du Lịch TravelVN
          </h1>
          <p className="text-xl mb-8">
            Khám phá thế giới qua những câu chuyện và kinh nghiệm thú vị
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-3">
            {/* Search and Filter */}
            <Card className="mb-8">
              <CardHeader>
                <CardTitle className="flex items-center">
                  <Search className="h-5 w-5 mr-2" />
                  Tìm kiếm bài viết
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="relative">
                    <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                    <Input
                      placeholder="Tìm kiếm bài viết..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="pl-10"
                    />
                  </div>
                  <Select
                    value={selectedCategory}
                    onValueChange={setSelectedCategory}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Chọn danh mục" />
                    </SelectTrigger>
                    <SelectContent>
                      {categories.map((category) => (
                        <SelectItem key={category} value={category}>
                          {category}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <Select value={sortBy} onValueChange={setSortBy}>
                    <SelectTrigger>
                      <SelectValue placeholder="Sắp xếp theo" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="newest">Mới nhất</SelectItem>
                      <SelectItem value="oldest">Cũ nhất</SelectItem>
                      <SelectItem value="popular">Phổ biến nhất</SelectItem>
                      <SelectItem value="shortest">Đọc nhanh nhất</SelectItem>
                      <SelectItem value="longest">Đọc lâu nhất</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </CardContent>
            </Card>

            {/* Results */}
            <div className="mb-6">
              <h2 className="text-2xl font-bold text-gray-900">
                Tìm thấy {filteredPosts.length} bài viết
              </h2>
            </div>

            {/* Loading State */}
            {loading && (
              <div className="text-center py-12">
                <div className="inline-block">
                  <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
                <p className="text-gray-600 mt-4">Đang tải bài viết...</p>
              </div>
            )}

            {/* Error State */}
            {error && !loading && (
              <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <p className="text-red-600 font-semibold">Lỗi: {error}</p>
              </div>
            )}

            {/* Blog List */}
            {!loading && !error && filteredPosts.length > 0 && (
            <div className="space-y-6">
              {filteredPosts.map((post) => (
                <Card
                  key={post.id}
                  className="overflow-hidden hover:shadow-lg transition-shadow"
                >
                  <div className="md:flex">
                    <div className="md:w-1/3">
                      <img
                        src={post.image}
                        alt={post.title}
                        className="w-full h-48 md:h-full object-cover"
                      />
                    </div>
                    <div className="md:w-2/3">
                      <CardContent className="p-6">
                        <div className="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                          <Badge variant="secondary">{post.category}</Badge>
                          <div className="flex items-center">
                            <Calendar className="h-4 w-4 mr-1" />
                            {formatDate(post.publishedAt)}
                          </div>
                          <div className="flex items-center">
                            <Clock className="h-4 w-4 mr-1" />
                            {post.readTime} phút đọc
                          </div>
                          <div className="flex items-center">
                            <Eye className="h-4 w-4 mr-1" />
                            {formatViews(post.views)} lượt xem
                          </div>
                        </div>

                        <h3 className="text-xl font-bold mb-2 hover:text-blue-600 transition-colors">
                          <Link to={`/blog/${post.slug}`}>{post.title}</Link>
                        </h3>

                        <p className="text-gray-600 mb-4 line-clamp-2">
                          {post.excerpt}
                        </p>

                        <div className="flex items-center justify-between">
                          <div className="flex items-center space-x-2">
                            <img
                              src={post.author?.avatar || "/default-avatar.png"}
                              alt={post.author?.name || "Tác giả"}
                              className="w-8 h-8 rounded-full"
                            />
                            <span className="text-sm text-gray-600">
                              {post.author?.name || "Ẩn danh"}
                            </span>
                          </div>

                          <Link to={`/blog/${post.slug}`}>
                            <Button variant="outline" size="sm">
                              Đọc thêm
                            </Button>
                          </Link>
                        </div>
                      </CardContent>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
            )}

            {filteredPosts.length === 0 && !loading && (
              <div className="text-center py-12">
                <Search className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                <h3 className="text-xl font-semibold text-gray-600 mb-2">
                  Không tìm thấy bài viết phù hợp
                </h3>
                <p className="text-gray-500 mb-4">
                  Hãy thử thay đổi từ khóa tìm kiếm hoặc danh mục
                </p>
                <Button
                  onClick={() => {
                    setSearchTerm("");
                    setSelectedCategory("Tất cả");
                  }}
                >
                  Xóa bộ lọc
                </Button>
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Categories */}
            <Card>
              <CardHeader>
                <CardTitle>Danh mục</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-2">
                  {categories.slice(1).map((category) => {
                    const count = blogs.filter(
                      (post) => post.category === category
                    ).length;
                    return (
                      <div
                        key={category}
                        className="flex items-center justify-between cursor-pointer hover:text-blue-600"
                        onClick={() => setSelectedCategory(category)}
                      >
                        <span className="text-sm">{category}</span>
                        <Badge variant="secondary" className="text-xs">
                          {count}
                        </Badge>
                      </div>
                    );
                  })}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
      <Footer />
    </div>
  );
}
