<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Denied</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registration Denied</h1>
        </div>
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            <p>We regret to inform you that your registration for the Alumni Management System has been denied.</p>
            
            <p><strong>Registration Details:</strong></p>
            <ul>
                <li>Email: {{ $userEmail }}</li>
                <li>Reviewed on: {{ $deniedAt }}</li>
            </ul>
            
            @if($denialReason)
            <p><strong>Reason:</strong></p>
            <p>{{ $denialReason }}</p>
            @endif
            
            <p>If you believe this was an error or have questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>