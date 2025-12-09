import axiosClient from './axiosClient';

export const tourReviewApi = {
  getReviews: (tourId: number, page = 1) =>
    axiosClient.get(`/tours/${tourId}/reviews`, { params: { page } }),

  createReview: (tourId: number, data: any) =>
    axiosClient.post(`/tours/${tourId}/reviews`, data),

  updateReview: (reviewId: number, data: any) =>
    axiosClient.put(`/reviews/${reviewId}`, data),

  deleteReview: (reviewId: number) =>
    axiosClient.delete(`/reviews/${reviewId}`),
};
