<?php

namespace App\Filament\Resources\Roles\Concerns;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Stops a non-developer from saving a role with permissions they do not hold themselves,
 * which would let them create a stronger role and assign it to their own account.
 */
trait LimitsGrantablePermissions
{
    /**
     * @param  Collection<int, string>  $submitted  Permission names ticked in the form.
     * @param  Collection<int, string>  $current  Permission names the role holds before saving (empty on create).
     * @return Collection<int, string>
     */
    protected function limitToGrantablePermissions(Collection $submitted, Collection $current): Collection
    {
        /** @var User $actor */
        $actor = auth()->user();

        if ($actor->isDeveloper()) {
            return $submitted;
        }

        $actorPermissions = $actor->getAllPermissions()->pluck('name');

        return $this->resolveGrantablePermissions($submitted, $current, $actorPermissions);
    }

    /**
     * @param  Collection<int, string>  $submitted
     * @param  Collection<int, string>  $current
     * @param  Collection<int, string>  $actorPermissions  Every permission the acting non-developer holds.
     * @return Collection<int, string> The permission names the role ends up with.
     */
    protected function resolveGrantablePermissions(Collection $submitted, Collection $current, Collection $actorPermissions): Collection
    {
        // Permissions outside the actor's reach keep their previous state, so saving never strips what a developer granted.
        $outsideActorReach = $current->diff($actorPermissions);

        return $submitted
            ->intersect($actorPermissions)
            ->merge($outsideActorReach)
            ->unique()
            ->values();
    }
}
