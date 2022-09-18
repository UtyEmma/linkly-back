<?php

namespace App\Library;

use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHandler {

    static function upload(string | array $files){
        if(!$files) return false;

        if(is_array($files)){
            $file_array = array_map(function($file){
                return self::handleUpload($file);
            }, $files);

            return json_encode($file_array);
        }

        return self::handleUpload($files);
    }

    static function handleUpload($file){
        if(env('STORAGE') === 'local' ){
            $url = self::saveToStorage($file);
        }else if (env('STORAGE') === 'cloud') {
            $url = cloudinary()->upload($file->getRealPath())->getSecurePath();
        }
        return $url;
    }

    static function saveToStorage($file){
        $ext = $file->getClientOriginalExtension();
        $imageName = Str::random().'.'.$ext;
        $file->move(public_path('/images/storage'), $imageName);
        return asset('/images/storage/'.$imageName);
    }

    static function updateFile($file, $oldFile){
        self::deleteFile($oldFile);
        return self::upload($file);
    }

    static function deleteFiles(array $files){
        foreach ($files as $file) {
            self::deleteFile($file);
        }
    }

    static function deleteFile($file){
        if ($file) {
            $cloudinary_id = self::extractFileId($file);
            cloudinary()->destroy($cloudinary_id);
        }
    }

    private static function extractFileId($file){
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        return basename($file, $ext);
    }


}
