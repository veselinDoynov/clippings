<?php

use App\Models\Contact;

$factory->define(Contact::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'countryId' => 'DK'
    ];
});