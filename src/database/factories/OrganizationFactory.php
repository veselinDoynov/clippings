<?php

use App\Models\Organization;

$factory->define(Organization::class, function (Faker\Generator $faker) {
    return [
        'name' => 'Braainy ApS',
        'erpId' => 'cwNMzNn1TOWhrYwyb6jdfA',
    ];
});