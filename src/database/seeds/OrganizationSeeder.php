<?php

use App\Models\Organization;
use App\Services\Base;
use App\Services\BillyClient;
use App\Services\Product;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{


    public function run(BillyClient $client)
    {
        $erpOrganization = $client->getOrganization();
        Organization::insert([
            'name' => $erpOrganization['organization']['name'],
            'erpId' => $erpOrganization['organization']['id'],
        ]);
    }
}