<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentOrder extends Model
{
    protected $fillable = ['pos1_id', 'memo', 'total_price', 'order_date'];

    public function pos1(): BelongsTo
    {
        return $this->belongsTo(Pos::class, 'pos1_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChildOrderItem::class, 'parent_order_id');
    }

    public function orderedItems()
{
    return $this->belongsToMany(Item::class, 'child_order_items', 'parent_order_id', 'item_id')
                ->withPivot(['quantity', 'unit_price', 'total_price']);
}
    public function updateTotalPrice()
    {
        $this->total_price = $this->items->sum('total_price');
        $this->save();
        return $this;
    }

    protected $casts = [
    'order_date' => 'datetime:Y-m-d\TH:i',
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s'

];


}