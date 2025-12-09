import axiosClient from "./axiosClient";

export const blogCommentApi = {
  // 📖 Lấy tất cả comment của một bài blog
  getComments: (blogId: number) => {
    return axiosClient.get(`/blog-comments/${blogId}`);
  },

  // 💬 Tạo comment mới
  createComment: (blogId: number, data: { content: string; parent_id?: number }) => {
    return axiosClient.post(`/blog-comments/${blogId}`, data);
  },

  // ✏️ Cập nhật comment
  updateComment: (commentId: number, data: { content: string }) => {
    return axiosClient.put(`/blog-comments/${commentId}`, data);
  },

  // 🗑️ Xóa comment
  deleteComment: (commentId: number) => {
    return axiosClient.delete(`/blog-comments/${commentId}`);
  },

  // 📖 Lấy comments theo slug
  getCommentsBySlug: (slug: string) => {
    return axiosClient.get(`/blog-comments/slug/${slug}`);
  },
};
