<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Keripik Pisang',
                'slug' => Str::slug('Keripik Pisang'),
                'description' => 'Berbagai varian keripik pisang crispy dan gurih',
                'is_active' => true,
            ],
            [
                'name' => 'Snack Asin',
                'slug' => Str::slug('Snack Asin'),
                'description' => 'Camilan asin yang cocok untuk teman minum teh atau kopi',
                'is_active' => true,
            ],
            [
                'name' => 'Snack Manis',
                'slug' => Str::slug('Snack Manis'),
                'description' => 'Camilan manis untuk menghilangkan stress dan mood booster',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']], // cek dulu biar tidak duplikat
                $category
            );
        }
    }
}
