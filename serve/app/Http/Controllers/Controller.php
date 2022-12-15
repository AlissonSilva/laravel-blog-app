<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    /**
     * @OA\OpenApi(
     *
     *     @OA\Info(
     *         version="1.0",
     *         title="Laravel Blog App",
     *         description="Blog with back-end laravel and front-end flutter",
     *     ),
     * )
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        $filename = time().'.png';

        \Storage::disk($path)->put($filename, base64_decode($image));

        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }
}
