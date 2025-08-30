<?php
// app/Http/Controllers/ProductController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        
        // Detect search method based on configuration
        $useScout = config('scout.driver') === 'tntsearch' && !empty($request->search);
        
        if ($useScout) {
            $products = $this->searchWithScout($request);
        } else {
            $products = $this->searchWithDatabase($request);
        }

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Search menggunakan Laravel Scout (TNTSearch)
     */
    private function searchWithScout(Request $request)
    {
        $searchQuery = trim($request->search);
        
        // Base Scout search
        $query = Product::search($searchQuery);
        
        // Apply category filter
        if ($request->category) {
            $query->where('category_slug', $request->category);
        }
        
        // Get paginated results
        $products = $query->paginate(12);
        
        // If Scout returns no results, fallback to manual search
        if ($products->total() === 0) {
            Log::info("Scout returned 0 results for: $searchQuery, falling back to manual search");
            return $this->searchWithDatabase($request);
        }
        
        return $products;
    }

    /**
     * Search menggunakan database manual dengan fuzzy logic
     */
    private function searchWithDatabase(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);
        
        // Apply category filter
        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Apply search filter
        if ($request->search) {
            $searchTerm = trim($request->search);
            $query = $this->applySmartSearch($query, $searchTerm);
        }
        
        return $query->paginate(12);
    }

    /**
     * Apply smart search dengan multiple algorithms
     */
    private function applySmartSearch($query, $search)
    {
        // Priority 1: Exact matches
        $exactQuery = clone $query;
        $exactCount = $exactQuery->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('sku', 'like', '%' . $search . '%');
        })->count();

        if ($exactCount > 0) {
            // Combine exact and fuzzy results with proper ordering
            return $query->where(function($mainQuery) use ($search) {
                $mainQuery->where(function($exactQuery) use ($search) {
                    // Exact matches (highest priority)
                    $exactQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('description', 'like', '%' . $search . '%')
                             ->orWhere('sku', 'like', '%' . $search . '%');
                })->orWhere(function($fuzzyQuery) use ($search) {
                    // Fuzzy matches (lower priority)
                    $this->addFuzzySearchConditions($fuzzyQuery, $search);
                });
            })->orderByRaw($this->getSearchOrderBy($search));
        }

        // If no exact matches, use comprehensive fuzzy search
        return $query->where(function($fuzzyQuery) use ($search) {
            $this->addFuzzySearchConditions($fuzzyQuery, $search);
        })->orderByRaw($this->getSearchOrderBy($search));
    }

    /**
     * Add various fuzzy search conditions
     */
    private function addFuzzySearchConditions($query, $search)
    {
        $search = strtolower(trim($search));
        
        // Method 1: SOUNDEX for phonetic matching
        $query->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$search]);
        
        // Method 2: Character similarity
        $query->orWhereRaw('CHAR_LENGTH(name) - CHAR_LENGTH(REPLACE(LOWER(name), LOWER(?), "")) > 0', [$search]);
        
        // Method 3: Generate and search variations
        $searchVariations = $this->generateSearchVariations($search);
        foreach ($searchVariations as $variation) {
            $query->orWhere('name', 'like', '%' . $variation . '%');
        }
        
        // Method 4: Word boundary matching
        $searchWords = explode(' ', $search);
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (strlen($word) > 2) {
                $query->orWhere('name', 'like', '%' . $word . '%');
            }
        }
        
        // Method 5: Category search
        $query->orWhereHas('category', function($categoryQuery) use ($search, $searchVariations) {
            $categoryQuery->where('name', 'like', '%' . $search . '%');
            foreach ($searchVariations as $variation) {
                $categoryQuery->orWhere('name', 'like', '%' . $variation . '%');
            }
        });
    }

    /**
     * Generate search variations for common Indonesian typos
     */
    private function generateSearchVariations($search)
    {
        $variations = [$search];
        
        // Common Indonesian vowel substitutions
        $vowelSubs = [
            'i' => ['e', 'a'],
            'e' => ['i', 'a'], 
            'a' => ['e', 'o'],
            'o' => ['a', 'u'],
            'u' => ['o', 'i']
        ];
        
        // Common consonant substitutions
        $consonantSubs = [
            'k' => ['c', 'g'],
            'c' => ['k', 's'],
            'p' => ['b'],
            'b' => ['p'],
            't' => ['d'],
            'd' => ['t']
        ];
        
        // Generate vowel variations
        foreach ($vowelSubs as $original => $replacements) {
            if (strpos($search, $original) !== false) {
                foreach ($replacements as $replacement) {
                    $variations[] = str_replace($original, $replacement, $search);
                }
            }
        }
        
        // Generate consonant variations
        foreach ($consonantSubs as $original => $replacements) {
            if (strpos($search, $original) !== false) {
                foreach ($replacements as $replacement) {
                    $variations[] = str_replace($original, $replacement, $search);
                }
            }
        }
        
        // Specific Indonesian food word variations
        $specificVariations = [
            'keripik' => 'kerepek',
            'kerepek' => 'keripik',
            'coklat' => 'cokelat',
            'cokelat' => 'coklat',
            'bakso' => 'baso',
            'baso' => 'bakso',
            'pisang' => 'pisant',
            'pisant' => 'pisang'
        ];
        
        foreach ($specificVariations as $original => $variation) {
            if (strpos($search, $original) !== false) {
                $variations[] = str_replace($original, $variation, $search);
            }
        }
        
        return array_unique(array_filter($variations));
    }

    /**
     * Generate ORDER BY clause for search relevance
     */
    private function getSearchOrderBy($search)
    {
        $escapedSearch = addslashes($search);
        
        return "
            CASE 
                WHEN LOWER(name) = LOWER('$escapedSearch') THEN 1
                WHEN LOWER(name) LIKE LOWER('$escapedSearch%') THEN 2  
                WHEN LOWER(name) LIKE LOWER('%$escapedSearch%') THEN 3
                WHEN SOUNDEX(name) = SOUNDEX('$escapedSearch') THEN 4
                WHEN sku LIKE '%$escapedSearch%' THEN 5
                ELSE 6 
            END ASC, 
            is_featured DESC,
            stock DESC,
            name ASC
        ";
    }

    /**
     * API endpoint untuk search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $search = trim($request->get('q', ''));
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Try Scout first if available
        if (config('scout.driver') === 'tntsearch') {
            try {
                $suggestions = Product::search($search)
                    ->take(10)
                    ->get()
                    ->pluck('name')
                    ->unique()
                    ->values()
                    ->toArray();
                
                if (count($suggestions) > 0) {
                    return response()->json($suggestions);
                }
            } catch (\Exception $e) {
                Log::warning('Scout search failed for suggestions: ' . $e->getMessage());
            }
        }

        // Fallback to database search
        $suggestions = Product::where('is_active', true)
            ->where(function($query) use ($search) {
                $this->addFuzzySearchConditions($query, $search);
            })
            ->select('name')
            ->distinct()
            ->limit(10)
            ->pluck('name')
            ->toArray();

        return response()->json($suggestions);
    }

    /**
     * Method untuk rebuild search index (utility)
     */
    public function rebuildSearchIndex()
    {
        if (config('scout.driver') !== 'tntsearch') {
            return response()->json(['error' => 'Scout TNTSearch not configured'], 400);
        }

        try {
            // Clear existing index
            Product::removeAllFromSearch();
            
            // Rebuild index
            Product::makeAllSearchable();
            
            return response()->json(['message' => 'Search index rebuilt successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to rebuild search index: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to rebuild index'], 500);
        }
    }

    // ==================
    // EXISTING METHODS
    // ==================

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Get related products menggunakan search yang sama
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function featured()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->limit(8)
            ->get();

        return view('products.featured', compact('featuredProducts'));
    }

    /**
     * Advanced search dengan filters
     */
    public function advancedSearch(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Price range filter
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Stock filter
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }

        // Featured filter
        if ($request->featured) {
            $query->where('is_featured', true);
        }

        // Apply search term
        if ($request->search) {
            $query = $this->applySmartSearch($query, $request->search);
        }

        // Category filter
        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('name', 'asc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('products.advanced-search', compact('products', 'categories'));
    }
}