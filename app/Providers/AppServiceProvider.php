<?php

namespace App\Providers;

use App\Policies\ActionMonitoringPolicy;
use App\Policies\AuthenticationMonitoringPolicy;
use App\Policies\VisitMonitoringPolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Binafy\LaravelUserMonitoring\Models\ActionMonitoring;
use Binafy\LaravelUserMonitoring\Models\AuthenticationMonitoring;
use Binafy\LaravelUserMonitoring\Models\VisitMonitoring;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentShield::enforcePolicies();

        // Bulk delete checks only deleteAny by default; run each record through its delete policy so protected records are skipped.
        DeleteBulkAction::configureUsing(fn (DeleteBulkAction $action): DeleteBulkAction => $action->authorizeIndividualRecords('delete'));

        // Monitoring models live in the vendor namespace, outside Laravel's policy discovery.
        Gate::policy(ActionMonitoring::class, ActionMonitoringPolicy::class);
        Gate::policy(AuthenticationMonitoring::class, AuthenticationMonitoringPolicy::class);
        Gate::policy(VisitMonitoring::class, VisitMonitoringPolicy::class);
    }
}
