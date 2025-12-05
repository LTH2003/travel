import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api";

export const hotelApi = {
  getAll: () => axios.get(`${API_URL}/hotels`),
  getById: (id: number | string) => axios.get(`${API_URL}/hotels/${id}`),
};
