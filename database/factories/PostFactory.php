<?php

use Faker\Generator as Faker;

$factory->define(\Ushahidi\Modules\V5\Models\Post\Post::class, function (Faker $faker) {
    return [
        'id' => $faker->randomNumber(),
    ];
});
