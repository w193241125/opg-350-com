<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'password' => bcrypt('123456')，
        'remember_token' => str_random(10),
        'trueName' => '刘冠生'.str_random(2),
        'sex' =>array_random(array('男','女'),1),
        'position' => random_int(1,10),
        'dept' => random_int(1,10),
        'username' => str_random(10),
        'gid' => random_int(1,10),
        'loginTimes' => random_bytes(11),
        'lastLoginTime' => '2017-7-16 07:35:12',
        'lastLoginIP' => '192.168.0.0.1',
        'created_at' => '2017-7-16 07:35:12',
        'updated_at' => '2016-7-16 07:35:12',
    ];
});
