<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\View\View;


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
        \App\Models\EqubPayment::observe(\App\Observers\EqubPaymentObserver::class);
        \App\Models\EqubDraw::observe(\App\Observers\EqubDrawObserver::class);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => \Livewire\Livewire::mount('manual-draw-player'),
        );
    }
}
