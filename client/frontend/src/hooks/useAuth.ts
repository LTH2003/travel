import { useEffect, useState } from "react";
import { getCurrentUser, logout as apiLogout } from "@/api/auth";
import { toast } from "@/hooks/use-toast";
import { useCart } from "./useCart";

export function useAuth() {
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const { loadFromServer, clearCart } = useCart();

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
          } catch (e) {
            // Invalid cache
          }
        }
        
        if (!token) {
          setLoading(false);
          setIsLoggedIn(false);
          clearCart();
          return;
        }

        // Fetch fresh data from server
        try {
          const [userRes] = await Promise.all([
            getCurrentUser(),
            loadFromServer().catch(() => null), // Don't fail if cart loading fails
          ]);
          
          setUser(userRes);
          setIsLoggedIn(true);
          // Update cache
          localStorage.setItem("user", JSON.stringify(userRes));
        } catch (err) {
          console.error("❌ Không thể lấy thông tin user:", err);
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          setUser(null);
          setIsLoggedIn(false);
          clearCart();
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
      setUser(null);
      setIsLoggedIn(false);
      toast({ title: "Đăng xuất thành công" });
    } catch (err) {
      console.error("Lỗi khi đăng xuất:", err);
    }
  };

  return { user, loading, isLoggedIn, logout, setUser };
}
