import axiosClient from "./axiosClient";

interface LoginResponse {
  token: string;
  user: {
    id: number;
    name: string;
    email: string;
    role: string;
  };
}

export const getProfile = async () => {
  const res = await axiosClient.get("/profile");
  return res.data;
};

export const updateUserProfile = async (data) => {
  const token = localStorage.getItem("token");
  const res = await axiosClient.put("/profile", data, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });
  return res.data;
};

export const register = async (data: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}) => {
  const res = await axiosClient.post("/register", data);
  return res.data;
};
export const login = async (data: {
  email: string;
  password: string;
}) => {
  const res = await axiosClient.post<LoginResponse>("/login", data);

  // Nếu backend trả token
  if (res.data.token) {
    localStorage.setItem("token", res.data.token);
  }

  return res.data;
};
export const logout = async () => {
  const token = localStorage.getItem("token");
  await axiosClient.post(
    "/logout",
    {},
    { headers: { Authorization: `Bearer ${token}` } }
  );
  localStorage.removeItem("token");
};
export const getCurrentUser = async () => {
  const token = localStorage.getItem("token");
  const res = await axiosClient.get("/me", {
    headers: { Authorization: `Bearer ${token}` },
  });
  return (res.data as any).user; // Extract user object from response
};

// 🔐 2FA Methods
export const verifyOtp = async (data: {
  user_id: number | string;
  code: string;
}) => {
  return axiosClient.post("/auth/verify-otp", data);
};

export const resendOtp = async (data: { user_id: number | string }) => {
  return axiosClient.post("/auth/resend-otp", data);
};

export const enableTwoFactor = async () => {
  return axiosClient.post("/auth/enable-2fa", {});
};

export const confirmTwoFactor = async (data: { code: string }) => {
  return axiosClient.post("/auth/confirm-2fa", data);
};

export const disableTwoFactor = async (data: { password: string }) => {
  return axiosClient.post("/auth/disable-2fa", data);
};
