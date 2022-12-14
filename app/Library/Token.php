<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Token {
    private static function generateId(){
        $uuid = (string) Str::uuid();
        $trim = explode('-', $uuid);
        $id = $trim[4];
        return $id;
    }

    static function unique($table, $column = 'unique_id'){
        $id =  static::generateId();
        $status = DB::table($table)->where($column, '===', $id)->first() ? false : $id;
        if (!$status) { return static::unique($table, $column); }
        return $status;
    }

    static function random(int $min = 10000, int $max = 999999){
        $random = rand($min, $max);
        return $random;
    }


    static function text(int | array $len = 5, $table = null, $column = null){
        $length = self::getLength($len);
        $str = Str::random($length);
        if($table && $column){
            $status = DB::table($table)->where($column, '===', $str)->first() ?? false;
            if ($status) return static::text($len, $table, $column);
        }

        return $str;
    }

    private static function getLength(int|array $len){
        if(gettype($len) == 'array') return rand($len[0], $len[1]);
        return $len;
    }
}
