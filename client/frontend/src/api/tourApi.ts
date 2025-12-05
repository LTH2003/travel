import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api";

export const tourApi = {
  getAll: () => axios.get(`${API_URL}/tours`),
  getById: (id: number) => axios.get(`${API_URL}/tours/${id}`),
};
