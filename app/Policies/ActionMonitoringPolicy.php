<?php

declare(strict_types=1);

namespace App\Policies;

use Binafy\LaravelUserMonitoring\Models\ActionMonitoring;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ActionMonitoringPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ActionMonitoring');
    }

    public function view(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('View:ActionMonitoring');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ActionMonitoring');
    }

    public function update(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('Update:ActionMonitoring');
    }

    public function delete(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('Delete:ActionMonitoring');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ActionMonitoring');
    }

    public function restore(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('Restore:ActionMonitoring');
    }

    public function forceDelete(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('ForceDelete:ActionMonitoring');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ActionMonitoring');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ActionMonitoring');
    }

    public function replicate(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('Replicate:ActionMonitoring');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ActionMonitoring');
    }
}
