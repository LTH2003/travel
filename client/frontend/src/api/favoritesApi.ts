import axiosClient from './axiosClient';

export const getFavorites = async () => {
  try {
    const response = await axiosClient.get('/favorites');
    return response.data;
  } catch (error) {
    console.error('Lỗi khi lấy danh sách yêu thích:', error);
    throw error;
  }
};

export const addFavorite = async (type: 'hotel' | 'tour', id: number) => {
  try {
    const response = await axiosClient.post('/favorites', {
      type,
      id,
    });
    return response.data;
  } catch (error) {
    console.error('Lỗi khi thêm yêu thích:', error);
    throw error;
  }
};

export const removeFavorite = async (type: 'hotel' | 'tour', id: number) => {
  try {
    const response = await axiosClient.request({
      method: 'delete',
      url: '/favorites',
      data: {
        type,
        id,
      },
    });
    return response.data;
  } catch (error) {
    console.error('Lỗi khi xóa yêu thích:', error);
    throw error;
  }
};

export const checkFavorite = async (type: 'hotel' | 'tour', id: number) => {
  try {
    const response = await axiosClient.post('/favorites/check', {
      type,
      id,
    });
    return response.data;
  } catch (error) {
    console.error('Lỗi khi kiểm tra yêu thích:', error);
    throw error;
  }
};
