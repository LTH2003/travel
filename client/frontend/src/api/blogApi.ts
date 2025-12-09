// src/api/blogApi.ts
import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api";

// Transform API response data
const transformBlogData = (data: any) => {
  if (Array.isArray(data)) {
    return data.map(blog => ({
      ...blog,
      publishedAt: blog.published_at || blog.publishedAt,
      readTime: blog.read_time || blog.readTime,
    }));
  }
  return {
    ...data,
    publishedAt: data.published_at || data.publishedAt,
    readTime: data.read_time || data.readTime,
  };
};

export const blogApi = {
  getAll: () => {
    return axios.get(`${API_URL}/blog`).then(response => {
      // API trả về array trực tiếp
      let blogs = response.data;
      if (!Array.isArray(blogs)) {
        blogs = blogs.value || blogs.data || [];
      }
      const transformedData = transformBlogData(blogs);
      
      return {
        data: transformedData,
      };
    });
  },
  getById: (id: number) => {
    return axios.get(`${API_URL}/blog/${id}`).then(response => {
      const transformedData = transformBlogData(response.data);
      return {
        data: transformedData,
      };
    });
  },
  
  // 📈 Tăng view count khi ai xem bài viết
  incrementView: (id: number) => axios.post(`${API_URL}/blog/${id}/increment-view`),
  
  // 📈 Tăng view count bằng slug
  incrementViewBySlug: (slug: string) => axios.post(`${API_URL}/blog/slug/${slug}/increment-view`),
};
