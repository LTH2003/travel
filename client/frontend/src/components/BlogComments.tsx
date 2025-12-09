import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { MessageCircle, Trash2, Edit, Send } from 'lucide-react';
import { blogCommentApi } from '@/api/blogCommentApi';
import { toast } from '@/hooks/use-toast';
import { formatDistanceToNow } from 'date-fns';
import { vi } from 'date-fns/locale';

interface Comment {
  id: number;
  content: string;
  user_id?: number;
  user?: {
    id: number;
    name: string;
    avatar?: string;
    email: string;
  };
  created_at: string;
  replies?: Comment[];
  is_approved: boolean;
}

interface BlogCommentsProps {
  blogId: number;
  currentUser?: any;
}

export default function BlogComments({ blogId, currentUser }: BlogCommentsProps) {
  const [comments, setComments] = useState<Comment[]>([]);
  const [newComment, setNewComment] = useState('');
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [editingContent, setEditingContent] = useState('');

  // Load comments
  useEffect(() => {
    loadComments();
  }, [blogId]);

  const loadComments = async () => {
    try {
      setLoading(true);
      const response = await blogCommentApi.getComments(blogId);
      setComments((response as any).data.data || []);
    } catch (error) {
      console.error('Failed to load comments:', error);
      toast({
        title: 'Lỗi',
        description: 'Không thể tải bình luận',
        variant: 'destructive',
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSubmitComment = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!newComment.trim()) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng nhập bình luận',
        variant: 'destructive',
      });
      return;
    }

    if (!currentUser) {
      toast({
        title: 'Vui lòng đăng nhập',
        description: 'Bạn cần đăng nhập để bình luận',
        variant: 'destructive',
      });
      return;
    }

    try {
      setSubmitting(true);
      await blogCommentApi.createComment(blogId, {
        content: newComment,
      });

      toast({
        title: 'Thành công',
        description: 'Bình luận đã được đăng',
      });

      setNewComment('');
      loadComments();
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Không thể đăng bình luận',
        variant: 'destructive',
      });
    } finally {
      setSubmitting(false);
    }
  };

  const handleEditComment = async (commentId: number) => {
    if (!editingContent.trim()) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng nhập nội dung',
        variant: 'destructive',
      });
      return;
    }

    try {
      await blogCommentApi.updateComment(commentId, {
        content: editingContent,
      });

      toast({
        title: 'Thành công',
        description: 'Bình luận đã được cập nhật',
      });

      setEditingId(null);
      setEditingContent('');
      loadComments();
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Không thể cập nhật bình luận',
        variant: 'destructive',
      });
    }
  };

  const handleDeleteComment = async (commentId: number) => {
    if (!confirm('Bạn chắc chắn muốn xóa bình luận này?')) return;

    try {
      await blogCommentApi.deleteComment(commentId);

      toast({
        title: 'Thành công',
        description: 'Bình luận đã được xóa',
      });

      loadComments();
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Không thể xóa bình luận',
        variant: 'destructive',
      });
    }
  };

  const renderComment = (comment: Comment) => {
    const initials = comment.user?.name?.split(' ').map(n => n[0]).join('') || '?';
    const timeAgo = formatDistanceToNow(new Date(comment.created_at), {
      addSuffix: true,
      locale: vi,
    });

    const isOwnComment = currentUser?.id === comment.user_id;

    return (
      <div key={comment.id} className="space-y-4">
        <div className="flex gap-4">
          <Avatar className="h-10 w-10">
            <AvatarImage src={comment.user?.avatar} alt={comment.user?.name} />
            <AvatarFallback>{initials}</AvatarFallback>
          </Avatar>

          <div className="flex-1">
            <div className="flex items-start justify-between">
              <div>
                <p className="font-semibold text-sm">{comment.user?.name || 'Ẩn danh'}</p>
                <p className="text-xs text-gray-500">{timeAgo}</p>
              </div>

              {isOwnComment && (
                <div className="flex gap-2">
                  <button
                    onClick={() => {
                      setEditingId(comment.id);
                      setEditingContent(comment.content);
                    }}
                    className="text-blue-600 hover:text-blue-700 text-xs"
                  >
                    <Edit className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => handleDeleteComment(comment.id)}
                    className="text-red-600 hover:text-red-700 text-xs"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </div>
              )}
            </div>

            {editingId === comment.id ? (
              <div className="mt-3 space-y-2">
                <Textarea
                  value={editingContent}
                  onChange={(e) => setEditingContent(e.target.value)}
                  placeholder="Chỉnh sửa bình luận..."
                  className="text-sm"
                  rows={3}
                />
                <div className="flex gap-2">
                  <Button
                    size="sm"
                    onClick={() => handleEditComment(comment.id)}
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    Lưu
                  </Button>
                  <Button
                    size="sm"
                    variant="outline"
                    onClick={() => {
                      setEditingId(null);
                      setEditingContent('');
                    }}
                  >
                    Hủy
                  </Button>
                </div>
              </div>
            ) : (
              <p className="text-sm text-gray-700 mt-2 whitespace-pre-wrap">{comment.content}</p>
            )}
          </div>
        </div>

        {comment.replies && comment.replies.length > 0 && (
          <div className="ml-8 space-y-4 border-l-2 border-gray-200 pl-4 py-2">
            {comment.replies.map((reply) => renderComment(reply))}
          </div>
        )}
      </div>
    );
  };

  return (
    <div className="space-y-6">
      {/* Comments Section Header */}
      <div className="flex items-center gap-2">
        <MessageCircle className="h-5 w-5" />
        <h3 className="text-lg font-semibold">Bình luận ({comments.length})</h3>
      </div>

      {/* New Comment Form */}
      {currentUser ? (
        <Card className="border border-blue-200 bg-blue-50">
          <CardContent className="pt-6">
            <form onSubmit={handleSubmitComment} className="space-y-4">
              <div>
                <p className="text-sm font-medium mb-2">Bình luận với tư cách: {currentUser.name}</p>
                <Textarea
                  value={newComment}
                  onChange={(e) => setNewComment(e.target.value)}
                  placeholder="Chia sẻ suy nghĩ của bạn về bài viết này..."
                  className="min-h-[100px] resize-none"
                  disabled={submitting}
                />
              </div>

              <Button
                type="submit"
                disabled={submitting || !newComment.trim()}
                className="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
              >
                {submitting ? (
                  <>Đang gửi...</>
                ) : (
                  <>
                    <Send className="h-4 w-4 mr-2" />
                    Gửi bình luận
                  </>
                )}
              </Button>
            </form>
          </CardContent>
        </Card>
      ) : (
        <Card className="border border-yellow-200 bg-yellow-50">
          <CardContent className="pt-6">
            <p className="text-sm text-yellow-800">
              Vui lòng <a href="/login" className="font-semibold text-blue-600 hover:underline">đăng nhập</a> để bình luận
            </p>
          </CardContent>
        </Card>
      )}

      {/* Comments List */}
      <div className="space-y-6">
        {loading ? (
          <div className="text-center py-8">
            <p className="text-gray-500">Đang tải bình luận...</p>
          </div>
        ) : comments.length === 0 ? (
          <div className="text-center py-8">
            <MessageCircle className="h-12 w-12 mx-auto text-gray-300 mb-2" />
            <p className="text-gray-500">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
          </div>
        ) : (
          <div className="space-y-6">
            {comments.map((comment) => (
              <div key={comment.id}>
                {renderComment(comment)}
                <Separator className="mt-4" />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
