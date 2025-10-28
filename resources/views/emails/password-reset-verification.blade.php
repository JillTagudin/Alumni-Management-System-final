<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #28a745;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #1e7e34;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .btn:hover {
            background-color: #218838;
            border-color: #1e7e34;
            text-decoration: none;
        }
        .security-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Password Reset Verification</h1>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        
        <p>We received a request to reset the password for your account associated with <strong>{{ $email }}</strong>.</p>
        
        <div class="security-info">
            <strong>🔒 Security Notice:</strong> For your protection, please verify this request by clicking the button below. This ensures that only you can reset your password.
        </div>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="btn">Verify Password Reset Request</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">{{ $verificationUrl }}</p>
        
        <div class="details">
            <h3>Request Details:</h3>
            <ul>
                <li><strong>Time:</strong> {{ $requestTime }}</li>
                <li><strong>IP Address:</strong> {{ $ipAddress }}</li>
                <li><strong>Browser:</strong> {{ $userAgent }}</li>
            </ul>
        </div>
        
        <div class="security-info">
            <strong>⚠️ Important:</strong>
            <ul>
                <li>This verification link will expire in <strong>15 minutes</strong></li>
                <li>If you didn't request this password reset, please ignore this email</li>
                <li>Never share this link with anyone</li>
                <li>If you notice suspicious activity, contact support immediately</li>
            </ul>
        </div>
        
        <p>After clicking the verification link, you'll be able to set a new password for your account.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} Alumni Management System. All rights reserved.</p>
    </div>
</body>
</html>