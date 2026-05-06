<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; }
    .container { max-width: 640px; margin: 0 auto; padding: 24px; }
    .card { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
    .header { background: #16a34a; color: white; padding: 18px 22px; }
    .content { padding: 22px; background: #ffffff; }
    .btn { display: inline-block; text-decoration: none; color: white; background: #2563eb; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 10px; }
    .note { margin-top: 14px; font-size: 12px; color: #6b7280; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <h2 style="margin:0;">Permintaan Download Disetujui</h2>
      </div>
      <div class="content">
        <p>Permintaan Anda untuk dokumen berikut telah disetujui:</p>
        <p><strong>{{ $downloadRequest->document?->title }}</strong></p>

        <a class="btn" href="{{ $downloadUrl }}">Download Dokumen</a>

        <p class="note">
          Link ini berlaku 24 jam sejak email dikirim.
        </p>
      </div>
    </div>
  </div>
</body>
</html>
