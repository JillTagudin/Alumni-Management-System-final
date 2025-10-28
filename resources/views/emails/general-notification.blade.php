<!DOCTYPE html>
<html>
<head>
    <title>Notification</title>
</head>
<body>
    <h2>Hello {{ $userName }},</h2>
    
    <div style="margin: 20px 0;">
        {!! nl2br(e($messageContent)) !!}
    </div>
    
    <p>Best regards,<br>
    Alumni Management System</p>
    
    <hr>
    <small>This email was sent on {{ $sentAt }}</small>
</body>
</html>