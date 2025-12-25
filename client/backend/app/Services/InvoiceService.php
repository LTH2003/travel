<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate invoice PDF for an order
     */
    public function generateInvoicePdf(Order $order): string
    {
        try {
            // Load relationships fresh from database
            $order = Order::with(['user', 'bookingDetails'])->find($order->id);
            
            // Prepare booking details for invoice
            $bookingDetails = [];
            if ($order->bookingDetails && count($order->bookingDetails) > 0) {
                $bookingDetails = $order->bookingDetails->map(function ($detail) {
                    $info = $detail->booking_info ?? [];
                    
                    // Extract booking info with fallback values
                    return [
                        'room_number' => $info['name'] ?? $info['room_name'] ?? 'N/A',
                        'check_in' => $info['check_in'] ?? 'N/A',
                        'check_out' => $info['check_out'] ?? 'N/A',
                        'nights' => (int)($info['nights'] ?? 1),
                        'price' => (float)$detail->price,
                        'price_per_night' => (float)($info['price_per_night'] ?? $info['price'] ?? $detail->price),
                        'total_price' => (float)$detail->price * (int)($info['nights'] ?? 1),
                    ];
                })->toArray();
            }
            
            // Log for debugging
            \Log::info('Invoice generation - booking details', [
                'order_id' => $order->id,
                'booking_count' => count($bookingDetails),
                'booking_data' => $bookingDetails
            ]);
            
            // Prepare invoice data
            $invoiceData = [
                'order' => $order,
                'customer' => $order->user,
                'bookingDetails' => $bookingDetails,
                'invoiceNumber' => 'INV-' . $order->order_code,
                'invoiceDate' => now()->format('d/m/Y'),
                'totalAmount' => $order->total_amount,
                'status' => $order->status
            ];

            // Generate PDF from view with UTF-8 encoding support
            $pdf = Pdf::loadView('invoices.invoice-template', $invoiceData);
            
            // Configure DomPDF for Vietnamese characters
            $pdf->getDomPDF()->getOptions()->set([
                'enable_utf8' => true,
                'isPhpEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'defaultFont' => 'DejaVu Sans',
                'fontDir' => storage_path('fonts'),
            ]);
            
            // Create filename
            $filename = "invoice-{$order->order_code}-" . time() . ".pdf";
            
            // Store in storage
            $path = "invoices/{$filename}";
            Storage::put($path, $pdf->output());
            
            Log::info("Invoice PDF generated: {$path}", ['order_id' => $order->id]);
            
            return Storage::path($path);
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice PDF", [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send invoice email to customer
     */
    public function sendInvoiceEmail(Order $order, string $invoicePdfPath): bool
    {
        try {
            // Validate customer email
            if (!$order->user || !$order->user->email) {
                Log::warning("Cannot send invoice - customer email not found", [
                    'order_id' => $order->id
                ]);
                return false;
            }

            // Validate PDF file exists
            if (!file_exists($invoicePdfPath)) {
                Log::warning("Invoice PDF file not found", [
                    'order_id' => $order->id,
                    'path' => $invoicePdfPath
                ]);
                return false;
            }

            // Send email
            Mail::to($order->user->email)->send(new InvoiceMail($order, $invoicePdfPath));
            
            // Update email_sent_at timestamp
            if (property_exists($order, 'email_sent_at')) {
                $order->email_sent_at = now();
                $order->save();
            }
            
            Log::info("Invoice email sent successfully", [
                'order_id' => $order->id,
                'customer_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice email", [
                'order_id' => $order->id,
                'customer_email' => $order->user->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Generate and send invoice in one operation
     */
    public function generateAndSendInvoice(Order $order): array
    {
        $result = [
            'success' => false,
            'pdfGenerated' => false,
            'emailSent' => false,
            'message' => ''
        ];

        try {
            // Generate PDF
            $pdfPath = $this->generateInvoicePdf($order);
            $result['pdfGenerated'] = true;
            
            // Send email
            $emailSent = $this->sendInvoiceEmail($order, $pdfPath);
            $result['emailSent'] = $emailSent;
            
            // Overall success = at least PDF generated
            $result['success'] = true;
            
            if ($emailSent) {
                $result['message'] = 'Hóa đơn đã được tạo và gửi email thành công';
            } else {
                $result['message'] = 'Hóa đơn đã được tạo nhưng gửi email thất bại. Vui lòng kiểm tra config mail.';
            }
            
            return $result;
        } catch (\Exception $e) {
            $result['message'] = 'Lỗi tạo hóa đơn: ' . $e->getMessage();
            Log::error("Invoice generation failed", [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return $result;
        }
    }
}
