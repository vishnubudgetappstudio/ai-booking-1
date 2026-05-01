<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Booking;
use App\Models\Slot;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->message;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Extract name, service, date, time in JSON only like {"name":"","service":"","date":"","time":""}'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ]
        ]);

        // Debug check
        if (!$response->successful()) {
            return response()->json([
                'message' => 'AI error',
                'error' => $response->body()
            ]);
        }

        $text = $response['choices'][0]['message']['content'];

        $data = json_decode($text, true);

        if (!$data) {
            return response()->json([
                'message' => 'Konjam clear ah sollunga bro 🙂'
            ]);
        }

        return $this->handleBooking($data);
    }

    private function handleBooking($data)
    {
        if (empty($data['service']) || empty($data['date'])) {
            return response()->json([
                'message' => 'Service and date venum bro 🙂'
            ]);
        }

        if (empty($data['time'])) {
            $slots = Slot::where('service', $data['service'])
                ->where('date', $data['date'])
                ->where('is_booked', false)
                ->pluck('time');

            return response()->json([
                'message' => 'Available slots',
                'slots' => $slots
            ]);
        }

        $slot = Slot::where('service', $data['service'])
            ->where('date', $data['date'])
            ->where('time', $data['time'])
            ->where('is_booked', false)
            ->first();

        if (!$slot) {
            return response()->json([
                'message' => 'Slot not available ❌'
            ]);
        }

        $slot->is_booked = true;
        $slot->save();

        Booking::create([
            'vendor_id' => $slot->vendor_id,
            'name' => $data['name'] ?? 'Guest',
            'service' => $data['service'],
            'date' => $data['date'],
            'time' => $data['time'],
            'status' => 'confirmed'
        ]);

        return response()->json([
            'message' => 'Booking confirmed 🎉'
        ]);
    }

}
