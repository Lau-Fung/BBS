<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        logger()->info('attachments.store', $request->only('attachable_type','attachable_id'));
        // return response('hit', 200); // comment this out after testing

        $request->validate([
            'attachable_type' => ['required','string'],   // 'assignment' | 'device' | ...
            'attachable_id'   => ['required','integer','min:1'],
            'category'        => ['nullable','string','max:64'],
            'files'           => ['required','array','min:1'],
            'files.*'         => ['file','max:20480', 'mimetypes:image/*,application/pdf,application/zip,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword'], // 20MB
        ],[
            'files.*.max' => 'Each file must be 20 MB or less.',
        ]);

        $class = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($request->attachable_type) ?? ltrim($request->attachable_type, '\\');

        abort_unless(class_exists($class), 404, 'Invalid attachable type');

        $model = $class::findOrFail($request->attachable_id);

        // Optional authorization
        Gate::authorize('update', $model);

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            $uploaded[] = $model->addAttachment($file, $request->category, $request->user());
        }

        if ($request->wantsJson()) return response()->json($uploaded, 201);

        return back()->with('status', __('Uploaded :n file(s).', ['n' => count($uploaded)]));
    }

    public function download(Attachment $attachment)
    {
        Gate::authorize('view', $attachment);
        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    public function destroy(Attachment $attachment)
    {
        Gate::authorize('delete', $attachment->attachable);

        // soft delete db row + remove file
        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        if (request()->wantsJson()) return response()->noContent();

        return back()->with('status', __('Attachment deleted.'));
    }
}
