<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = ['office_no', 'description', 'ddd_id', 'location_id'];

    public function distributions(){
        return $this->morphMany(ItemDistribution::class, 'distributionable');
    }

    public function ddd(){
        return $this->belongsTo(Ddd::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }
}
