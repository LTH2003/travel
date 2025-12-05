import axiosClient from "./axiosClient";

export const getCheckoutInfo = async () => {
  const res = await axiosClient.get("/checkout-info");
  return res.data;
};

export const getPaymentMethods = async () => {
  const res = await axiosClient.get("/payment-methods");
  return res.data;
};

export const createOrder = async (data: {
  items: any[];
  total_amount: number;
  notes?: string;
}) => {
  const res = await axiosClient.post("/orders", {
    items: JSON.stringify(data.items),
    total_amount: data.total_amount,
    notes: data.notes,
  });
  return res.data;
};

export const initiateMoMoPayment = async (orderId: number) => {
  const res = await axiosClient.post("/payment/momo/initiate", { order_id: orderId });
  return res.data;
};

export const initiateVietQRPayment = async (orderId: number) => {
  const res = await axiosClient.post("/payment/vietqr/initiate", { order_id: orderId });
  return res.data;
};

export const initiateCardPayment = async (orderId: number) => {
  const res = await axiosClient.post("/payment/card/initiate", { order_id: orderId });
  return res.data;
};

export const verifyCardPayment = async (orderId: number, paymentIntentId: string) => {
  const res = await axiosClient.post("/payment/card/verify", {
    order_id: orderId,
    payment_intent_id: paymentIntentId,
  });
  return res.data;
};

export const initiateEWalletPayment = async (orderId: number, walletType: string) => {
  const res = await axiosClient.post("/payment/ewallet/initiate", {
    order_id: orderId,
    wallet_type: walletType,
  });
  return res.data;
};

export const verifyVietQRPayment = async (orderId: number, transactionRef: string) => {
  const res = await axiosClient.post("/payment/vietqr/verify", {
    order_id: orderId,
    transaction_ref: transactionRef,
  });
  return res.data;
};

export const initiateZaloPayPayment = async (orderId: number) => {
  const res = await axiosClient.get(`/payment/zalopay/quicklink/${orderId}`);
  return res.data;
};

export const getOrder = async (orderId: number) => {
  const res = await axiosClient.get(`/orders/${orderId}`);
  return res.data;
};

export const getUserOrders = async () => {
  const res = await axiosClient.get("/orders");
  return res.data;
};
