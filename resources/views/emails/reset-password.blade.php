<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }

        .button {
            display: inline-block;
            background-color: #4F46E5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }

        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Choose Your Cart</h1>
        <h2>Password Reset Request</h2>
    </div>

    <div class="content">
        <p>Hello {{ $userName }},</p>

        <p>You have requested to reset your password for your Choose Your Cart account. Click the button below to reset
            your password:</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>

        <p>If the button above doesn't work, you can also copy and paste this link into your browser:</p>
        <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

        <div class="warning">
            <strong>Important:</strong> This password reset link will expire in 1 hour for security reasons. If you
            didn't request this password reset, please ignore this email.
        </div>

        <p>If you have any questions or need help, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
            The Choose Your Cart Team</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $userName }} at your request. If you didn't request a password reset, please
            ignore this email.</p>
        <p>&copy; {{ date('Y') }} Choose Your Cart. All rights reserved.</p>
    </div>
</body>

</html>
