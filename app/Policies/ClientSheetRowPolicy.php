<?php

namespace App\Policies;

use App\Models\ClientSheetRow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientSheetRowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any client sheet rows.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('assignments.view');
    }

    /**
     * Determine whether the user can view the client sheet row.
     */
    public function view(User $user, ClientSheetRow $clientSheetRow)
    {
        return $user->hasPermissionTo('assignments.view');
    }

    /**
     * Determine whether the user can create client sheet rows.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('assignments.create');
    }

    /**
     * Determine whether the user can update the client sheet row.
     */
    public function update(User $user, ClientSheetRow $clientSheetRow)
    {
        return $user->hasPermissionTo('assignments.update');
    }

    /**
     * Determine whether the user can update multiple client sheet rows.
     */
    public function updateAll(User $user)
    {
        return $user->hasPermissionTo('assignments.update');
    }

    /**
     * Determine whether the user can delete the client sheet row.
     */
    public function delete(User $user, ClientSheetRow $clientSheetRow)
    {
        return $user->hasPermissionTo('assignments.delete');
    }

    /**
     * Determine whether the user can restore the client sheet row.
     */
    public function restore(User $user, ClientSheetRow $clientSheetRow)
    {
        return $user->hasPermissionTo('assignments.restore');
    }

    /**
     * Determine whether the user can permanently delete the client sheet row.
     */
    public function forceDelete(User $user, ClientSheetRow $clientSheetRow)
    {
        return $user->hasPermissionTo('assignments.forceDelete');
    }
}
