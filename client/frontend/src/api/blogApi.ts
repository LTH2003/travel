// src/api/blogApi.ts
import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api";

export const blogApi = {
  getAll: () => axios.get(`${API_URL}/blog`),
  getById: (id: number) => axios.get(`${API_URL}/blog/${id}`),
};
