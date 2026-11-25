<?php

namespace App\Providers;

use App\Models\Enrollment;
use App\Observers\EnrollmentObserver;
use App\Repositories\Contracts\OfferingRepositoryInterface;
use App\Repositories\OfferingRepository;
use App\Support\AcademicConfigSingleton;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AcademicConfigSingleton::class, fn () => AcademicConfigSingleton::getInstance());
        $this->app->bind(OfferingRepositoryInterface::class, OfferingRepository::class);
    }

    public function boot(): void
    {
        Enrollment::observe(EnrollmentObserver::class);
    }
}
