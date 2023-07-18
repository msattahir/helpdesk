<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'activity'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->staff_id = auth()->id();
        });
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
