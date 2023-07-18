<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestCategory extends Model
{
    use HasFactory;

    public function parent()
    {
        return $this->belongsTo(RequestCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(RequestCategory::class, 'parent_id');
    }

    public function helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'request_category_id');
    }

    public function pending_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'request_category_id')
        ->where('status', 'Pending');
    }

    public function inprogress_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'request_category_id')
        ->where('status', 'In-Progress');
    }

    public function resolved_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'request_category_id')
        ->where('status', 'Resolved');
    }

    public function unresolved_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'request_category_id')
        ->where('status', 'Unresolved');
    }
}
