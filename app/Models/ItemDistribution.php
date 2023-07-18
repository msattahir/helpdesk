<?php

namespace App\Models;

use App\Models\DistributionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemDistribution extends Model
{
    use HasFactory;

    protected $fillable = ['distribution_item_id', 'remark', 'status', 'authorize_staff_id', 'valid_from', 'time', 'distributionable_id', 'distributionable_type'];

    protected static function booted(){
        static::addGlobalScope('valid', function ($query) {
            $query->whereNull('valid_until');
        });
    }

    public function distributionable(){
        return $this->morphTo();
    }

    public function staff(){
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function ddd(){
        return $this->belongsTo(Ddd::class, 'ddd_id');
    }

    public function distribution_item(){
        return $this->belongsTo(DistributionItem::class, 'distribution_item_id');
    }

    public function item(){
        return $this->hasOneThrough(Item::class, DistributionItem::class, 'id', 'id', 'distribution_item_id', 'item_id');
    }
}
