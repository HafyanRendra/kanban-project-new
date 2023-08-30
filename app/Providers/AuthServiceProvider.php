<?php

namespace App\Providers;
use App\Models\Task; // Ditambahkan
use App\Policies\TaskPolicy; // Ditambahkan
use App\Policies\RolePolicy; // Ditambahkan
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class, // Ditambahkan
        Role::class => RolePolicy::class, // Ditambahkan
    
    ];

    

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
