<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliExpressSavedProduct extends Model
{
    use HasFactory;

    protected $table = 'aliexpress_saved_products';

    protected $fillable = [
        'user_id',
        'aliexpress_product_id'
    ];

    /**
     * Get the user that saved the product
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the AliExpress product that was saved
     */
    public function aliexpressProduct()
    {
        return $this->belongsTo(AliExpressProduct::class, 'aliexpress_product_id');
    }

    /**
     * Toggle save status for a product
     *
     * @param int $userId
     * @param int $aliexpressProductId
     * @return array
     */
    public static function toggleSave($userId, $aliexpressProductId)
    {
        $existingSave = self::where('user_id', $userId)
            ->where('aliexpress_product_id', $aliexpressProductId)
            ->first();

        if ($existingSave) {
            $existingSave->delete();
            return [
                'action' => 'removed',
                'message' => 'Product removed from saved items'
            ];
        } else {
            self::create([
                'user_id' => $userId,
                'aliexpress_product_id' => $aliexpressProductId
            ]);
            return [
                'action' => 'saved',
                'message' => 'Product saved successfully'
            ];
        }
    }
}
