<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ddd extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'short', 'category', 'floor'];

    public function staff(){
        return $this->hasMany(Staff::class, 'ddd_id');
    }

    public function offices(){
        return $this->hasMany(Office::class);
    }

    // Helpdesk Supports
    public function helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'ddd_id');
    }

    public function pending_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'ddd_id')
        ->where('status', 'Pending');
    }

    public function inprogress_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'ddd_id')
        ->where('status', 'In-Progress');
    }

    public function resolved_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'ddd_id')
        ->where('status', 'Resolved');
    }

    public function unresolved_helpdesks(){
        return $this->hasMany(HelpdeskRequest::class, 'ddd_id')
        ->where('status', 'Unresolved');
    }

    // Item Distributions
    public function staff_distributions(){
        return $this->hasManyThrough(
            ItemDistribution::class,
            Staff::class,
            'ddd_id',
            'distributionable_id'
        )
        ->where('item_distributions.distributionable_type', Staff::class);
    }

    public function office_distributions(){
        return $this->hasManyThrough(
            ItemDistribution::class,
            Office::class,
            'ddd_id',
            'distributionable_id'
        )
        ->where('item_distributions.distributionable_type', Office::class);
    }

    // Staff
    public function staff_allocated(){
        return $this->staff_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Allocated');
        });
    }

    public function staff_configured(){
        return $this->staff_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Configured');
        });
    }

    public function staff_installed(){
        return $this->staff_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Installed');
        });
    }

    public function staff_distributed(){
        return $this->staff_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Distributed');
        });
    }

    public function staff_returned(){
        return $this->staff_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Returned');
        });
    }

    // Office
    public function office_allocated(){
        return $this->office_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Allocated');
        });
    }

    public function office_configured(){
        return $this->office_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Configured');
        });
    }

    public function office_installed(){
        return $this->office_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Installed');
        });
    }

    public function office_distributed(){
        return $this->office_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Distributed');
        });
    }

    public function office_returned(){
        return $this->office_distributions()
        ->where(function ($query) {
            $query->where('item_distributions.status', 'Returned');
        });
    }
}
