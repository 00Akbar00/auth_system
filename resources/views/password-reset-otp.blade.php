<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .otp { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; padding: 10px; background-color: #f8f9fa; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->email }},</p>
            
            <p>We received a request to reset your password. Here is your one-time password (OTP):</p>
            
            <div class="otp">{{ $otp }}</div>
            
            <p>This OTP is valid for {{ $expiryMinutes }} minutes. If you didn't request this password reset, please ignore this email or contact support.</p>
            
            <p><strong>Security Notice:</strong> This request was made from IP address {{ $ipAddress }}. If you don't recognize this activity, please secure your account immediately.</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>