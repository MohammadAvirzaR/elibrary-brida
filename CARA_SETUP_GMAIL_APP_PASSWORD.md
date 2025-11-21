# 📧 Cara Mendapatkan Gmail App Password untuk OTP

## ⚠️ MASALAH SAAT INI

Email OTP **TIDAK TERKIRIM** karena:
- ❌ `MAIL_PASSWORD` menggunakan password Gmail biasa
- ✅ Harus menggunakan **App Password** khusus dari Google

---

## 🔧 SOLUSI: Dapatkan App Password

### Langkah 1: Aktifkan 2-Step Verification

1. Buka browser, pergi ke: **https://myaccount.google.com/security**
2. Scroll ke bawah cari **"2-Step Verification"** atau **"Verifikasi 2 Langkah"**
3. Klik **"Turn On"** atau **"Aktifkan"**
4. Ikuti petunjuk (verifikasi dengan nomor HP)
5. ✅ Selesai

---

### Langkah 2: Generate App Password

1. Setelah 2-Step Verification aktif, buka: **https://myaccount.google.com/apppasswords**
   
   > Atau dari Security → Scroll ke "App passwords"

2. Google akan minta password Gmail Anda → **Masukkan password biasa** (`Onlyavirzaknows21`)

3. Halaman App Passwords akan terbuka:
   - **Select app**: Pilih **"Mail"**
   - **Select device**: Pilih **"Other (Custom name)"**
   - Ketik nama: **"Laravel E-Library"**

4. Klik **"Generate"** atau **"Create"**

5. Google akan menampilkan **16 karakter kode**, contoh:
   ```
   abcd efgh ijkl mnop
   ```

6. **COPY KODE INI** (tanpa spasi ketika paste)

---

### Langkah 3: Update File .env

1. Buka file: `d:\PAD_PROJECT\elibrary-brida\elibrary-brida-be\.env`

2. Cari baris:
   ```env
   MAIL_PASSWORD=Onlyavirzaknows21
   ```

3. Ganti dengan App Password (16 karakter, **TANPA SPASI**):
   ```env
   MAIL_PASSWORD=abcdefghijklmnop
   ```

4. **SAVE** file

---

### Langkah 4: Restart Laravel Server

Setelah update `.env`:

```powershell
# 1. Buka terminal di folder backend
cd d:\PAD_PROJECT\elibrary-brida\elibrary-brida-be

# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Stop server yang lama (tekan Ctrl+C di terminal server)

# 4. Start server baru
php artisan serve
```

---

### Langkah 5: Test Registrasi

1. Buka: **http://localhost:5173/register**
2. Isi form registrasi dengan email Anda: **avirzaradyatanza@gmail.com**
3. Klik **"Daftar"**
4. **CEK INBOX EMAIL ANDA** → Seharusnya ada email dengan 6-digit OTP
5. Masukkan kode OTP → Selesai!

---

## 🧪 Test Email Manual (Optional)

Untuk test apakah email sudah bisa terkirim:

```powershell
cd d:\PAD_PROJECT\elibrary-brida\elibrary-brida-be
php artisan tinker
```

Lalu ketik:

```php
Mail::raw('Test OTP: 123456', function($m) {
    $m->to('avirzaradyatanza@gmail.com')
      ->subject('Test Email OTP');
});
```

Tekan Enter. Cek inbox email Anda!

---

## ❓ Troubleshooting

### Tidak bisa buka App Passwords?
- ✅ Pastikan 2-Step Verification sudah aktif
- ✅ Tunggu beberapa menit setelah aktifkan 2-Step
- ✅ Refresh halaman atau logout-login ulang

### Error "Username and Password not accepted"?
- ❌ Anda masih pakai password Gmail biasa
- ✅ Harus ganti dengan App Password 16 karakter

### Email masih tidak masuk?
- ✅ Cek folder **Spam/Junk**
- ✅ Pastikan App Password di-copy **tanpa spasi**
- ✅ Restart Laravel server setelah update `.env`

### Tidak mau aktifkan 2-Step Verification?
- Pakai **Mailtrap** untuk testing (tidak perlu Gmail):
  
  1. Daftar gratis: https://mailtrap.io/
  2. Dapat SMTP credentials
  3. Update `.env`:
     ```env
     MAIL_MAILER=smtp
     MAIL_HOST=sandbox.smtp.mailtrap.io
     MAIL_PORT=2525
     MAIL_USERNAME=your-mailtrap-username
     MAIL_PASSWORD=your-mailtrap-password
     MAIL_ENCRYPTION=tls
     ```
  4. Semua email OTP akan masuk ke inbox Mailtrap (untuk testing)

---

## 📋 Checklist

- [ ] 2-Step Verification aktif di Gmail
- [ ] App Password sudah di-generate (16 karakter)
- [ ] File `.env` sudah diupdate dengan App Password
- [ ] Laravel cache sudah di-clear
- [ ] Laravel server sudah di-restart
- [ ] Test registrasi dan cek inbox email

---

## 📝 Catatan Penting

1. **App Password ≠ Password Gmail biasa**
2. App Password hanya ditampilkan **SATU KALI** saat generate
3. Jika lupa, **generate ulang** App Password baru
4. App Password bisa di-revoke kapan saja di halaman Security
5. Untuk production, pertimbangkan pakai **SendGrid** atau **AWS SES**

---

## ✅ Setelah Setup Berhasil

Ketika sudah berhasil:
- ✅ User registrasi → Email OTP terkirim otomatis
- ✅ OTP valid 10 menit
- ✅ Bisa resend OTP jika belum masuk
- ✅ Email verified setelah input OTP benar

**Good luck! 🚀**
