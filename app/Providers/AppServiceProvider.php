<?php

namespace App\Providers;

use App\Repositories\CompanyRepository;
use App\Repositories\CompanyRepositoryEloquent;
use App\Repositories\JobTitleRepository;
use App\Repositories\JobTitleRepositoryEloquent;
use App\Repositories\OccupationRepository;
use App\Repositories\OccupationRepositoryEloquent;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceRepositoryEloquent;
use App\Repositories\TagRepository;
use App\Repositories\TagRepositoryEloquent;
use App\Repositories\WelfareRepository;
use App\Repositories\WelfareRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(OccupationRepository::class, OccupationRepositoryEloquent::class);
        $this->app->bind(WelfareRepository::class, WelfareRepositoryEloquent::class);
        $this->app->bind(TagRepository::class, TagRepositoryEloquent::class);
        $this->app->bind(JobTitleRepository::class, JobTitleRepositoryEloquent::class);
        $this->app->bind(ServiceRepository::class, ServiceRepositoryEloquent::class);
        $this->app->bind(CompanyRepository::class, CompanyRepositoryEloquent::class);
    }
}
