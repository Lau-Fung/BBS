<?php

namespace App\Models\Concerns;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAttachments
{
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')->latest();
    }

    public function addAttachment(UploadedFile $file, ?string $category = null, $user = null): Attachment
    {
        $disk = config('filesystems.default');
        $dir  = 'attachments/' . Str::kebab(class_basename($this)) . '/' . $this->getKey();

        $safeBase = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 80, '');
        $name     = Str::uuid()->toString() . ($safeBase ? "_{$safeBase}" : '') . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($dir, $name, $disk);

        return $this->attachments()->create([
            'category'      => $category,
            'disk'          => $disk,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'sha256'        => hash_file('sha256', $file->getRealPath()),
            'uploaded_by'   => $user?->id,
        ]);
    }
}
