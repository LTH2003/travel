import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import { getCart as fetchCartFromServer, saveCart as syncCartToServer } from '@/api/cartApi';

export interface HotelCartItem {
  id: string;
  type: 'hotel';
  hotelId: number;
  hotelName: string;
  hotelLocation: string;
  hotelImage: string;
  roomId: number;
  roomName: string;
  roomPrice: number;
  quantity: number;
  checkIn: Date;
  checkOut: Date;
  guests: number;
  specialRequests: string;
  totalPrice: number;
}

export interface TourCartItem {
  id: string;
  type: 'tour';
  tourId: number;
  tourName: string;
  tourLocation: string;
  tourImage: string;
  tourPrice: number;
  quantity: number;
  departureDate: Date;
  duration: number;
  guests: number;
  specialRequests: string;
  totalPrice: number;
}

export type CartItem = HotelCartItem | TourCartItem;

export interface CartStore {
  items: CartItem[];
  addItem: (item: CartItem) => void;
  removeItem: (id: string) => void;
  updateItem: (id: string, updates: Partial<CartItem>) => void;
  clearCart: () => void;
  getTotal: () => number;
  getItemCount: () => number;
  setItems: (items: CartItem[]) => void;
  syncToServer: () => Promise<void>;
  loadFromServer: () => Promise<void>;
}

export const useCart = create<CartStore>()(
  persist(
    (set, get) => ({
      items: [],
      
      addItem: (item: CartItem) => {
        set((state) => {
          let existingItem = null;
          
          if (item.type === 'hotel') {
            const hotelItem = item as HotelCartItem;
            existingItem = state.items.find(
              (i) =>
                i.type === 'hotel' &&
                (i as HotelCartItem).hotelId === hotelItem.hotelId &&
                (i as HotelCartItem).roomId === hotelItem.roomId &&
                (i as HotelCartItem).checkIn.toString() === hotelItem.checkIn.toString() &&
                (i as HotelCartItem).checkOut.toString() === hotelItem.checkOut.toString()
            );
          } else if (item.type === 'tour') {
            const tourItem = item as TourCartItem;
            existingItem = state.items.find(
              (i) =>
                i.type === 'tour' &&
                (i as TourCartItem).tourId === tourItem.tourId &&
                (i as TourCartItem).departureDate.toString() === tourItem.departureDate.toString()
            );
          }

          let newItems;
          if (existingItem) {
            newItems = state.items.map((i) => {
              if (i.id === existingItem!.id) {
                const newQty = i.quantity + item.quantity;
                let newTotal = 0;
                
                if (item.type === 'hotel') {
                  const hotelItem = item as HotelCartItem;
                  newTotal = newQty * hotelItem.roomPrice * getDays(hotelItem.checkIn, hotelItem.checkOut);
                } else {
                  const tourItem = item as TourCartItem;
                  newTotal = newQty * tourItem.tourPrice;
                }
                
                return {
                  ...i,
                  quantity: newQty,
                  totalPrice: newTotal,
                };
              }
              return i;
            });
          } else {
            newItems = [...state.items, item];
          }

          // Auto-sync to server
          const token = localStorage.getItem('token');
          if (token) {
            const total = newItems.reduce((sum, item) => sum + item.totalPrice, 0);
            syncCartToServer(newItems, total).catch(err => 
              console.error('Lỗi khi đồng bộ giỏ hàng:', err)
            );
          }

          return { items: newItems };
        });
      },

      removeItem: (id: string) => {
        set((state) => {
          const newItems = state.items.filter((item) => item.id !== id);
          
          // Auto-sync to server
          const token = localStorage.getItem('token');
          if (token) {
            const total = newItems.reduce((sum, item) => sum + item.totalPrice, 0);
            syncCartToServer(newItems, total).catch(err => 
              console.error('Lỗi khi đồng bộ giỏ hàng:', err)
            );
          }

          return { items: newItems };
        });
      },

      updateItem: (id: string, updates: Partial<CartItem>) => {
        set((state) => {
          const newItems = state.items.map((item) => {
            if (item.id !== id) return item;
            
            const updatedItem = { ...item, ...updates } as CartItem;
            
            if (updatedItem.type === 'hotel') {
              const hotelItem = updatedItem as HotelCartItem;
              const oldHotel = item as HotelCartItem;
              const qty = (updates as any).quantity ?? oldHotel.quantity;
              const price = (updates as any).roomPrice ?? oldHotel.roomPrice;
              const checkIn = (updates as any).checkIn ?? oldHotel.checkIn;
              const checkOut = (updates as any).checkOut ?? oldHotel.checkOut;
              updatedItem.totalPrice = qty * price * getDays(checkIn, checkOut);
            } else if (updatedItem.type === 'tour') {
              const tourItem = updatedItem as TourCartItem;
              const oldTour = item as TourCartItem;
              const qty = (updates as any).quantity ?? oldTour.quantity;
              const price = (updates as any).tourPrice ?? oldTour.tourPrice;
              updatedItem.totalPrice = qty * price;
            }
            
            return updatedItem;
          });

          // Auto-sync to server
          const token = localStorage.getItem('token');
          if (token) {
            const total = newItems.reduce((sum, item) => sum + item.totalPrice, 0);
            syncCartToServer(newItems, total).catch(err => 
              console.error('Lỗi khi đồng bộ giỏ hàng:', err)
            );
          }

          return { items: newItems };
        });
      },

      clearCart: () => {
        set({ items: [] });
        
        // Clear from server
        const token = localStorage.getItem('token');
        if (token) {
          syncCartToServer([], 0).catch(err => 
            console.error('Lỗi khi xóa giỏ hàng:', err)
          );
        }
      },

      getTotal: () => {
        return get().items.reduce((sum, item) => sum + item.totalPrice, 0);
      },

      getItemCount: () => {
        return get().items.reduce((sum, item) => sum + item.quantity, 0);
      },

      setItems: (items: CartItem[]) => {
        set({ items });
      },

      syncToServer: async () => {
        const state = get();
        try {
          await syncCartToServer(state.items, state.getTotal());
        } catch (error) {
          console.error('Lỗi khi đồng bộ giỏ hàng:', error);
        }
      },

      loadFromServer: async () => {
        try {
          const serverCart: any = await fetchCartFromServer();
          if (serverCart?.items && Array.isArray(serverCart.items)) {
            const items = serverCart.items.map((item: any) => {
              if (item.type === 'hotel') {
                return {
                  ...item,
                  checkIn: new Date(item.checkIn),
                  checkOut: new Date(item.checkOut),
                };
              } else if (item.type === 'tour') {
                return {
                  ...item,
                  departureDate: new Date(item.departureDate),
                };
              }
              return item;
            });
            set({ items });
          }
        } catch (error) {
          console.error('Lỗi khi tải giỏ hàng từ server:', error);
        }
      },
    }),
    {
      name: 'cart-storage',
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        items: state.items.map((item) => {
          const serialized: any = { ...item };
          if (item.type === 'hotel') {
            const hotel = item as HotelCartItem;
            serialized.checkIn = hotel.checkIn instanceof Date ? hotel.checkIn.toISOString() : hotel.checkIn;
            serialized.checkOut = hotel.checkOut instanceof Date ? hotel.checkOut.toISOString() : hotel.checkOut;
          } else if (item.type === 'tour') {
            const tour = item as TourCartItem;
            serialized.departureDate = tour.departureDate instanceof Date ? tour.departureDate.toISOString() : tour.departureDate;
          }
          return serialized;
        }) as any,
      }),
      merge: (persistedState: any, currentState) => {
        const items = persistedState?.items?.map((item: any) => {
          if (item.type === 'hotel') {
            return {
              ...item,
              checkIn: new Date(item.checkIn),
              checkOut: new Date(item.checkOut),
            };
          } else if (item.type === 'tour') {
            return {
              ...item,
              departureDate: new Date(item.departureDate),
            };
          }
          return item;
        }) ?? [];
        return { ...currentState, items };
      },
    }
  )
);

function getDays(checkIn: Date, checkOut: Date): number {
  const diffTime = Math.abs(checkOut.getTime() - checkIn.getTime());
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}
