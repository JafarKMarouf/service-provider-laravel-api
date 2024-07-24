<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BookService;
use App\Models\Category;
use App\Models\CustomerInfos;
use App\Models\User;
use \App\Models\ExpertInfos;
use App\Models\Payment;
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
        Category::factory(9)->create();
        Service::factory(16)->create();
        CustomerInfos::factory(6)->create();
        ExpertInfos::factory(16)->create();
        BookService::factory(12)->create();
        Payment::factory(6)->create();
    }
}
