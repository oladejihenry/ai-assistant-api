<?php

namespace App\Http\Controllers;

use App\Events\TravelResponseUpdated;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class TravelAssistantController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');

        $stream = OpenAI::chat()->createStreamed([
            'model' => 'gpt-4o',
            'temperature' => 0.8,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a travel assistant that uses AI to provide personalized travel itineraries, and suggest local attractions.'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ],
            ],
            'max_tokens' => 1024,
        ]);

        foreach ($stream as $response) {
            $text = $response->choices[0]->delta->content ?? '';



            if (connection_aborted()) {
                break;
            }

            if (!empty($text)) {
                broadcast(new TravelResponseUpdated($message, $text));
            }
        }

        return response()->json(['status' => 'Streaming started']);
    }
}
