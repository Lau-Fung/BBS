<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Assignment;
use App\Policies\AssignmentPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Assignment::class => AssignmentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies(); // keep this
        // Gate::policy(Assignment::class, AssignmentPolicy::class); // optional explicit
    }
}
