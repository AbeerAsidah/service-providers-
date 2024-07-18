<?php

namespace App\Providers;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Policies\ContactMessagePolicy;
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
        Route::bind('trashed_contact_message' , function($id){
            return ContactMessage::onlyTrashed()->findOrFail($id) ;
        });
    }
}
