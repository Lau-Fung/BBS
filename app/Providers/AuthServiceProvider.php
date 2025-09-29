<?php 

namespace App\Providers;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Assignment;
use App\Policies\AssignmentPolicy;
use App\Models\Attachment;
use App\Policies\AttachmentPolicy;
use Spatie\Activitylog\Models\Activity;
use App\Policies\ActivityLogPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Assignment::class => AssignmentPolicy::class,
        Attachment::class => AttachmentPolicy::class,
        Activity::class => ActivityLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies(); // keep this
        // Gate::policy(Assignment::class, AssignmentPolicy::class); // optional explicit
    }
}
