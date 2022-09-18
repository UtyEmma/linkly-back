<?php

namespace App\Library;

use Illuminate\Support\Arr;

class Response {
    private $code;

    function redirectBack($type = null, $message = null){
        return redirect()->back()->with($type, $message);
    }

    static function success($code = null){
        $class = new self;
        $class->code = $code ?? 200;
        return $class;
    }

    static function error($code = null){
        $class = new self;
        $class->code = $code ?? 400;
        return $class;
    }

    function redirect($to, $type = null, $message = null){
        return redirect($to)->with($type, $message);
    }

    function intended($default, $key = null, $value = null){
        return redirect()->intended($default)->with($key, $value);
    }

    function view($blade, $data = []){
        return response()->view($blade, $data);
    }

    function json(...$args){
        $arr = ['message' => "", 'data' => []];
        
        for ($i=0; $i < count($args); $i++) { 
            if(gettype($args[$i]) == 'string') $arr['message'] = $args[$i];
            if(gettype($args[$i]) == 'array') $arr['data'] = $args[$i];
            if(gettype($args[$i]) == 'integer') $this->code = $args[$i];
        }

        return response()->json($arr, $this->code);
    }
}
