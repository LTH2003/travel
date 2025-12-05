import axiosClient from './axiosClient';

export interface RecommendationsResponse {
  recommended_hotels: any[];
  recommended_tours: any[];
  reason: string;
}

export const getRecommendations = async (): Promise<RecommendationsResponse> => {
  const response = await axiosClient.get('/recommendations');
  return response.data;
};
