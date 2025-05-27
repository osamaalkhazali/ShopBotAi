<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliExpressViewedProduct extends Model
{
    use HasFactory;

    protected $table = 'aliexpress_viewed_products';

    protected $fillable = [
        'user_id',
        'aliexpress_product_id'
    ];

    /**
     * Get the user that viewed the product
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the AliExpress product that was viewed
     */
    public function aliexpressProduct()
    {
        return $this->belongsTo(AliExpressProduct::class, 'aliexpress_product_id');
    }

    /**
     * Record a product view for a user, maintaining only the most recent 6 views.
     *
     * @param int $userId
     * @param int $aliexpressProductId
     * @return self
     */
    public static function recordView($userId, $aliexpressProductId)
    {
        // Update the timestamp if this product was already viewed by the user
        $view = self::updateOrCreate(
            ['user_id' => $userId, 'aliexpress_product_id' => $aliexpressProductId],
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
