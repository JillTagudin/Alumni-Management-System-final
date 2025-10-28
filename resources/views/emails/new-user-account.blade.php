<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Alumni Management System</title>
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
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .credentials-box {
            background-color: #fff;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f9ff;
            border-radius: 4px;
        }
        .password {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            background-color: #fef2f2;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #fecaca;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Alumni Management System</h1>
        <p>Your account has been successfully created</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $userName }},</h2>
        
        <p>Your alumni account has been successfully created in our Alumni Management System. Below are your login credentials:</p>
        
        <div class="credentials-box">
            <h3 style="color: #10b981; margin-top: 0;">🔐 Your Login Credentials</h3>
            
            <div class="credential-item">
                <strong>Email:</strong> {{ $userEmail }}
            </div>
            
            <div class="credential-item">
                <strong>Temporary Password:</strong>
                <div class="password">{{ $generatedPassword }}</div>
            </div>
        </div>
        
        <div class="warning">
            <h4 style="margin-top: 0;">⚠️ Important Security Notice</h4>
            <ul>
                <li><strong>Change your password immediately</strong> after your first login</li>
                <li>This password is temporary and should not be shared with anyone</li>
                <li>Use a strong, unique password that you haven't used elsewhere</li>
                <li>Keep your login credentials secure and confidential</li>
            </ul>
        </div>
        
        <h3>Next Steps:</h3>
        <ol>
            <li>Click the login button below to access your account</li>
            <li>Use the credentials provided above to log in</li>
            <li>Update your password in your profile settings</li>
            <li>Complete your profile information</li>
            <li>Explore the alumni features and connect with fellow alumni</li>
        </ol>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $loginUrl }}" class="btn">Login to Your Account</a>
        </div>
        
        <h3>Account Details:</h3>
        <ul>
            <li><strong>Alumni ID:</strong> {{ $alumniId }}</li>
            <li><strong>Student Number:</strong> {{ $studentNumber }}</li>
            <li><strong>Account Created:</strong> {{ $createdAt }}</li>
            <li><strong>Account Status:</strong> Active & Verified</li>
        </ul>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the Alumni Management System.</p>
        <p>Please do not reply to this email. For support, contact our admin team.</p>
        <p>&copy; {{ date('Y') }} Alumni Management System. All rights reserved.</p>
    </div>
</body>
</html>