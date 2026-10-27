<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    private Role $developerRole;

    private Role $operatorRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->developerRole = Role::create(['name' => config('filament-shield.super_admin.name')]);

        $this->operatorRole = Role::create(['name' => 'operator']);
        $this->operatorRole->givePermissionTo(collect(['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny'])
            ->map(fn (string $action): Permission => Permission::create(['name' => "{$action}:User"])));
    }

    public function test_developer_can_create_user_with_role(): void
    {
        $this->actingAs($this->makeUser($this->developerRole));

        Livewire::test(ManageUsers::class)
            ->callAction('create', [
                'name' => 'Guru Satu',
                'email' => 'guru@example.com',
                'password' => 'rahasia123',
                'roles' => [$this->operatorRole->id],
            ])
            ->assertHasNoFormErrors();

        $user = User::where('email', 'guru@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('operator'));
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    public function test_blank_password_on_edit_keeps_existing_password(): void
    {
        $this->actingAs($this->makeUser($this->developerRole));
        $user = $this->makeUser($this->operatorRole);
        $originalHash = $user->password;

        Livewire::test(ManageUsers::class)
            ->callAction(TestAction::make(EditAction::class)->table($user), [
                'name' => 'Nama Baru',
                'password' => '',
            ])
            ->assertHasNoFormErrors();

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame($originalHash, $user->password);
    }

    public function test_operator_cannot_edit_or_delete_developer_account(): void
    {
        $this->actingAs($this->makeUser($this->operatorRole));
        $developer = $this->makeUser($this->developerRole);

        Livewire::test(ManageUsers::class)
            ->assertActionHidden(TestAction::make(EditAction::class)->table($developer))
            ->assertActionHidden(TestAction::make(DeleteAction::class)->table($developer));
    }

    public function test_operator_cannot_assign_developer_role(): void
    {
        $this->actingAs($this->makeUser($this->operatorRole));

        Livewire::test(ManageUsers::class)
            ->callAction('create', [
                'name' => 'Penyusup',
                'email' => 'penyusup@example.com',
                'password' => 'rahasia123',
                'roles' => [$this->developerRole->id],
            ])
            ->assertHasFormErrors(['roles.0']);

        $this->assertDatabaseMissing('users', ['email' => 'penyusup@example.com']);
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $operator = $this->makeUser($this->operatorRole);
        $this->actingAs($operator);

        Livewire::test(ManageUsers::class)
            ->assertActionHidden(TestAction::make(DeleteAction::class)->table($operator));
    }

    private function makeUser(Role $role): User
    {
        return tap(User::factory()->create(), fn (User $user) => $user->assignRole($role));
    }
}
