<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
        }
        .otp-code {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 8px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .instruction {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛍️ {{ config('app.name') }}</div>
            <h1>Reset Password</h1>
        </div>
        
        <div class="content">
            <h2>Halo, {{ $customerName }}!</h2>
            <p>Kami menerima permintaan untuk reset password akun Anda. Gunakan kode OTP berikut untuk melanjutkan proses reset password:</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <div class="instruction">
                <strong>Cara menggunakan kode OTP:</strong>
                <ol>
                    <li>Masukkan kode OTP di atas pada halaman reset password</li>
                    <li>Buat password baru yang aman</li>
                    <li>Konfirmasi password baru Anda</li>
                </ol>
            </div>
            
            <div class="warning">
                <strong>⚠️ Penting untuk diketahui:</strong>
                <ul>
                    <li>Kode OTP ini berlaku selama <strong>15 menit</strong></li>
                    <li>Jangan bagikan kode ini kepada siapapun</li>
                    <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                </ul>
            </div>
            
            <p>Jika Anda mengalami kesulitan, jangan ragu untuk menghubungi tim support kami.</p>
            
            <p>Terima kasih,<br>
            <strong>Tim {{ config('app.name') }}</strong></p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>