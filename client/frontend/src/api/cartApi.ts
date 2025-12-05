import axiosClient from './axiosClient';

export const getCart = async () => {
  try {
    const response = await axiosClient.get('/cart');
    return response.data;
  } catch (error) {
    console.error('Lỗi khi lấy giỏ hàng:', error);
    throw error;
  }
};

export const saveCart = async (items: any[], total: number) => {
  try {
    const response = await axiosClient.post('/cart', {
      items,
      total,
    });
    return response.data;
  } catch (error) {
    console.error('Lỗi khi lưu giỏ hàng:', error);
    throw error;
  }
};

export const clearCart = async () => {
  try {
    const response = await axiosClient.delete('/cart');
    return response.data;
  } catch (error) {
    console.error('Lỗi khi xóa giỏ hàng:', error);
    throw error;
  }
};
