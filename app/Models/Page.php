<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    use HasFactory;

    protected $fillable = ['unique_id', 'title', 'slug', 'user_id', 'desc', 'logo', 'meta_title', 'meta_desc', 'meta_tags', 'status'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => true
    ];

    function links(){
        return $this->hasMany(Links::class, 'page_id');
    }

    function visits(){
        return $this->hasMany(Sessions::class, 'page_id');
    }

    function clicks(){
        return $this->hasMany(Clicks::class, 'page_id', 'unique_id');
    }
    
    function views(){
        return $this->hasMany(Views::class, 'page_id', 'unique_id');
    }

}
