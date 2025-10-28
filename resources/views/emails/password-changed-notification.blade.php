<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Changed - Security Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .security-badge {
            background-color: #f39c12;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: inline-block;
        }
        .content {
            padding: 30px;
        }
        .security-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .details-box {
            background-color: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="security-badge">🔒 Security Alert</div>
            <h1>Password Changed Successfully</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $userName }},</h2>
            
            <p>This is a security notification to inform you that your account password was successfully changed.</p>
            
            <div class="details-box">
                <h3>Change Details:</h3>
                <ul>
                    <li><strong>Date & Time:</strong> {{ $changeTime }}</li>
                    <li><strong>IP Address:</strong> {{ $ipAddress }}</li>
                    <li><strong>Browser:</strong> {{ $userAgent }}</li>
                </ul>
            </div>
            
            <div class="security-info">
                <strong>⚠️ Important Security Information:</strong>
                <ul>
                    <li>If you made this change, no further action is required</li>
                    <li>If you did NOT change your password, your account may be compromised</li>
                    <li>Contact support immediately if this change was unauthorized</li>
                </ul>
            </div>
            
            <p>If this password change was unauthorized, please:</p>
            <ol>
                <li>Change your password immediately</li>
                <li>Review your account activity</li>
                <li>Contact our support team</li>
            </ol>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="btn">Login to Your Account</a>
            </div>
            
            <p><strong>Security Tips:</strong></p>
            <ul>
                <li>Use a strong, unique password</li>
                <li>Enable two-factor authentication if available</li>
                <li>Never share your password with anyone</li>
                <li>Log out from shared or public computers</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>This is an automated security notification from the Alumni Management System.</p>
            <p>Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Alumni Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>