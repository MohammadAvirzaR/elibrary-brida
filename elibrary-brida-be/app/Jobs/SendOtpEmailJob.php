<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public int $otp,
        public int $expiryMinutes
    ) {}

    public function handle(): void
    {
        try {
            Mail::raw(
                "Kode OTP Anda adalah: {$this->otp}\n\nKode ini berlaku selama {$this->expiryMinutes} menit.",
                function ($message) {
                    $message->to($this->email)
                            ->subject('Kode Verifikasi OTP - Registrasi')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            Log::info("OTP email sent successfully to {$this->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$this->email}: " . $e->getMessage());
            throw $e;
        }
    }
}
