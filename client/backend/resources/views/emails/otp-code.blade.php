<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .otp-box {
            background: #fff;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
            border: 2px solid #2563eb;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 10px;
            margin: 20px 0;
            font-family: monospace;
        }
        .expiry {
            color: #e74c3c;
            font-weight: bold;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛫 TravelVN</div>
        </div>

        <h2>Xác thực 2FA của bạn</h2>
        
        <p>Xin chào {{ $user->name }},</p>

        <p>Bạn vừa yêu cầu kích hoạt hoặc xác thực 2FA cho tài khoản của mình. Dưới đây là mã xác thực:</p>

        <div class="otp-box">
            <div style="font-size: 14px; color: #666;">Mã xác thực:</div>
            <div class="otp-code">{{ $code }}</div>
        </div>

        <div class="expiry">
            ⏱️ Mã này sẽ hết hạn sau 10 phút
        </div>

        <div class="warning">
            <strong>⚠️ Lưu ý bảo mật:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Không bao giờ chia sẻ mã này cho ai</li>
                <li>TravelVN sẽ không bao giờ yêu cầu mã này qua email hay SMS</li>
                <li>Nếu bạn không yêu cầu điều này, hãy bỏ qua email này</li>
            </ul>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

        <div class="footer">
            <p>Đây là email tự động. Vui lòng không trả lời.</p>
            <p>&copy; 2025 TravelVN. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
