<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Successful</h2>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->email }},</p>
            
            <p>Your password has been successfully reset.</p>
            
            <p>For security reasons, all active sessions for your account have been terminated. If you didn't make this change, please contact our support team immediately.</p>
            
            <p>Thank you,<br>The {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>