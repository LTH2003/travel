import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Star, MessageSquare, Edit2, Trash2 } from 'lucide-react';
import { toast } from '@/hooks/use-toast';
import { useAuth } from '@/hooks/useAuth';
import { tourReviewApi } from '@/api/tourReviewApi';

interface Review {
  id: number;
  user_id: number;
  rating: number;
  title?: string;
  comment?: string;
  is_approved: boolean;
  created_at: string;
  user: {
    id: number;
    name: string;
    avatar?: string;
  };
  can_edit?: boolean;
  can_delete?: boolean;
}
interface ReviewStats {
  average_rating: number;
  total_reviews: number;
  rating_distribution: Record<string, number>;
}

export default function TourReviews({ tourId, tourTitle, onReviewAdded }: { tourId: number; tourTitle: string; onReviewAdded?: () => void }) {
  const { user, isLoggedIn } = useAuth();
  const [reviews, setReviews] = useState<Review[]>([]);
  const [stats, setStats] = useState<ReviewStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  const [formData, setFormData] = useState({
    rating: 5,
    title: '',
    comment: '',
  });

  useEffect(() => {
    loadReviews();
  }, [tourId]);

  const loadReviews = async () => {
    try {
      setLoading(true);
      const res = await tourReviewApi.getReviews(tourId);
      const responseData = res.data as any;
      
      // Handle both paginated response and direct array
      let reviewsData: Review[] = [];
      if (Array.isArray(responseData.data)) {
        reviewsData = responseData.data;
      } else if (responseData.data && Array.isArray(responseData.data.data)) {
        reviewsData = responseData.data.data;
      }
      
      setReviews(reviewsData);
      setStats(responseData.stats || null);
      
      console.log('Loaded reviews:', reviewsData);
      console.log('Stats:', responseData.stats);
    } catch (error: any) {
      console.error('Failed to load reviews:', error);
      toast({
        title: 'Lỗi',
        description: 'Không thể tải danh sách đánh giá',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!isLoggedIn) {
      toast({
        title: 'Vui lòng đăng nhập',
        description: 'Bạn cần đăng nhập để đánh giá tour',
        variant: 'destructive',
      });
      return;
    }

    try {
      setIsSubmitting(true);

      const reviewData = {
        rating: formData.rating,
        title: formData.title.trim() || null,
        comment: formData.comment.trim() || null,
      };

      if (editingId) {
        await tourReviewApi.updateReview(editingId, reviewData);
        toast({
          title: 'Thành công',
          description: 'Cập nhật đánh giá thành công',
        });
        setEditingId(null);
      } else {
        await tourReviewApi.createReview(tourId, reviewData);
        toast({
          title: 'Thành công',
          description: 'Đánh giá của bạn đã được gửi thành công!',
        });
      }

      setFormData({ rating: 5, title: '', comment: '' });
      setShowForm(false);
      await loadReviews();
      
      // Callback to refresh tour data (rating and review_count)
      onReviewAdded?.();
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Gửi đánh giá thất bại',
        variant: 'destructive',
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async (reviewId: number) => {
    if (!confirm('Bạn có chắc muốn xóa đánh giá này?')) return;

    try {
      await tourReviewApi.deleteReview(reviewId);
      toast({
        title: 'Thành công',
        description: 'Xóa đánh giá thành công',
      });
      await loadReviews();
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Xóa đánh giá thất bại',
        variant: 'destructive',
      });
    }
  };

  const renderStars = (rating: number, clickable = false, onRate?: (r: number) => void) => (
    <div className="flex gap-1">
      {[1, 2, 3, 4, 5].map((star) => (
        <button
          key={star}
          type="button"
          onClick={() => clickable && onRate?.(star)}
          disabled={!clickable}
          className={clickable ? 'cursor-pointer hover:scale-110' : 'cursor-default'}
        >
          <Star
            className={`h-5 w-5 ${
              star <= rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'
            }`}
          />
        </button>
      ))}
    </div>
  );

  if (loading) {
    return (
      <Card>
        <CardContent className="p-8 text-center">
          <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
        </CardContent>
      </Card>
    );
  }

  return (
    <div className="space-y-6 mt-12">
      {/* Rating Summary */}
      {stats && (
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center">
              <Star className="h-5 w-5 mr-2 text-yellow-400 fill-yellow-400" />
              Đánh Giá ({stats.total_reviews})
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {/* Average Rating */}
              <div className="text-center">
                <div className="text-4xl font-bold text-gray-900 mb-2">
                  {stats.average_rating}
                </div>
                {renderStars(Math.round(stats.average_rating))}
                <p className="text-sm text-gray-600 mt-2">
                  Dựa trên {stats.total_reviews} đánh giá
                </p>
              </div>

              {/* Rating Distribution */}
              <div className="md:col-span-2">
                {[5, 4, 3, 2, 1].map((star) => (
                  <div key={star} className="flex items-center gap-2 mb-2">
                    <span className="w-8 text-sm font-medium">{star}★</span>
                    <div className="flex-1 bg-gray-200 rounded-full h-2">
                      <div
                        className="bg-yellow-400 h-2 rounded-full transition-all"
                        style={{
                          width: `${
                            stats.total_reviews > 0
                              ? ((stats.rating_distribution[star] || 0) / stats.total_reviews) * 100
                              : 0
                          }%`,
                        }}
                      ></div>
                    </div>
                    <span className="text-sm text-gray-600 w-12">
                      ({stats.rating_distribution[star] || 0})
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Review Form */}
      {isLoggedIn && (
        <Card>
          <CardHeader>
            <CardTitle>{editingId ? 'Chỉnh Sửa Đánh Giá' : 'Viết Đánh Giá'}</CardTitle>
            <CardDescription>Chia sẻ trải nghiệm của bạn về {tourTitle}</CardDescription>
          </CardHeader>
          <CardContent>
            {!showForm && !editingId ? (
              <Button onClick={() => setShowForm(true)} className="w-full" variant="outline">
                <MessageSquare className="h-4 w-4 mr-2" />
                Viết đánh giá
              </Button>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-4">
                {/* Rating */}
                <div>
                  <label className="text-sm font-medium block mb-2">Đánh giá</label>
                  {renderStars(formData.rating, true, (r) =>
                    setFormData({ ...formData, rating: r })
                  )}
                </div>

                {/* Title */}
                <div>
                  <label className="text-sm font-medium block mb-2">Tiêu đề (tùy chọn)</label>
                  <Input
                    value={formData.title}
                    onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                    placeholder="Tóm tắt đánh giá của bạn..."
                    maxLength={255}
                  />
                </div>

                {/* Comment */}
                <div>
                  <label className="text-sm font-medium block mb-2">Bình luận (tùy chọn)</label>
                  <Textarea
                    value={formData.comment}
                    onChange={(e) => setFormData({ ...formData, comment: e.target.value })}
                    placeholder="Chia sẻ chi tiết về trải nghiệm của bạn..."
                    rows={4}
                    maxLength={2000}
                  />
                  <p className="text-xs text-gray-500 mt-1">{formData.comment.length}/2000</p>
                </div>

                {/* Buttons */}
                <div className="flex gap-2">
                  <Button type="submit" disabled={isSubmitting} className="flex-1">
                    {isSubmitting ? 'Đang gửi...' : editingId ? 'Cập nhật' : 'Gửi đánh giá'}
                  </Button>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => {
                      setShowForm(false);
                      setEditingId(null);
                      setFormData({ rating: 5, title: '', comment: '' });
                    }}
                  >
                    Hủy
                  </Button>
                </div>
              </form>
            )}
          </CardContent>
        </Card>
      )}

      {/* Reviews List */}
      <div className="space-y-4">
        <h3 className="text-xl font-semibold">Các Đánh Giá</h3>

        {reviews.length === 0 ? (
          <Card>
            <CardContent className="p-8 text-center">
              <MessageSquare className="h-12 w-12 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-600">Chưa có đánh giá nào</p>
            </CardContent>
          </Card>
        ) : (
          reviews.map((review) => (
            <Card key={review.id}>
              <CardContent className="p-6">
                <div className="flex justify-between items-start mb-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-2">
                      <span className="font-semibold">{review.user.name}</span>
                      {!review.is_approved && (
                        <span className="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                          Chờ duyệt
                        </span>
                      )}
                    </div>
                    {renderStars(review.rating)}
                    {review.title && (
                      <p className="font-semibold text-gray-900 mt-2">{review.title}</p>
                    )}
                  </div>

                  {(review.can_edit || review.can_delete) && (
                    <div className="flex gap-2">
                      {review.can_edit && (
                        <Button
                          size="sm"
                          variant="ghost"
                          onClick={() => {
                            setEditingId(review.id);
                            setFormData({
                              rating: review.rating,
                              title: review.title || '',
                              comment: review.comment || '',
                            });
                            setShowForm(true);
                          }}
                        >
                          <Edit2 className="h-4 w-4" />
                        </Button>
                      )}
                      {review.can_delete && (
                        <Button
                          size="sm"
                          variant="ghost"
                          className="text-red-600 hover:text-red-700"
                          onClick={() => handleDelete(review.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      )}
                    </div>
                  )}
                </div>

                {review.comment && (
                  <p className="text-gray-700 mb-4">{review.comment}</p>
                )}

                <p className="text-sm text-gray-600">
                  {new Date(review.created_at).toLocaleDateString('vi-VN')}
                </p>
              </CardContent>
            </Card>
          ))
        )}
      </div>
    </div>
  );
}
