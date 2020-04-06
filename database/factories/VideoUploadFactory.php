<?php

$factory->define(Ushahidi\App\VideoUpload::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
    ];
});
