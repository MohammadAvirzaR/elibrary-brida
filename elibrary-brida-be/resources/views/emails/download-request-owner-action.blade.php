<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; }
    .container { max-width: 640px; margin: 0 auto; padding: 24px; }
    .card { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
    .header { background: #1d4ed8; color: white; padding: 18px 22px; }
    .content { padding: 22px; background: #ffffff; }
    .meta { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; margin: 14px 0; }
    .actions { margin-top: 18px; display: flex; gap: 10px; }
    .btn { display: inline-block; text-decoration: none; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; }
    .btn-approve { background: #16a34a; }
    .btn-reject { background: #dc2626; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <h2 style="margin:0;">Permintaan Download Dokumen</h2>
      </div>
      <div class="content">
        <p>Ada permintaan download untuk dokumen Anda:</p>
        <div class="meta">
          <div><strong>Dokumen:</strong> {{ $downloadRequest->document?->title }}</div>
          <div><strong>Requester:</strong> {{ $downloadRequest->name }}</div>
          <div><strong>Email:</strong> {{ $downloadRequest->email }}</div>
          @if($downloadRequest->purpose)
            <div><strong>Tujuan:</strong> {{ $downloadRequest->purpose }}</div>
          @endif
        </div>

        <p>Pilih tindakan di bawah ini:</p>
        <div class="actions">
          <a class="btn btn-approve" href="{{ $approveUrl }}">Setujui</a>
          <a class="btn btn-reject" href="{{ $rejectUrl }}">Tolak</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
