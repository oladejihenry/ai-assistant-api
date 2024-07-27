<?php

namespace App\Http\Controllers;

use App\Events\AudioTranscribeEvent;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class AudioTranscribeController extends Controller
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


            $stream = OpenAI::audio()->transcribe([
                'model' => 'whisper-1',
                'file' => fopen($filePath, 'r'),
                'response_format' => 'verbose_json',
                'timestamp_granularities' => ['segment', 'word']
            ]);

            foreach ($stream->segments as $response) {
                $word = $response->text ?? '';

                if (connection_aborted()) {
                    break;
                }

                if (!empty($word)) {
                    broadcast(new AudioTranscribeEvent($word));
                }
            }
        }


        return response()->json([
            'status' => 'Audio transcribe started',
            'audio' => $filePath
        ], 200);
    }
}
