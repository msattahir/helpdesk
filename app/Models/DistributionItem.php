<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'reference_no'];

    public function item(){
        return $this->belongsTo(Item::class, 'item_id')->orderBy('name')->orderBy('model');
    }

    public function last_distribution(){
        return $this->hasOne(ItemDistribution::class, 'distribution_item_id')->latestOfMany();
    }
}
