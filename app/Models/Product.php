<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'images',
        'category_id',
        'is_active',
        'is_featured',
        'weight',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ======================
    // LARAVEL SCOUT METHODS
    // ======================

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs()
    {
        return 'products_index';
    }

    /**
     * Get the indexable data array for the model.
     * Data ini yang akan diindex oleh Scout untuk pencarian
     */
    public function toSearchableArray()
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => strip_tags($this->description ?? ''),
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'is_featured' => $this->is_featured,
            'category_name' => $this->category->name ?? '',
            'category_slug' => $this->category->slug ?? '',
            'category_id' => $this->category_id,
            
            // Tambahan field untuk fuzzy search
            'search_text' => $this->generateSearchText(),
            'phonetic_name' => soundex($this->name),
            'name_variations' => $this->generateNameVariations(),
        ];

        return $array;
    }

    /**
     * Determine if the model should be searchable.
     * Hanya produk aktif yang akan diindex
     */
    public function shouldBeSearchable()
    {
        return $this->is_active === true;
    }

    /**
     * Get the Scout engine for the model.
     * Uncomment jika ingin override default engine
     */
    // public function searchableUsing()
    // {
    //     return app(\TeamTNT\TNTSearch\TNTSearch::class);
    // }

    /**
     * Modify the query used to retrieve models when making all searchable.
     */
    public function makeAllSearchableUsing($query)
    {
        return $query->with(['category'])->where('is_active', true);
    }

    // ========================
    // SEARCH HELPER METHODS
    // ========================

    /**
     * Generate comprehensive search text untuk indexing
     */
    private function generateSearchText()
    {
        $searchParts = [
            $this->name,
            $this->description ? strip_tags($this->description) : '',
            $this->sku,
            $this->category->name ?? '',
        ];

        // Tambahkan variasi nama untuk typo tolerance
        $nameVariations = $this->generateNameVariations();
        $searchParts = array_merge($searchParts, $nameVariations);

        return implode(' ', array_filter($searchParts));
    }

    /**
     * Generate variasi nama untuk menangani typo umum bahasa Indonesia
     */
    private function generateNameVariations()
    {
        $name = strtolower($this->name);
        $variations = [$name];

        // Common Indonesian vowel substitutions
        $vowelSubs = [
            'i' => 'e', 'e' => 'i',
            'a' => 'e', 'e' => 'a',
            'o' => 'u', 'u' => 'o',
        ];

        // Common consonant substitutions  
        $consonantSubs = [
            'k' => 'c', 'c' => 'k',
            'p' => 'b', 'b' => 'p',
            't' => 'd', 'd' => 't',
        ];

        // Generate vowel variations
        foreach ($vowelSubs as $from => $to) {
            if (strpos($name, $from) !== false) {
                $variations[] = str_replace($from, $to, $name);
            }
        }

        // Generate consonant variations
        foreach ($consonantSubs as $from => $to) {
            if (strpos($name, $from) !== false) {
                $variations[] = str_replace($from, $to, $name);
            }
        }

        // Specific Indonesian food variations
        $specificVariations = [
            'keripik' => 'kerepek',
            'kerepek' => 'keripik', 
            'coklat' => 'cokelat',
            'cokelat' => 'coklat',
            'bakso' => 'baso',
            'baso' => 'bakso',
        ];

        foreach ($specificVariations as $original => $variation) {
            if (strpos($name, $original) !== false) {
                $variations[] = str_replace($original, $variation, $name);
            }
        }

        return array_unique(array_filter($variations));
    }

    // ====================
    // EXISTING RELATIONS
    // ====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ===================
    // EXISTING ACCESSORS
    // ===================

    public function getMainImageAttribute()
    {
        return $this->images ? $this->images[0] ?? null : null;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // ====================
    // SEARCH SCOPES
    // ====================

    /**
     * Scope untuk pencarian manual (tanpa Scout)
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('sku', 'like', '%' . $search . '%')
              ->orWhereHas('category', function($categoryQuery) use ($search) {
                  $categoryQuery->where('name', 'like', '%' . $search . '%');
              });
        });
    }

    /**
     * Scope untuk fuzzy search manual
     */
    public function scopeFuzzySearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            // Exact matches
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              
              // Phonetic matching
              ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$search])
              
              // Character similarity
              ->orWhereRaw('CHAR_LENGTH(name) - CHAR_LENGTH(REPLACE(LOWER(name), LOWER(?), "")) > 0', [$search]);
            
            // Word variations
            $variations = $this->generateSearchVariations($search);
            foreach ($variations as $variation) {
                $q->orWhere('name', 'like', '%' . $variation . '%');
            }
        });
    }

    /**
     * Generate search variations (method untuk scope)
     */
    private function generateSearchVariations($search)
    {
        $variations = [strtolower($search)];
        
        // Vowel substitutions
        $vowelSubs = ['i' => 'e', 'e' => 'i', 'a' => 'e', 'o' => 'u'];
        
        foreach ($vowelSubs as $from => $to) {
            if (strpos($search, $from) !== false) {
                $variations[] = str_replace($from, $to, strtolower($search));
            }
        }

        return array_unique($variations);
    }

    // =======================
    // ADDITIONAL HELPER METHODS
    // =======================

    /**
     * Check if product is available for purchase
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active && $this->stock > 0;
    }

    /**
     * Get stock status text
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Stok Habis';
        } elseif ($this->stock <= 5) {
            return 'Stok Terbatas';
        } else {
            return 'Tersedia';
        }
    }

    /**
     * Get stock status class for styling
     */
    public function getStockStatusClassAttribute()
    {
        if ($this->stock <= 0) {
            return 'stock-empty';
        } elseif ($this->stock <= 5) {
            return 'stock-low';
        } else {
            return 'stock-available';
        }
    }
}