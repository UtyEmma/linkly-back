<?php

namespace App\Library;

use Exception;

use function PHPUnit\Framework\directoryExists;

// defined('BASEPATH') OR exit("No direct script access allowed");

class Base64 {

    public $base64string;
    public $decoded;
    public $encodedImg;
    public $extension;
    public $mimeType;

    function parse($base64string){
        $this->base64string = $base64string;
        $base64Image = explode(";base64,", $base64string);
        $explodeImage = explode("image/", $base64Image[0]);
        $imageType = $explodeImage[1];
        $this->encodedImg = $base64Image[1];
        return $this;
    }

    function check(){
        $decodedString = $this->encodedImg;
        if(!$decodedString) throw new Exception("Valid Base64 string required");
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $decodedString)) throw new Exception("Invalid Base64 String Contents");
        $decoded = base64_decode($decodedString, true);
        if(false === $decoded) throw new Exception("Invalid Base64 string decoded characters");
        $this->decoded = $decoded;
        return $this;
    }
    
    public function file(){
        return $this->decoded;
    }

    public function upload($path, $name = null){
        if(!is_dir($path)) throw new Exception("Invalid Directory");
        $name = $name ?? uniqid().date('Y_m_d');
        $fileName = $name.'.'.$this->extension;
        file_put_contents($path.'/'.$fileName, $this->decoded);
        return asset('/images/storage/'.$fileName);
    }

    public function type(array | string | null $type = null){
        $mime_type = @mime_content_type($this->base64string);
        if($type){
            $allowed_file_types = is_array($type) ? $type : [$type];
            if (!in_array($mime_type, $allowed_file_types)) throw new Exception("The base 64 file string does not match the filetypes");
        }
        $this->extension = explode('/', $mime_type)[1];
        $this->mimeType = $mime_type;
        return $this;
    }
}