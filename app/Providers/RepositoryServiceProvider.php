<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\BarberRepositoryInterface;
use App\Repositories\Eloquent\BarberRepository;
use App\Interfaces\ServiceRepositoryInterface;
use App\Repositories\Eloquent\ServiceRepository;
use App\Interfaces\AppointmentRepositoryInterface;
use App\Repositories\Eloquent\AppointmentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BarberRepositoryInterface::class, BarberRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(\App\Interfaces\FinancialRepositoryInterface::class, \App\Repositories\Eloquent\FinancialRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
