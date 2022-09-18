<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model{
    use HasFactory;

    protected $fillable = [ 'region', 'country_code', 'unique_id', 'page_id', 'referrer', 'device', 'country', 'city', 'ip_address'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    function clicks(){
        return $this->hasMany(Clicks::class, 'session_id');
    }

    function views(){
        return $this->hasMany(Views::class, 'session_id', 'unique_id');
    }

    

}
