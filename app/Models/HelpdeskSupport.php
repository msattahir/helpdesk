<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskSupport extends Model
{
    use HasFactory;

    protected $fillable = ['helpdesk_request_id', 'staff_id', 'remark', 'status', 'valid_from', 'time'];

    protected static function booted(){
        static::addGlobalScope('valid', function ($query) {
            $query->whereNull('valid_until');
        });
    }

    public function request(){
        return $this->belongsTo(HelpdeskRequest::class, 'helpdesk_request_id');
    }

    public function staff(){
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function next_support(){
        return $this
        ->where('helpdesk_request_id', $this->helpdesk_request_id)
        ->where('id', '>', $this->id)
        ->orderBy('id')
        ->first();
    }
}
