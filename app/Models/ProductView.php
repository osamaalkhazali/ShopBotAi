<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Get the user that owns the product view.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was viewed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Record a product view for a user, maintaining only the most recent 6 views.
     *
     * @param int $userId
     * @param int $productId
     * @return self
     */
    public static function recordView(int $userId, int $productId): self
    {
        // Update the timestamp if this product was already viewed by the user
        $view = self::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['updated_at' => now()]
        );

        // Get the count of user's viewed products
        $count = self::where('user_id', $userId)->count();

        // If more than 6 products, remove the oldest one(s)
        if ($count > 6) {
            self::where('user_id', $userId)
                ->orderBy('updated_at')
                ->limit($count - 6)
                ->delete();
        }

        return $view;
    }
}
