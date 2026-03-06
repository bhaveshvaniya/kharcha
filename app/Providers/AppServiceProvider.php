<?php

namespace App\Providers;

use App\Models\Goal;
use App\Models\Transaction;
use App\Policies\GoalPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Transaction::class => TransactionPolicy::class,
        Goal::class        => GoalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
