<!DOCTYPE html>
<html>
<head>
    <title>Join Our Alumni Network</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .benefits {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .benefit-item {
            margin: 10px 0;
            padding-left: 20px;
            position: relative;
        }
        .benefit-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Join Our Alumni Network!</h1>
        <p>Stay Connected with Your Alma Mater</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $studentName }},</h2>
        
        <p>We hope this message finds you well! As a graduate from <strong>{{ $program }}</strong>, you're part of our amazing alumni community, and we'd love to have you officially join our alumni network.</p>
        
        <div class="benefits">
            <h3>🌟 Benefits of Joining:</h3>
            <div class="benefit-item">Access to exclusive job opportunities and career resources</div>
            <div class="benefit-item">Connect with fellow alumni in your field</div>
            <div class="benefit-item">Receive invitations to networking events and reunions</div>
            <div class="benefit-item">Access to alumni directory and mentorship programs</div>
            <div class="benefit-item">Stay updated with school news and achievements</div>
            <div class="benefit-item">Opportunities to give back and support current students</div>
        </div>
        
        <p><strong>Your Details:</strong></p>
        <ul>
            <li><strong>Student Number:</strong> {{ $studentNumber }}</li>
            <li><strong>Program:</strong> {{ $program }}</li>
            <li><strong>Email:</strong> {{ $email }}</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $registrationLink }}" class="cta-button">🚀 Register Now - It's Free!</a>
        </div>
        
        <p>Registration is quick and easy! Your information is already pre-filled, so you just need to verify and complete your profile.</p>
        
        <p>If you have any questions or need assistance with registration, please don't hesitate to contact our alumni office.</p>
        
        <p>We look forward to welcoming you to our official alumni network!</p>
        
        <p>Best regards,<br>
        <strong>Alumni Relations Office</strong><br>
        Alumni Management System</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        <small style="color: #666;">
            This invitation was sent on {{ $sentAt }}<br>
            If you believe you received this email in error, please contact our alumni office.
        </small>
    </div>
</body>
</html>