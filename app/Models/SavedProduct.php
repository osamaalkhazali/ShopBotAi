<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedProduct extends Model
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
     * Get the user that owns the saved product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was saved.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Toggle (save or remove) a product for a user.
     *
     * @param int $userId
     * @param int $productId
     * @return array
     */
    public static function toggleSave(int $userId, int $productId): array
    {
        $saved = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($saved) {
            // Product was already saved, so remove it
            $saved->delete();
            return [
                'status' => 'removed',
                'message' => 'Product removed from saved items'
            ];
        } else {
            // Product wasn't saved, so save it
            self::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return [
                'status' => 'saved',
                'message' => 'Product saved successfully'
            ];
        }
    }
}
