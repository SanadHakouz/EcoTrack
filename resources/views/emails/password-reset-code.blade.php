<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code - EcoTrack</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #374151;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .code-container {
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 36px;
            font-weight: 700;
            color: #059669;
            letter-spacing: 8px;
            margin: 0;
        }
        .code-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .warning {
            background: #fef3cd;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer {
            background: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .footer a {
            color: #059669;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .security-note {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-card">
            <!-- Header -->
            <div class="header">
                <h1>🌱 EcoTrack</h1>
                <p>Password Reset Request</p>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="greeting">
                    Hello {{ $userName }},
                </div>

                <div class="message">
                    We received a request to reset the password for your EcoTrack account
                    associated with <strong>{{ $email }}</strong>.
                </div>

                <div class="message">
                    Please use the following 4-digit verification code to complete your password reset:
                </div>

                <!-- Code Display -->
                <div class="code-container">
                    <div class="code">{{ $code }}</div>
                    <div class="code-label">Verification Code</div>
                </div>

                <!-- Warning -->
                <div class="warning">
                    <p>
                        <strong>⚠️ Important:</strong> This code will expire in 15 minutes.
                        If you didn't request this password reset, please ignore this email or contact our support team.
                    </p>
                </div>

                <div class="message">
                    Enter this code in the password reset form to proceed with setting a new password.
                </div>

                <!-- Security Note -->
                <div class="security-note">
                    <p>
                        <strong>Security tip:</strong> Never share this code with anyone. EcoTrack will never ask for your password or verification codes via email or phone.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    If you have any questions, please contact us at
                    <a href="mailto:support@ecotrack.com">support@ecotrack.com</a>
                </p>
                <p style="margin-top: 15px;">
                    Best regards,<br>
                    The EcoTrack Team
                </p>
            </div>
        </div>
    </div>
</body>
</html>
