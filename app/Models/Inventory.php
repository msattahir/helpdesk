<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    protected $table = 'inventory';
    use HasFactory;

    protected $fillable = ['item_id', 'quantity', 'staff_id'];

    protected static function booted(){
        static::addGlobalScope('valid', function ($query) {
            $query->whereNull('valid_until');
        });
    }

    public function scopeTotalQuantity($query){
        return $query->select('item_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('item_id');
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function staff(){
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
