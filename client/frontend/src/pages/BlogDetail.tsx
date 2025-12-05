import { useParams, Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import {
  Calendar,
  Clock,
  Eye,
  Heart,
  Share2,
  ArrowLeft,
  User,
  Tag,
} from "lucide-react";
import Header from "@/components/Header";
import { blogApi } from "@/api/blogApi";
import { toast } from "@/hooks/use-toast";

export default function BlogDetail() {
  const { slug } = useParams();
  const [post, setPost] = useState<any | null>(null);
  const [relatedPosts, setRelatedPosts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // 📡 Gọi API để lấy bài viết chi tiết
  useEffect(() => {
    setLoading(true);
    setError(null);

    blogApi
      .getAll() // Gọi toàn bộ bài viết (vì backend chưa có API getBySlug)
      .then((res) => {
        const data = res.data as any[];
        const currentPost = data.find((p) => p.slug === slug);
        if (currentPost) {
          setPost(currentPost);

          // Lấy các bài viết liên quan
          const related = data
            .filter(
              (p) => p.category === currentPost.category && p.id !== currentPost.id
            )
            .slice(0, 3);
          setRelatedPosts(related);
        } else {
          setError("Bài viết không tồn tại");
        }
      })
      .catch((err) => setError(err.message))
      .then(() => setLoading(false));
  }, [slug]);

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("vi-VN", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  };

  const handleShare = () => {
    if (!post) return;
    if (navigator.share) {
      navigator.share({
        title: post.title,
        text: post.excerpt,
        url: window.location.href,
      });
    } else {
      navigator.clipboard.writeText(window.location.href);
      toast({
        title: "Đã sao chép link",
        description: "Link bài viết đã được sao chép vào clipboard",
      });
    }
  };

  const handleLike = () => {
    toast({
      title: "Cảm ơn bạn!",
      description: "Bạn đã thích bài viết này",
    });
  };

  // 🕐 Loading state
  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-gray-500 text-lg">Đang tải bài viết...</p>
      </div>
    );
  }

  // ⚠️ Lỗi hoặc không có bài viết
  if (error || !post) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16 text-center">
          <h1 className="text-2xl font-bold text-gray-900 mb-4">
            {error || "Bài viết không tồn tại"}
          </h1>
          <Link to="/blog">
            <Button>
              <ArrowLeft className="h-4 w-4 mr-2" />
              Quay lại Blog
            </Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      <div className="container mx-auto px-4 py-8">
        {/* Breadcrumb */}
        <div className="flex items-center space-x-2 text-sm text-gray-600 mb-6">
          <Link to="/blog" className="hover:text-blue-600 flex items-center">
            <ArrowLeft className="h-4 w-4 mr-1" />
            Blog
          </Link>
          <span>/</span>
          <span className="text-gray-900">{post.category}</span>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-3">
            <Card>
              <div className="relative">
                <img
                  src={post.image}
                  alt={post.title}
                  className="w-full h-64 md:h-80 object-cover rounded-t-lg"
                />
                <Badge className="absolute top-4 left-4 bg-blue-600">
                  {post.category}
                </Badge>
              </div>

              <CardContent className="p-6 md:p-8">
                {/* Meta Info */}
                <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                  <div className="flex items-center">
                    <Calendar className="h-4 w-4 mr-1" />
                    <span>{formatDate(post.publishedAt)}</span>
                  </div>
                  <div className="flex items-center">
                    <Clock className="h-4 w-4 mr-1" />
                    <span>{post.readTime} phút đọc</span>
                  </div>
                  <div className="flex items-center">
                    <Eye className="h-4 w-4 mr-1" />
                    <span>{post.views} lượt xem</span>
                  </div>
                  <div className="flex items-center">
                    <Heart className="h-4 w-4 mr-1" />
                    <span>{post.likes} lượt thích</span>
                  </div>
                </div>

                {/* Title */}
                <h1 className="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                  {post.title}
                </h1>

                {/* Author */}
                {post.author && (
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                      <img
                        src={post.author.avatar}
                        alt={post.author.name}
                        className="w-10 h-10 rounded-full mr-3"
                      />
                      <div>
                        <div className="font-medium text-gray-900">
                          {post.author.name}
                        </div>
                        <div className="text-sm text-gray-600">Tác giả</div>
                      </div>
                    </div>

                    <div className="flex items-center space-x-2">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={handleLike}
                        className="flex items-center"
                      >
                        <Heart className="h-4 w-4 mr-1" />
                        Thích
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={handleShare}
                        className="flex items-center"
                      >
                        <Share2 className="h-4 w-4 mr-1" />
                        Chia sẻ
                      </Button>
                    </div>
                  </div>
                )}

                <Separator className="mb-6" />

                {/* Content */}
                <div className="prose prose-lg max-w-none">
                  <div className="text-gray-700 leading-relaxed whitespace-pre-line">
                    {post.content}
                  </div>
                </div>

                <Separator className="my-6" />

                {/* Tags */}
                {post.tags && (
                  <div className="flex flex-wrap gap-2">
                    <Tag className="h-4 w-4 text-gray-600 mr-2" />
                    {post.tags.map((tag: string, index: number) => (
                      <Badge key={index} variant="outline" className="text-sm">
                        {tag}
                      </Badge>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-1 space-y-6">
            {/* Related Posts */}
            {relatedPosts.length > 0 && (
              <Card>
                <CardContent className="p-6">
                  <h3 className="font-semibold mb-4">Bài viết liên quan</h3>
                  <div className="space-y-4">
                    {relatedPosts.map((relatedPost) => (
                      <Link
                        key={relatedPost.id}
                        to={`/blog/${relatedPost.slug}`}
                        className="block group"
                      >
                        <div className="flex space-x-3">
                          <img
                            src={relatedPost.image}
                            alt={relatedPost.title}
                            className="w-16 h-16 object-cover rounded"
                          />
                          <div className="flex-1">
                            <h4 className="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2">
                              {relatedPost.title}
                            </h4>
                            <p className="text-xs text-gray-600 mt-1">
                              {formatDate(relatedPost.publishedAt)}
                            </p>
                          </div>
                        </div>
                      </Link>
                    ))}
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
