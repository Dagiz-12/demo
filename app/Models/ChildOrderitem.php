<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildOrderItem extends Model
{
    protected $fillable = ['parent_order_id', 'item_id', 'quantity', 'unit_price', 'total_price'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class, 'parent_order_id');
    }

    public function item(): BelongsTo
{
    return $this->belongsTo(Item::class);
}

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total_price = $model->quantity * $model->unit_price;
        });

        static::saved(function ($model) {
            $model->parent->updateTotalPrice();
        });

        static::deleted(function ($model) {
            $model->parent->updateTotalPrice();
        });
    }

    protected $casts = [
    'unit_price' => 'float',
    'quantity' => 'integer',
    'total_price' => 'float'
];


}

