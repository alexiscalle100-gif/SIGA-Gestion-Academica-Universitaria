<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public function notify(
        int|User $recipient,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null
    ): AppNotification {
        $userId = $recipient instanceof User ? $recipient->id : $recipient;

        return AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }

    public function logActivity(
        ?int $userId,
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
        ]);
    }
}
