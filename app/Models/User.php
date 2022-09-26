<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'unique_id', 'name', 'email', 'avatar', 'username', 'password', 'instagram', 'facebook', 'twitter', 'phone', 'desc'
    ];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function scopeRelations($query){
        return $query->with(['pages', 'pages.links' => function($query){
            $query->orderBy('position', 'desc');
        }, 'pages.visits' => function($query){
            $query->latest();
        }, 'pages.links.clicks' => function($query){
            $query->latest();
        }, 'pages.clicks' => function($query){
            $query->latest();
        }]);
    }

    function pages(){
        return $this->hasMany(Page::class, 'user_id', 'unique_id');
    }
}
