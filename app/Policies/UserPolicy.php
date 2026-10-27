<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    /**
     * Developers pass through Shield's Gate::before, so a developer account here is always someone else's to protect.
     */
    public function update(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Update:User') && ! $user->isDeveloper();
    }

    public function delete(AuthUser $authUser, User $user): bool
    {
        return $authUser->can('Delete:User')
            && ! $user->isDeveloper()
            && ! $user->is($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:User');
    }
}
