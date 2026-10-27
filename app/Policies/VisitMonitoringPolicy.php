<?php

declare(strict_types=1);

namespace App\Policies;

use Binafy\LaravelUserMonitoring\Models\VisitMonitoring;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VisitMonitoringPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisitMonitoring');
    }

    public function view(AuthUser $authUser, VisitMonitoring $visitMonitoring): bool
    {
        return $authUser->can('View:VisitMonitoring');
    }

    public function delete(AuthUser $authUser, VisitMonitoring $visitMonitoring): bool
    {
        return $authUser->can('Delete:VisitMonitoring');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VisitMonitoring');
    }
}
