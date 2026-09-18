<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Concerns\BelongsToShop;

class Product extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'price',
        'cost_price',
        'stock',
        'alert_quantity',
        'image',
        'barcode',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}
