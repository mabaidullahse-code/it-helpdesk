<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
        'group',
        'description',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->is_encrypted && !empty($setting->value)) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (Throwable) {
                return $default;
            }
        }

        return $setting->value ?? $default;
    }

    public static function setValue(string $key, $value, bool $isEncrypted = false, string $group = 'general', ?string $description = null): self
    {
        $storedValue = $value;
        if ($isEncrypted && !empty($value)) {
            $storedValue = Crypt::encryptString((string) $value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'is_encrypted' => $isEncrypted,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}
