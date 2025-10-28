<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
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
        .announcement-badge {
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
        .social-sharing {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .social-sharing h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
        }
        .social-buttons {
            display: block;
            text-align: center;
        }
        .social-btn {
            display: inline-block;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            color: white !important;
            font-weight: bold;
            font-size: 14px;
            margin: 5px 10px;
            text-align: center;
            min-width: 120px;
        }
        .social-btn:hover {
            opacity: 0.8;
            color: white !important;
        }
        .facebook { 
            background-color: #1877f2 !important; 
            border: none;
        }
        .twitter { 
            background-color: #1da1f2 !important; 
            border: none;
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
            <div class="announcement-badge">📢 Announcement</div>
            <h1>{{ $subject }}</h1>
        </div>
        
        <div class="content">
            @if(isset($greeting))
                <h2>{{ $greeting }}</h2>
            @endif
            
            <div class="message-content">
                {!! nl2br(e($messageContent ?? $message ?? '')) !!}
            </div>
            
            @if(isset($actionUrl) && isset($actionText))
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $actionUrl }}" style="display: inline-block; background-color: #e74c3c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">{{ $actionText }}</a>
                </div>
            @endif
        </div>
        
        <div class="social-sharing">
            <h3>📤 Share this announcement</h3>
            <div class="social-buttons">
                @php
                    $shareUrl = url('/');
                    $shareText = urlencode($subject ?? 'Check out this announcement');
                @endphp
                
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" 
                   target="_blank" class="social-btn facebook">📘 Facebook</a>
                
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareText }}" 
                   target="_blank" class="social-btn twitter">🐦 X (Twitter)</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This announcement was sent from the Alumni Management System.</p>
            <p>&copy; {{ date('Y') }} Alumni Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>