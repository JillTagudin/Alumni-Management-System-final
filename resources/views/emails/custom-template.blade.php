<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
    <style>
        /* Enhanced responsive email styles */
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .content {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $subject }}</h1>
            @if(isset($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
        </div>
        
        <div class="content">
            @if(isset($greeting))
                <h2>{{ $greeting }}</h2>
            @endif
            
            <div class="message-content">
                {!! nl2br(e($message)) !!}
            </div>
            
            @if(isset($actionUrl) && isset($actionText))
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $actionUrl }}" style="display: inline-block; background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">{{ $actionText }}</a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This email was sent from the Alumni Management System.</p>
            <p>&copy; {{ date('Y') }} Alumni Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>