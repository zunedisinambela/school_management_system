<?php

namespace Tests\Feature;

use App\Models\User;
use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    use RefreshDatabase;

    private Role $developerRole;

    private Role $operatorRole;

    private Role $teacherRole;

    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->developerRole = Role::create(['name' => config('filament-shield.super_admin.name')]);
        $this->teacherRole = Role::create(['name' => 'guru']);

        $this->operatorRole = Role::create(['name' => 'operator']);
        $this->operatorRole->givePermissionTo(collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->map(fn (string $action): Permission => Permission::create(['name' => "{$action}:Role"])));

        $this->operator = tap(User::factory()->create(), fn (User $user) => $user->assignRole($this->operatorRole));
    }

    public function test_operator_can_manage_roles_they_do_not_hold(): void
    {
        $this->assertTrue($this->operator->can('update', $this->teacherRole));
        $this->assertTrue($this->operator->can('delete', $this->teacherRole));
    }

    public function test_operator_cannot_change_developer_role_or_own_role(): void
    {
        $this->actingAs($this->operator);

        Livewire::test(ListRoles::class)
            ->assertActionHidden(TestAction::make(EditAction::class)->table($this->developerRole))
            ->assertActionHidden(TestAction::make(DeleteAction::class)->table($this->developerRole))
            ->assertActionHidden(TestAction::make(EditAction::class)->table($this->operatorRole))
            ->assertActionHidden(TestAction::make(DeleteAction::class)->table($this->operatorRole));
    }

    public function test_bulk_delete_skips_protected_roles(): void
    {
        $this->actingAs($this->operator);

        Livewire::test(ListRoles::class)
            ->selectTableRecords([$this->developerRole->getKey(), $this->operatorRole->getKey(), $this->teacherRole->getKey()])
            ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk());

        $this->assertModelExists($this->developerRole);
        $this->assertModelExists($this->operatorRole);
        $this->assertModelMissing($this->teacherRole);
    }

    public function test_developer_keeps_full_access(): void
    {
        $developer = tap(User::factory()->create(), fn (User $user) => $user->assignRole($this->developerRole));

        $this->assertTrue($developer->can('update', $this->developerRole));
        $this->assertTrue($developer->can('delete', $this->operatorRole));
    }
}
