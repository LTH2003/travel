import { useEffect, useState } from "react";
import { getCurrentUser, logout as apiLogout } from "@/api/auth";
import { toast } from "@/hooks/use-toast";
import { useCart } from "./useCart";
import { useFavorites } from "./useFavorites";

export function useAuth() {
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const { loadFromServer, clearCart } = useCart();
  const { reset: resetFavorites } = useFavorites();

  useEffect(() => {
    const initAuth = async () => {
      try {
        const token = localStorage.getItem("token");
        
        // Check if there's cached user info - show immediately
        const cachedUser = localStorage.getItem("user");
        if (cachedUser && token) {
          try {
            const parsed = JSON.parse(cachedUser);
            setUser(parsed);
            setIsLoggedIn(true);
            // Don't set loading to false yet - wait for API call
          } catch (e) {
            // Invalid cache
          }
        }
        
        if (!token) {
          setLoading(false);
          setIsLoggedIn(false);
          clearCart();
          resetFavorites();
          return;
        }

        // Fetch fresh data from server with timeout
        try {
          // Create abort controller with 5 second timeout
          const controller = new AbortController();
          const timeoutId = setTimeout(() => controller.abort(), 5000);
          
          const userRes = await getCurrentUser();
          clearTimeout(timeoutId);
          
          setUser(userRes);
          setIsLoggedIn(true);
          // Update cache
          localStorage.setItem("user", JSON.stringify(userRes));
          
          // Load cart in background without blocking
          loadFromServer().catch(() => null);
        } catch (err) {
          console.error("❌ Không thể lấy thông tin user:", err);
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          setUser(null);
          setIsLoggedIn(false);
          clearCart();
          resetFavorites();
        }
      } finally {
        setLoading(false);
      }
    };

    initAuth();
  }, []);

  const logout = async () => {
    try {
      await apiLogout();
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      clearCart();
      resetFavorites();
      setUser(null);
      setIsLoggedIn(false);
      toast({ title: "Đăng xuất thành công" });
    } catch (err) {
      console.error("Lỗi khi đăng xuất:", err);
    }
  };

  return { user, loading, isLoggedIn, logout, setUser };
}
