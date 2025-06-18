<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Generate a new 4-digit code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new password reset code for an email
     */
    public static function createForEmail(string $email): self
    {
        // Delete any existing unused codes for this email
        self::where('email', $email)
            ->where('used', false)
            ->delete();

        return self::create([
            'email' => $email,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(15), // Code expires in 15 minutes
            'used' => false,
        ]);
    }

    /**
     * Verify a code for an email
     */
    public static function verifyCode(string $email, string $code): bool
    {
        $resetCode = self::where('email', $email)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($resetCode) {
            $resetCode->update([
                'used' => true,
                'used_at' => Carbon::now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if code is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Check if code is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
