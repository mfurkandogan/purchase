<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\Device\DeviceRepository;
use App\Repositories\Device\IDeviceRepository;
use App\Repositories\IRepository;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IRepository::class, BaseRepository::class);
        $this->app->bind(IOrderRepository::class, OrderRepository::class);
        $this->app->bind(IDeviceRepository::class, DeviceRepository::class);

        $this->app->singleton(OrderRepository::class, function () {
            return new OrderRepository(new Order());
        });

        $this->app->singleton(DeviceRepository::class, function () {
            return new DeviceRepository(new Device());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
