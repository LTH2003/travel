<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;

class CardPaymentService
{
    private $stripeKey;

    public function __construct()
    {
        $this->stripeKey = config('payments.stripe.secret_key', '');
        Stripe::setApiKey($this->stripeKey);
    }

    /**
     * Tạo Payment Intent cho Stripe
     */
    public function createPaymentIntent($orderId, $amount, $customerEmail, $description = '')
    {
        try {
            // Amount phải tính theo cents (VNĐ không có cents, nên nhân 100)
            $amountInCents = (int)($amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'vnd',
                'description' => $description ?: "Order #{$orderId}",
                'metadata' => [
                    'order_id' => $orderId,
                    'customer_email' => $customerEmail,
                ],
                'receipt_email' => $customerEmail,
            ]);

            return [
                'status' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => 'vnd',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Xác minh Payment Intent
     */
    public function verifyPaymentIntent($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'status' => true,
                'paymentStatus' => $paymentIntent->status, // succeeded, processing, requires_action, etc.
                'amount' => $paymentIntent->amount / 100, // Convert back from cents
                'chargeId' => $paymentIntent->charges->data[0]->id ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund giao dịch
     */
    public function refund($chargeId, $amount = null)
    {
        try {
            $refundData = [
                'charge' => $chargeId,
            ];

            if ($amount) {
                $refundData['amount'] = (int)($amount * 100);
            }

            $refund = \Stripe\Refund::create($refundData);

            return [
                'status' => true,
                'refundId' => $refund->id,
                'amount' => $refund->amount / 100,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
