<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; color: #374151; line-height: 1.6; }
    .container { max-width: 600px; margin: 0 auto; padding: 24px; }
    .header { background: #1d4ed8; color: white; padding: 20px 24px; border-radius: 8px 8px 0 0; }
    .body { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; }
    .footer { background: #e5e7eb; padding: 16px 24px; border-radius: 0 0 8px 8px; font-size: 12px; color: #6b7280; }
    .highlight { background: #eff6ff; border-left: 4px solid #2563eb; padding: 12px 16px; margin: 16px 0; border-radius: 0 6px 6px 0; }
    .notice { background: #fefce8; border: 1px solid #fde047; padding: 12px 16px; border-radius: 6px; font-size: 13px; margin: 16px 0; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2 style="margin:0;">E-Library BRIDA</h2>
      <p style="margin:4px 0 0; font-size:14px; opacity:0.9;">Dokumen yang Anda Minta</p>
    </div>
    <div class="body">
      <p>Yth. <strong>{{ $downloadRequest->name }}</strong>,</p>
      <p>Terima kasih atas kepercayaan Anda. Berikut adalah dokumen yang Anda ajukan permintaan unduh:</p>

      <div class="highlight">
        <strong>Judul Dokumen:</strong><br>
        {{ $downloadRequest->document?->title }}
      </div>

      <p>Dokumen terlampir pada email ini. Dengan mengunduh dan menggunakan dokumen ini, Anda telah menyetujui pernyataan bahwa Anda tidak akan menyalahgunakan dokumen ini dan akan menggunakannya sesuai dengan aturan hukum yang berlaku.</p>

      <div class="notice">
        ⚠️ <strong>Perhatian:</strong> Dokumen ini bersifat ilmiah dan diterima hanya untuk keperluan penelitian, pendidikan, dan referensi. Dilarang menyebarluaskan, memperbanyak, atau menggunakan secara komersial tanpa izin.
      </div>

      @if($downloadRequest->admin_notes)
      <p><strong>Catatan dari Admin:</strong><br>{{ $downloadRequest->admin_notes }}</p>
      @endif

      <p>Jika ada pertanyaan, silakan hubungi kami melalui website E-Library BRIDA.</p>

      <p>Hormat kami,<br><strong>Tim E-Library BRIDA</strong></p>
    </div>
    <div class="footer">
      Email ini dikirim secara otomatis. Mohon jangan membalas email ini.
    </div>
  </div>
</body>
</html>
