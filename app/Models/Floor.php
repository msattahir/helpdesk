<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    public function ddds(){
        return $this->hasMany(Ddd::class, 'floor', 'name');
    }

    /** Helpdesk Supports */
    public function helpdesks(){
        return $this->hasManyThrough(HelpdeskRequest::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id');
    }

    public function pending_helpdesks(){
        return $this->hasManyThrough(HelpdeskRequest::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Pending');
    }

    public function inprogress_helpdesks(){
        return $this->hasManyThrough(HelpdeskRequest::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'In-Progress');
    }

    public function resolved_helpdesks(){
        return $this->hasManyThrough(HelpdeskRequest::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Resolved');
    }

    public function unresolved_helpdesks(){
        return $this->hasManyThrough(HelpdeskRequest::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Unresolved');
    }

    /** Item Distributions */
    public function distributions(){
        // return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id');
        return $this->hasManyThrough(
            ItemDistribution::class,
            Staff::class,
            'ddd_id',
            'distributionable_id',
            'id',
            'id'
        );
    }

    public function allocated_items(){
        return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Allocated');
    }

    public function configured_items(){
        return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Configured');
    }

    public function installed_items(){
        return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Installed');
    }

    public function distributed_items(){
        return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Distributed');
    }

    public function returned_items(){
        return $this->hasManyThrough(ItemDistribution::class, Ddd::class, 'floor', 'ddd_id', 'name', 'id')
        ->where('status', 'Returned');
    }
}
