<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //设置默认字符串生成长度，解决 utf8mb4 字符编码导致的问题。
        Schema::defaultStringLength(191);

        error_reporting(E_ALL ^ E_NOTICE);

        view()->composer('layouts/_game_list', function ($view) {
            $game_list = getPlatsGamesServers(2, 1, 0, 0, 0, 0, 0, 0, 2);
            $game_sort_list = getGameSorts();
            $view->with('game_list',$game_list)
            ->with('game_sort_list',$game_sort_list);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
