<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Approved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .button { 
            display: inline-block; 
            background: #28a745; 
            color: white !important; 
            padding: 15px 30px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 16px; 
            text-align: center; 
            margin: 15px 0; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            border: 2px solid #28a745;
            transition: all 0.3s ease;
        }
        .button:hover { 
            background: #218838; 
            border-color: #218838; 
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); 
            transform: translateY(-1px);
        }
        .button-container { 
            text-align: center; 
            margin: 25px 0; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Approved!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            <p>Congratulations! Your registration for the Alumni Management System has been approved.</p>
            
            <p><strong>Account Details:</strong></p>
            <ul>
                <li>Email: {{ $userEmail }}</li>
                <li>Approved on: {{ $approvedAt }}</li>
            </ul>
            
            @if($approvalNotes)
            <p><strong>Administrator Notes:</strong></p>
            <p>{{ $approvalNotes }}</p>
            @endif
            
            <p>You can now log in to your account:</p>
            <div class="button-container">
                <a href="{{ $loginUrl }}" class="button">Login Now</a>
            </div>
            
            <p>Welcome to our alumni community!</p>
        </div>
    </div>
</body>
</html>