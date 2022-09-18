<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Views extends Model
{
    use HasFactory;

    protected $fillable = ['unique_id', 'session_id', 'page_id'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    function session(){
        return $this->belongsTo(Sessions::class, 'session_id', 'unique_id');
    }

}
