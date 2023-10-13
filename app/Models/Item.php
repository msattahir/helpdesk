<?php

namespace App\Models;

use App\Models\Inventory;
use App\Models\ItemRequest;
use App\Models\ItemDistribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    // protected $appends = ['distribution_total', 'inventory_total', 'inventory_balance'];

    protected $fillable = ['name', 'model', 'category'];

    public function inventory(){
        return $this->hasMany(Inventory::class, 'item_id');
    }

    public function distribution_items(){
        return $this->hasMany(DistributionItem::class, 'item_id');
    }

    public function scopeTotalByStatus($query, $status, $opr = "=", $alias = "")
    {
        if($alias == "") $alias = strtolower($status);

        return $query->withCount(['distribution_items AS ' . $alias => function ($query) use ($status, $opr) {
            $query->whereHas('last_distribution', function ($query) use ($status, $opr) {
                $query->where('status', $opr, $status);
            });
        }]);
    }

    public function scopeInventoryTotal($query)
    {
        return $query->withSum(['inventory AS inventory_total' => fn ($query) =>
            $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
        ], 'quantity');
    }

    // public function getInventoryTotalAttribute()
    // {
    //     return $this->inventory()->sum('quantity');
    // }

    // public function getDistributionTotalAttribute()
    // {
    //     return $this->distribution_items()->with('last_distribution')->count();
    // }

    // public function getInventoryBalanceAttribute()
    // {
    //     $inventory_total = $this->inventory_total;
    //     $distribution_total = $this->distribution_total;

    //     return $inventory_total - $distribution_total;
    // }

    public function requested(){
        return $this->hasMany(ItemRequest::class, 'item_id');
    }
}
