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
		CustomerInfos::factory(3)->create();
		User::factory()->create();
		ExpertInfos::factory(3)->create();
		Category::factory(10)->create();
		Service::factory(15)->create();
		BookService::factory(16)->create();
	}
}
