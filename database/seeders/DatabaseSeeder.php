<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BookService;
use App\Models\Category;
use App\Models\CustomerInfos;
use App\Models\User;
use \App\Models\ExpertInfos;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create();
        ExpertInfos::factory(3)->create();
        CustomerInfos::factory(5)->create();
        Category::factory(10)->create();
        Service::factory(20)->create();
        BookService::factory(4)->create();
    }
}
