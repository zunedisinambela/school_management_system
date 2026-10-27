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

    public function delete(AuthUser $authUser, ActionMonitoring $actionMonitoring): bool
    {
        return $authUser->can('Delete:ActionMonitoring');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ActionMonitoring');
    }
}
