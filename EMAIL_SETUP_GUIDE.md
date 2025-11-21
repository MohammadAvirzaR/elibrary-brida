# Laravel Email Configuration Guide

## Current Configuration

Your Laravel application is now configured to use Gmail SMTP for sending OTP emails.

## Setup Steps

### 1. Enable Gmail App Password

1. Go to your Google Account: https://myaccount.google.com/
2. Navigate to **Security** section
3. Enable **2-Step Verification** (required by Gmail to generate app passwords)
4. Go to **App passwords**: https://myaccount.google.com/apppasswords
5. Select app: **Mail**
6. Select device: **Other (Custom name)** → Type: "Laravel Email"
7. Click **Generate**
8. Copy the 16-character password (format: `xxxx xxxx xxxx xxxx`)

### 2. Update .env File

Open `elibrary-brida-be/.env` and update:

```env
MAIL_USERNAME=your-email@gmail.com      # Replace with your Gmail address
MAIL_PASSWORD=your-16-char-app-password # Replace with generated App Password (no spaces)
MAIL_FROM_ADDRESS="your-email@gmail.com" # Same as MAIL_USERNAME
```

**Example:**
```env
MAIL_USERNAME=avirza@gmail.com
MAIL_PASSWORD=abcdxxxx12345678
MAIL_FROM_ADDRESS="avirza@gmail.com"
```

### 3. Clear Laravel Cache

Run these commands in `elibrary-brida-be` directory:

```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test Email Sending

**Option 1: Test via Registration**
1. Go to http://localhost:5173/register
2. Fill in the registration form
3. Submit → OTP should be sent to your email
4. Check your inbox for 6-digit OTP code

**Option 2: Test via Tinker**
```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from Laravel', function ($message) {
    $message->to('recipient@example.com')
            ->subject('Test Email');
});
```

## Alternative Email Providers

### Using Mailtrap (Development/Testing)

1. Sign up at https://mailtrap.io/
2. Get SMTP credentials from your inbox
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@elibrary.com"
MAIL_FROM_NAME="E-Library Brida"
```

### Using SendGrid

1. Sign up at https://sendgrid.com/
2. Create API Key
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@elibrary.com"
MAIL_FROM_NAME="E-Library Brida"
```

## Troubleshooting

### Error: "Failed to authenticate on SMTP server"
- Check if you're using App Password (not regular Gmail password)
- Verify 2-Step Verification is enabled
- Make sure no spaces in the App Password

### Error: "Connection could not be established"
- Check internet connection
- Verify MAIL_HOST and MAIL_PORT are correct
- Try PORT=465 with MAIL_ENCRYPTION=ssl

### Error: "Address in mailbox given does not comply with RFC 2822"
- Remove quotes from MAIL_FROM_ADDRESS: `MAIL_FROM_ADDRESS=email@gmail.com`

### Emails not arriving
- Check spam/junk folder
- Verify MAIL_FROM_ADDRESS matches MAIL_USERNAME
- Test with Mailtrap first to confirm Laravel is sending

## OTP Email Template

Currently, the OTP email is sent as plain text. To customize it, you can update `AuthController.php`:

```php
// Current implementation
Mail::raw("Your OTP code is: {$otp}", function ($message) use ($data) {
    $message->to($data['email'])->subject('Your OTP Code');
});

// Enhanced version
Mail::send('emails.otp', ['otp' => $otp, 'name' => $data['name']], function ($message) use ($data) {
    $message->to($data['email'])
            ->subject('E-Library Registration - OTP Verification');
});
```

Then create `resources/views/emails/otp.blade.php`:

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .otp-code { font-size: 32px; font-weight: bold; color: #4F46E5; text-align: center; padding: 20px; background: #F3F4F6; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>E-Library Registration</h2>
        <p>Hello {{ $name }},</p>
        <p>Your OTP verification code is:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This code will expire in 10 minutes.</p>
        <p>If you didn't request this code, please ignore this email.</p>
    </div>
</body>
</html>
```

## Security Best Practices

1. **Never commit .env to Git** - Already in .gitignore
2. **Use App Passwords** - Never use your main Gmail password
3. **Limit OTP attempts** - Consider adding rate limiting
4. **Log email failures** - Monitor for delivery issues
5. **Use queue for emails** - Set `QUEUE_CONNECTION=database` for better performance

## Next Steps

1. ✅ Email configuration updated
2. ⏳ Get Gmail App Password
3. ⏳ Update .env with your credentials
4. ⏳ Clear Laravel cache
5. ⏳ Test registration with OTP
6. ⏳ Customize email template (optional)

## Quick Test Command

After configuration, restart Laravel server and test:

```bash
# Terminal 1: Start Laravel server
cd elibrary-brida-be
php artisan serve

# Terminal 2: Test email
php artisan tinker
Mail::raw('Test OTP: 123456', function($m) { $m->to('your-test-email@gmail.com')->subject('Test OTP'); });
```

If you see no errors and receive the email, your configuration is working! 🎉
