<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Record an audit log entry.
     */
    public function log(
        string $action,
        ?Model $auditable = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): AuditLog {
        // Sanitize sensitive fields from old and new values
        $sanitizedOld = $this->sanitizeValues($oldValues);
        $sanitizedNew = $this->sanitizeValues($newValues);

        return AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'description' => $description,
            'old_values' => $sanitizedOld,
            'new_values' => $sanitizedNew,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    protected function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitiveKeys = ['password', 'smtp_password', 'secret', 'token', 'app_password', 'key'];
        foreach ($values as $key => $value) {
            foreach ($sensitiveKeys as $sensitive) {
                if (stripos((string) $key, $sensitive) !== false) {
                    $values[$key] = '********';
                }
            }
        }

        return $values;
    }
}
