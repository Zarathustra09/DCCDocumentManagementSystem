<?php

namespace App\Providers;

use App\Interfaces\CustomerInterface;
use App\Services\CustomerService;
use App\Interfaces\DocumentRegistryFileServiceInterface;
use App\Interfaces\DocumentRegistryServiceInterface;
use App\Services\DocumentFileService;
use App\Services\DocumentRegistryService;
use App\Interfaces\CategoryInterface;
use App\Services\CategoryService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerInterface::class, CustomerService::class);
        $this->app->bind(DocumentRegistryServiceInterface::class, DocumentRegistryService::class);
        $this->app->bind(DocumentRegistryFileServiceInterface::class, DocumentFileService::class);
        $this->app->bind(CategoryInterface::class, CategoryService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
