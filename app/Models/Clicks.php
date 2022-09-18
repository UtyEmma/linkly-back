<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clicks extends Model {
    use HasFactory;

    protected $fillable = ['unique_id', 'session_id', 'link_id', 'page_id'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;


}
