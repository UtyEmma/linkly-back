<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Links extends Model {
    use HasFactory;

    protected $fillable = ['unique_id', 'user_id', 'page_id', 'thumbnail', 'position', 'title', 'url', 'desc', 'icon', 'shorturl', 'status'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'draft'
    ];

    function scopeActive($query){
        return $query->where('status', true);
    }

    function clicks(){
        return $this->hasMany(Clicks::class, 'link_id');
    }

    

}
