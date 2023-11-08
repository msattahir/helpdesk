<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HelpdeskRequest extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'ddd_id', 'request_category_id', 'authorize_staff_id', 'description', 'valid_from', 'time'];

    protected static function booted(){
        static::addGlobalScope('valid', function ($query) {
            $query->whereNull('helpdesk_requests.valid_until');
        });
    }

    public function total(){
        return $this->hasMany(HelpdeskRequest::class, 'id');
    }

    public function scopePending($query){
        return $query->where('status', 'Pending');
    }

    public function scopeTreated($query){
        return $query->whereNot('status', 'Pending');
    }

    public function staff(){
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function ddd(){
        return $this->belongsTo(Ddd::class, 'ddd_id');
    }

    public function supports(){
        return $this->hasMany(HelpdeskSupport::class, 'helpdesk_request_id')->latest();
    }

    public function first_support() {
        return $this
        ->hasOne(HelpdeskSupport::class, 'helpdesk_request_id')
        ->oldestOfMany();
    }

    public function last_support() {
        return $this->hasOne(HelpdeskSupport::class, 'helpdesk_request_id')->latestOfMany();
    }

    public function request_category() {
        return $this->belongsTo(RequestCategory::class, 'request_category_id');
    }
}
