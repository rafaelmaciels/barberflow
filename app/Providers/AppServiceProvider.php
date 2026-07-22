<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces
use App\Interfaces\AppointmentRepositoryInterface;
use App\Interfaces\BarberRepositoryInterface;
use App\Interfaces\ServiceRepositoryInterface;

// Repositories
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\BarberRepository;
use App\Repositories\Eloquent\ServiceRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            BarberRepositoryInterface::class,
            BarberRepository::class
        );

        $this->app->bind(
            ServiceRepositoryInterface::class,
            ServiceRepository::class
        );

        $this->app->bind(
            AppointmentRepositoryInterface::class,
            AppointmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath === '/tmp/database.sqlite' && !file_exists($dbPath)) {
                touch($dbPath);
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        }
    }
}
