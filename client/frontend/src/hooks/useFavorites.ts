import { create } from 'zustand';
import { getFavorites, addFavorite, removeFavorite, checkFavorite } from '@/api/favoritesApi';

export interface FavoritesStore {
  hotelIds: number[];
  tourIds: number[];
  addFavorite: (type: 'hotel' | 'tour', id: number) => Promise<void>;
  removeFavorite: (type: 'hotel' | 'tour', id: number) => Promise<void>;
  isFavorited: (type: 'hotel' | 'tour', id: number) => boolean;
  loadFavorites: () => Promise<void>;
  checkAndLoad: (type: 'hotel' | 'tour', id: number) => Promise<boolean>;
}

export const useFavorites = create<FavoritesStore>((set, get) => ({
  hotelIds: [],
  tourIds: [],

  addFavorite: async (type: 'hotel' | 'tour', id: number) => {
    try {
      await addFavorite(type, id);
      set((state) => {
        if (type === 'hotel') {
          return {
            hotelIds: [...state.hotelIds, id].filter((v, i, a) => a.indexOf(v) === i),
          };
        } else {
          return {
            tourIds: [...state.tourIds, id].filter((v, i, a) => a.indexOf(v) === i),
          };
        }
      });
    } catch (error) {
      console.error('Lỗi khi thêm yêu thích:', error);
      throw error;
    }
  },

  removeFavorite: async (type: 'hotel' | 'tour', id: number) => {
    try {
      await removeFavorite(type, id);
      set((state) => {
        if (type === 'hotel') {
          return {
            hotelIds: state.hotelIds.filter((hid) => hid !== id),
          };
        } else {
          return {
            tourIds: state.tourIds.filter((tid) => tid !== id),
          };
        }
      });
    } catch (error) {
      console.error('Lỗi khi xóa yêu thích:', error);
      throw error;
    }
  },

  isFavorited: (type: 'hotel' | 'tour', id: number) => {
    const state = get();
    if (type === 'hotel') {
      return state.hotelIds.includes(id);
    } else {
      return state.tourIds.includes(id);
    }
  },

  loadFavorites: async () => {
    try {
      const data: any = await getFavorites();
      const hotelIds = (data.hotels || []).map((h: any) => h.id);
      const tourIds = (data.tours || []).map((t: any) => t.id);
      set({ hotelIds, tourIds });
    } catch (error) {
      console.error('Lỗi khi tải danh sách yêu thích:', error);
    }
  },

  checkAndLoad: async (type: 'hotel' | 'tour', id: number) => {
    try {
      const data: any = await checkFavorite(type, id);
      return data.is_favorited;
    } catch (error) {
      console.error('Lỗi khi kiểm tra yêu thích:', error);
      return false;
    }
  },
}));
