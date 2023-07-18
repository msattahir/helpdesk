<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'item_id', 'quantity', 'description', 'valid_from', 'time'];

    protected static function booted(){
        static::addGlobalScope('valid', function ($query) {
            $query->whereNull('valid_until');
        });
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function staff(){
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
