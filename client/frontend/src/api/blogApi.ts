// src/api/blogApi.ts
import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api";

export const blogApi = {
  getAll: () => axios.get(`${API_URL}/blog`),
  getById: (id: number) => axios.get(`${API_URL}/blog/${id}`),
  
  // 📈 Tăng view count khi ai xem bài viết
  incrementView: (id: number) => axios.post(`${API_URL}/blog/${id}/increment-view`),
  
  // 📈 Tăng view count bằng slug
  incrementViewBySlug: (slug: string) => axios.post(`${API_URL}/blog/slug/${slug}/increment-view`),
};
