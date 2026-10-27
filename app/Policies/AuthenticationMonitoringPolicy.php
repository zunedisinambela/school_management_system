<?php

declare(strict_types=1);

namespace App\Policies;

use Binafy\LaravelUserMonitoring\Models\AuthenticationMonitoring;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AuthenticationMonitoringPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuthenticationMonitoring');
    }

    public function view(AuthUser $authUser, AuthenticationMonitoring $authenticationMonitoring): bool
    {
        return $authUser->can('View:AuthenticationMonitoring');
    }

    public function delete(AuthUser $authUser, AuthenticationMonitoring $authenticationMonitoring): bool
    {
        return $authUser->can('Delete:AuthenticationMonitoring');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuthenticationMonitoring');
    }
}
