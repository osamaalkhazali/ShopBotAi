<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's recently viewed products.
     */
    public function productViews(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    /**
     * Get the user's saved products.
     */
    public function savedProducts(): HasMany
    {
        return $this->hasMany(SavedProduct::class);
    }

    /**
     * Get the user's recently viewed products with eager loading.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentlyViewedProducts($limit = 6)
    {
        return $this->productViews()
            ->with('product.category')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($view) {
                return $view->product;
            });
    }

    /**
     * Get the user's saved products with eager loading.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSavedProducts($limit = 10)
    {
        return $this->savedProducts()
            ->with('product.category')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($saved) {
                return $saved->product;
            });
    }

    /**
     * Check if a product is saved by the user.
     *
     * @param int $productId
     * @return bool
     */
    public function hasSavedProduct($productId): bool
    {
        return $this->savedProducts()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get the chat sessions owned by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * Get the user's AliExpress product views.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliexpressProductViews(): HasMany
    {
        return $this->hasMany(AliExpressViewedProduct::class);
    }

    /**
     * Get the user's saved AliExpress products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliexpressSavedProducts(): HasMany
    {
        return $this->hasMany(AliExpressSavedProduct::class);
    }

    /**
     * Get the user's AliExpress chat sessions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliexpressChatSessions(): HasMany
    {
        return $this->hasMany(AliExpressChatSession::class);
    }

    /**
     * Get the user's recently viewed AliExpress products with eager loading.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentlyViewedAliExpressProducts($limit = 6)
    {
        return $this->aliexpressProductViews()
            ->with('aliexpressProduct')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($view) {
                return $view->aliexpressProduct;
            });
    }

    /**
     * Get the user's saved AliExpress products with eager loading.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSavedAliExpressProducts($limit = 10)
    {
        return $this->aliexpressSavedProducts()
            ->with('aliexpressProduct')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($saved) {
                return $saved->aliexpressProduct;
            });
    }

    /**
     * Check if an AliExpress product is saved by the user.
     *
     * @param int $productId
     * @return bool
     */
    public function hasSavedAliExpressProduct($productId): bool
    {
        return $this->aliexpressSavedProducts()
            ->whereHas('aliexpressProduct', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }
}
