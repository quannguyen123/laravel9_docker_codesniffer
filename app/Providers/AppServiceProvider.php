<?php

namespace App\Providers;

use App\Repositories\AlertJobRepository;
use App\Repositories\AlertJobRepositoryEloquent;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\CompanyLocationRepositoryEloquent;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyRepositoryEloquent;
use App\Repositories\DistrictRepository;
use App\Repositories\DistrictRepositoryEloquent;
use App\Repositories\JobLocationRepository;
use App\Repositories\JobLocationRepositoryEloquent;
use App\Repositories\JobRepository;
use App\Repositories\JobRepositoryEloquent;
use App\Repositories\JobTitleRepository;
use App\Repositories\JobTitleRepositoryEloquent;
use App\Repositories\OccupationRepository;
use App\Repositories\OccupationRepositoryEloquent;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderDetailRepositoryEloquent;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryEloquent;
use App\Repositories\PartnerInviteRepository;
use App\Repositories\PartnerInviteRepositoryEloquent;
use App\Repositories\PartnerRepository;
use App\Repositories\PartnerRepositoryEloquent;
use App\Repositories\ProvinceRepository;
use App\Repositories\ProvinceRepositoryEloquent;
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
        $this->app->bind(JobLocationRepository::class, JobLocationRepositoryEloquent::class);
        $this->app->bind(OrderRepository::class, OrderRepositoryEloquent::class);
        $this->app->bind(OrderDetailRepository::class, OrderDetailRepositoryEloquent::class);
        $this->app->bind(JobRepository::class, JobRepositoryEloquent::class);
        $this->app->bind(CompanyLocationRepository::class, CompanyLocationRepositoryEloquent::class);
        $this->app->bind(PartnerRepository::class, PartnerRepositoryEloquent::class);
        $this->app->bind(PartnerInviteRepository::class, PartnerInviteRepositoryEloquent::class);
        $this->app->bind(AlertJobRepository::class, AlertJobRepositoryEloquent::class);
        $this->app->bind(ProvinceRepository::class, ProvinceRepositoryEloquent::class);
        $this->app->bind(DistrictRepository::class, DistrictRepositoryEloquent::class);
    }
}
