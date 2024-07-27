<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class AudioProcessController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'audio' => 'required|file',
        ]);

        if ($request->file('audio')) {

            $filePath = Cloudinary::upload($request->file('audio')->getRealPath(), [
                'resource_type' => 'video'
            ])->getSecurePath();

            return response()->json(['url' => $filePath]);
        }
    }
}
