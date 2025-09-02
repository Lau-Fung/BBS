<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AttachmentPolicy
{
    public function view(User $user, Attachment $attachment): bool
    {
        // If the user can view the attachable model, allow
        if (Gate::forUser($user)->allows('view', $attachment->attachable)) {
            return true;
        }

        // Uploader can always see their own file
        if ($attachment->uploaded_by && $attachment->uploaded_by === $user->id) {
            return true;
        }

        // Optional: global permission
        return $user->can('attachments.view');
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        if (Gate::forUser($user)->allows('update', $attachment->attachable)) {
            return true;
        }
        if ($attachment->uploaded_by && $attachment->uploaded_by === $user->id) {
            return true;
        }
        return $user->can('attachments.delete');
    }
}
