<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PdfConverter;

class ConvertPDFService extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PdfConverter::class, function ($app) {
            return new PdfConverter();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
