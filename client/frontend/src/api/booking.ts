import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

// Get token from localStorage
const getToken = () => localStorage.getItem('token');

const bookingAPI = {
  // Complete booking after QR scan
  completeBooking: async (orderId) => {
    const response = await axios.post(`${API_URL}/bookings/complete/${orderId}`, {}, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Get purchase history
  getPurchaseHistory: async () => {
    const response = await axios.get(`${API_URL}/bookings/history`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Get purchase detail
  getPurchaseDetail: async (purchaseId) => {
    const response = await axios.get(`${API_URL}/bookings/history/${purchaseId}`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Get all user bookings (pending + completed)
  getAllUserBookings: async () => {
    const response = await axios.get(`${API_URL}/bookings/all`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Verify booking (public)
  verifyBooking: async (orderId) => {
    const response = await axios.get(`${API_URL}/bookings/verify/${orderId}`);
    return response.data;
  },

  // Admin: Get all bookings
  getAllBookings: async (params = {}) => {
    const response = await axios.get(`${API_URL}/admin/bookings`, {
      params,
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Admin: Get bookings by item (tour/hotel)
  getBookingsByItem: async (itemType, itemId) => {
    const response = await axios.get(`${API_URL}/admin/bookings/item/${itemType}/${itemId}`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Admin: Get statistics
  getStats: async () => {
    const response = await axios.get(`${API_URL}/admin/bookings/stats`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
    });
    return response.data;
  },

  // Admin: Export CSV
  exportBookings: async (dateFrom = '', dateTo = '') => {
    const params = new URLSearchParams();
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    const response = await axios.get(`${API_URL}/admin/bookings/export?${params}`, {
      headers: {
        Authorization: `Bearer ${getToken()}`,
      },
      responseType: 'blob',
    });
    
    // Download CSV
    const url = window.URL.createObjectURL(response.data as Blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `bookings-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    link.parentElement?.removeChild(link);
    window.URL.revokeObjectURL(url);
  },
};

export default bookingAPI;
