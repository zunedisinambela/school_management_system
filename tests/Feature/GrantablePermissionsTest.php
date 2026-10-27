<?php

namespace Tests\Feature;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GrantablePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->each(fn (string $action) => Permission::create(['name' => "{$action}:User"]));

        $operatorRole = Role::create(['name' => 'operator']);
        $operatorRole->givePermissionTo(collect(['ViewAny', 'View', 'Create', 'Update'])
            ->map(fn (string $action): Permission => Permission::create(['name' => "{$action}:Role"]))
            ->push('ViewAny:User', 'View:User'));

        $this->operator = tap(User::factory()->create(), fn (User $user) => $user->assignRole($operatorRole));
    }

    public function test_operator_creating_role_only_grants_permissions_they_hold(): void
    {
        $this->actingAs($this->operator);

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'guru',
                UserResource::class => ['ViewAny:User', 'Delete:User'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEqualsCanonicalizing(['ViewAny:User'], $this->permissionNamesOf('guru'));
    }

    public function test_operator_editing_role_keeps_permissions_outside_their_reach(): void
    {
        $this->actingAs($this->operator);
        $teacherRole = Role::create(['name' => 'guru'])->givePermissionTo('View:User', 'Delete:User');

        Livewire::test(EditRole::class, ['record' => $teacherRole->getKey()])
            ->fillForm([UserResource::class => ['ViewAny:User']])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEqualsCanonicalizing(['ViewAny:User', 'Delete:User'], $this->permissionNamesOf('guru'));
    }

    public function test_developer_grants_any_permission(): void
    {
        $developerRole = Role::create(['name' => config('filament-shield.super_admin.name')]);
        $this->actingAs(tap(User::factory()->create(), fn (User $user) => $user->assignRole($developerRole)));

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'guru',
                UserResource::class => ['ViewAny:User', 'Delete:User'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEqualsCanonicalizing(['ViewAny:User', 'Delete:User'], $this->permissionNamesOf('guru'));
    }

    /**
     * @return array<int, string>
     */
    private function permissionNamesOf(string $roleName): array
    {
        return Role::findByName($roleName)->permissions()->pluck('name')->all();
    }
}
