<?php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StoreSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->limit(8)
            ->get();

        // Get latest products
        $latestProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Get active categories - FIXED: Using whereHas instead of having
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->whereHas('products', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        // Get store settings
        $store = StoreSettings::current();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories', 'store'));
    }
}