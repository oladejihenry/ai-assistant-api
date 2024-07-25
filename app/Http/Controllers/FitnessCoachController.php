<?php

namespace App\Http\Controllers;

use App\Events\FitnessCoachUpdated;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class FitnessCoachController extends Controller
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
                    'content' => 'You are a fitness coach AI that provides personalized workout plans, nutrition advice, and motivational tips based on user input about fitness goals, preferences, and progress.'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ],
            ],
        ]);

        foreach ($stream as $response) {
            $text = $response->choices[0]->delta->content ?? '';

            if (connection_aborted()) {
                break;
            }

            if (!empty($text)) {
                broadcast(new FitnessCoachUpdated($message, $text));
            }
        }

        return response()->json(['status' => 'Streaming started']);
    }
}
