<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - IMYV</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="100%" max-width="500" cellpadding="0" cellspacing="0" style="max-width: 500px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    
                    <!-- Header / Logo Area -->
                    <tr>
                        <td style="background-color: #166534; padding: 30px 20px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">IMYV</h1>
                            <p style="margin: 5px 0 0; color: #bbf7d0; font-size: 14px;">Jackfruit Farm</p>
                        </td>
                    </tr>
                    
                    <!-- Content Area -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 15px; color: #166534; font-size: 24px;">Reset Your Password</h2>
                            
                            <p style="margin: 0 0 20px; color: #4b5563; line-height: 1.6; font-size: 16px;">
                                You requested to reset your password. Click the button below to create a new password.
                            </p>
                            
                            <!-- Reset Button Only -->
                            <table cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="background-color: #16a34a; border-radius: 8px;">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 14px 36px; color: #ffffff; background-color: #16a34a; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 20px 0 0; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                ⚠️ This link expires in <strong>60 minutes</strong>.
                            </p>
                            
                            <p style="margin: 10px 0 0; color: #9ca3af; font-size: 12px;">
                                If you didn't request this, please ignore this email.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">
                                IMYV Jackfruit Farm — Brgy. San Isidro Sitio Kiga, Baybay, Philippines, 6521
                            </p>
                            <p style="margin: 10px 0 0; color: #9ca3af; font-size: 11px;">
                                &copy; {{ date('Y') }} IMYV Jackfruit Farm. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>