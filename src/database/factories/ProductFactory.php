<?php

use App\Models\Product;

$factory->define(Product::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomDigit,
        'currencyId' => 'DKK'
    ];
});