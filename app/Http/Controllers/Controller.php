<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $ext, $path = '/public')
    {
        $filename = time() . '.' . $ext;
        $image->storeAs('public/' . $path, $filename);
        return URL::to('/') . '/storage/' . $path . '/' . $filename;
    }

    public function uploadImage($request)
    {
        try {

            $response = $this->uploadToImgBB($$request);
            return response()->json([
                'image_url' => $response['data']['url'],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => $response['error']['message'],
            ], 400);
        }
    }
    public function uploadToImgBB($image)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://api.imgbb.com/1/upload', [
                'form_params' => [
                    'image' => base64_encode(file_get_contents($image->getRealPath())),
                    'key' => env('IMAGEBB_API_KEY'),
                ],
            ]);
            $json_decode = json_decode($response->getBody(), true);
            return $json_decode['data']['url'];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            return response()->json([
                'error' => $response['error']['message'],
            ], 400);
        }
    }
}
