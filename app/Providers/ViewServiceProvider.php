<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         //check if request is admin
        if(request()->is('admin/*') || request()->is('admin')){

            //send cache to every view under admin
            View()->composer('*', function ($view) {

                if(!Cache::has('admin_side_menu')){
                    //make cache with  main permition come from permission model tree function
                    Cache::forever('admin_side_menu', Permission::tree());
                }
                $admin_side_menu = Cache::get('admin_side_menu');
                $view->with(
                    [
                        //send admin side menu cache as variable to view page
                        'admin_side_menu'=>$admin_side_menu
                    ]
                );
            });
        }
    }
}
