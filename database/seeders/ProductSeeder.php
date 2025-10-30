<?php
// database/seeders/ProductSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $keripikPisang = Category::where('slug', 'keripik-pisang')->first();
        $snackAsin = Category::where('slug', 'snack-asin')->first();
        $snackManis = Category::where('slug', 'snack-manis')->first();

        $products = [
            // Keripik Pisang
            [
                'name' => 'Keripik Pisang Original',
                'slug' => 'keripik-pisang-original',
                'description' => 'Keripik pisang renyah dengan rasa original yang klasik. Dibuat dari pisang pilihan dengan proses penggorengan yang sempurna.',
                'price' => 15000,
                'stock' => 50,
                'category_id' => $keripikPisang->id,
                'is_active' => true,
                'is_featured' => true,
                'weight' => 250,
                'whatsapp_contact' => '81234567890' // Format: 8xxxx
            ],
            [
                'name' => 'Keripik Pisang Balado',
                'slug' => 'keripik-pisang-balado',
                'description' => 'Keripik pisang dengan bumbu balado pedas yang menggigit. Cocok untuk pecinta makanan pedas.',
                'price' => 18000,
                'stock' => 30,
                'category_id' => $keripikPisang->id,
                'is_active' => true,
                'is_featured' => false,
                'weight' => 250,
                'whatsapp_contact' => '81234567890'
            ],
            [
                'name' => 'Keripik Pisang Coklat',
                'slug' => 'keripik-pisang-coklat',
                'description' => 'Keripik pisang dengan lapisan coklat manis yang lezat. Perpaduan sempurna antara renyah dan manis.',
                'price' => 20000,
                'stock' => 25,
                'category_id' => $keripikPisang->id,
                'is_active' => true,
                'is_featured' => true,
                'weight' => 250,
                'whatsapp_contact' => '81234567890'
            ],
            
            // Snack Asin
            [
                'name' => 'Keripik Tempe Gurih',
                'slug' => 'keripik-tempe-gurih',
                'description' => 'Keripik tempe renyah dengan bumbu gurih yang pas di lidah. Camilan sehat dan bergizi.',
                'price' => 12000,
                'stock' => 40,
                'category_id' => $snackAsin->id,
                'is_active' => true,
                'is_featured' => false,
                'weight' => 200,
                'whatsapp_contact' => '82187654321'
            ],
            [
                'name' => 'Kerupuk Udang',
                'slug' => 'kerupuk-udang',
                'description' => 'Kerupuk udang asli dengan rasa udang yang kuat. Renyah dan cocok untuk lauk atau camilan.',
                'price' => 10000,
                'stock' => 60,
                'category_id' => $snackAsin->id,
                'is_active' => true,
                'is_featured' => false,
                'weight' => 150,
                'whatsapp_contact' => '82187654321'
            ],
            [
                'name' => 'Kacang Bawang',
                'slug' => 'kacang-bawang',
                'description' => 'Kacang tanah panggang dengan bumbu bawang yang gurih dan renyah. Cocok untuk teman ngemil.',
                'price' => 8000,
                'stock' => 80,
                'category_id' => $snackAsin->id,
                'is_active' => true,
                'is_featured' => true,
                'weight' => 100,
                'whatsapp_contact' => '82187654321'
            ],
            
            // Snack Manis
            [
                'name' => 'Dodol Betawi',
                'slug' => 'dodol-betawi',
                'description' => 'Dodol Betawi dengan rasa manis legit yang khas. Dibuat dengan resep tradisional turun temurun.',
                'price' => 25000,
                'stock' => 20,
                'category_id' => $snackManis->id,
                'is_active' => true,
                'is_featured' => true,
                'weight' => 300,
                'whatsapp_contact' => '85712345678'
            ],
            [
                'name' => 'Permen Jahe',
                'slug' => 'permen-jahe',
                'description' => 'Permen jahe hangat dengan rasa manis dan sedikit pedas. Baik untuk tenggorokan dan menghangatkan badan.',
                'price' => 5000,
                'stock' => 100,
                'category_id' => $snackManis->id,
                'is_active' => true,
                'is_featured' => false,
                'weight' => 50,
                'whatsapp_contact' => '85712345678'
            ],
            [
                'name' => 'Biskuit Kelapa',
                'slug' => 'biskuit-kelapa',
                'description' => 'Biskuit renyah dengan rasa kelapa yang manis dan gurih. Cocok untuk teman minum teh atau kopi.',
                'price' => 15000,
                'stock' => 35,
                'category_id' => $snackManis->id,
                'is_active' => true,
                'is_featured' => false,
                'weight' => 200,
                'whatsapp_contact' => '85712345678'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}