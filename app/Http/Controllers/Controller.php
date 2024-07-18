<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // public function saveImage($image, $ext, $path = 'public')
    // {
    //     $filename = time() . '.' . $ext;
    //     $image->storeAs($path, $filename);
    //     return URL::to('/') . '/storage/' . $path . '/' . $filename;
    // }
    public function saveImage($image, $ext, $path = '/public')
    {
        $filename = time() . '.' . $ext;
        $image->storeAs('public/' . $path, $filename);
        return URL::to('/') . '/storage/' . $path . '/' . $filename;
    }
}
