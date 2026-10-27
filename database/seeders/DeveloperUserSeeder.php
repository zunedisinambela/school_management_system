<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeveloperUserSeeder extends Seeder
{
    /**
     * Seed the default developer account.
     */
    public function run(): void
    {
        // Known credentials: never create this account outside local dev.
        if (! app()->environment('local')) {
            return;
        }

        // forceFill: email_verified_at is intentionally not mass-assignable.
        $user = User::firstOrNew(['email' => 'developer@gmail.com']);
        $user->forceFill([
            'name' => 'developer',
            'password' => 'developer#123',
            'email_verified_at' => now(),
        ])->save();

        $developer = Role::firstOrCreate([
            'name' => config('filament-shield.super_admin.name'),
            'guard_name' => 'web',
        ]);

        // Access is already granted via Shield's Gate::before; syncing makes it visible in the role editor.
        $developer->syncPermissions(Permission::where('guard_name', 'web')->get());

        $user->assignRole($developer);
    }
}
