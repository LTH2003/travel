<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #f0f4f8;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details {
            margin: 20px 0;
            border-collapse: collapse;
            width: 100%;
        }
        .details tr {
            border-bottom: 1px solid #ddd;
        }
        .details td {
            padding: 10px;
        }
        .details td:first-child {
            font-weight: bold;
            color: #667eea;
            width: 40%;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            margin-top: 20px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>

        <div class="content">
            <p>Kính gửi <strong>{{ $customer_name }}</strong>,</p>

            <p>Chúng tôi vinh dự thông báo rằng đơn hàng của bạn đã được <strong style="color: #dc3545;">hủy thành công</strong>.</p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #333;">Thông tin hủy đơn</h3>
                <table class="details">
                    <tr>
                        <td>Khách hàng:</td>
                        <td>{{ $customer_name }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $customer_email }}</td>
                    </tr>
                    <tr>
                        <td>Lý do hủy:</td>
                        <td>{{ $cancellation_reason }}</td>
                    </tr>
                    <tr>
                        <td>Ngày hủy:</td>
                        <td>{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #28a745;">Hoàn Tiền</h3>
            <div class="amount">
                {{ number_format($refund_amount, 0, ',', '.') }} VND
            </div>

            <p><strong>Thông tin hoàn tiền:</strong></p>
            <ul>
                <li>Số tiền hoàn lại: <strong style="color: #28a745;">{{ number_format($refund_amount, 0, ',', '.') }} VND</strong></li>
                <li>Thời gian hoàn tiền: Dự kiến đến {{ $refund_date }} hoặc sớm hơn</li>
                <li>Phương thức: Hoàn lại tài khoản cá nhân của bạn</li>
            </ul>

            <div class="warning">
                <strong>⏱️ Lưu ý quan trọng:</strong><br>
                Tiền hoàn lại thường mất từ 1-3 ngày làm việc tùy theo ngân hàng của bạn xử lý. Nếu bạn không nhận được tiền sau {{ $refund_date }}, vui lòng liên hệ với chúng tôi.
            </div>

            <h3 style="color: #667eea;">Tiếp Theo</h3>
            <p>Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ thêm, vui lòng liên hệ với đội hỗ trợ khách hàng của chúng tôi:</p>
            <ul>
                <li>📧 Email: <a href="mailto:huyhoahien86@gmail.com">huyhoahien86@gmail.com</a></li>
                <li>📞 Điện thoại: +84 (0) 889 421 997</li>
            </ul>

            <p>Cảm ơn bạn đã tin tưởng TravelVN. Chúng tôi hy vọng sẽ được phục vụ bạn trong tương lai!</p>

            <p style="color: #666; font-size: 12px;">
                <strong>Lưu ý:</strong> Đây là email tự động, vui lòng không trả lời trực tiếp vào email này. 
                Nếu có câu hỏi, vui lòng sử dụng các kênh liên hệ được liệt kê ở trên.
            </p>
        </div>

        <div class="footer">
            <p>&copy; 2025 TravelVN - Travel App. Tất cả quyền được bảo lưu.</p>
            <p>Địa chỉ: Hà Nội, Việt Nam</p>
        </div>
    </div>
</body>
</html>
