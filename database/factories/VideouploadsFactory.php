<?php


$factory->define(Ushahidi\App\Videouploads::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});
