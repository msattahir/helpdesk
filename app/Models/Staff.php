<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';

    protected $fillable = ['staff_no', 'name', 'email', 'ddd_id', 'location_id', 'role', 'status', 'password'];

    protected static function booted(){
        $controller_action = class_basename(request()->route()->getAction()['controller']);
        $controller_name = Str::before($controller_action, '@');

        if($controller_name != "AuthController") {
            static::addGlobalScope('select', function ($query) {
                $query->select(
                    'id',
                    'staff_no',
                    'name',
                    'email',
                    'ddd_id',
                    'location_id',
                    'role',
                    'status',
                    'created_at'
                );
            });
        }
    }

    public function ddd(){
        return $this->belongsTo(Ddd::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'Admin';
    }

    public function helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id');
    }

    public function pending_helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id')
        ->where('status', 'Pending');
    }

    public function inprogress_helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id')
        ->where('status', 'In-Progress');
    }

    public function escalated_helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id')
        ->where('status', 'Escalated');
    }

    public function resolved_helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id')
        ->where('status', 'Resolved');
    }

    public function unresolved_helpdesks(){
        return $this->hasMany(HelpdeskSupport::class, 'staff_id')
        ->where('status', 'Unresolved');
    }

    public function distributions(){
        return $this->morphMany(ItemDistribution::class, 'distributionable');
    }
}
