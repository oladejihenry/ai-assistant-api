<?php

namespace App\Http\Controllers;

use App\Events\AudioTranscribeEvent;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AudioTranscribeController extends Controller
{
    public function __invoke(Request $request)
    {

        $request->validate([
            'audio' => 'required|string',
        ]);

        $audioPath = $request->audio;

        $stream = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($audioPath, 'r'),
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

        return response()->json([
            'status' => 'Audio transcribe started',
            'audio' => $audioPath
        ], 200);
    }
}
