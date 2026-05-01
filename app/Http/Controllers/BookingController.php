<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;
use App\Models\Booking;


class BookingController extends Controller
{
    public function addSlot(Request $request)
    {
        Slot::create([
            'vendor_id' => 1, // temp
            'service' => $request->service,
            'date' => $request->date,
            'time' => $request->time,
        ]);

        return response()->json(['message' => 'Slot added']);
    }

    public function getSlots(Request $request)
    {
        $slots = Slot::where('service', $request->service)
            ->where('date', $request->date)
            ->where('is_booked', false)
            ->pluck('time');

        return response()->json($slots);
    }

    public function book(Request $request)
    {
        $slot = Slot::where('service', $request->service)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('is_booked', false)
            ->first();

        if (!$slot) {
            return response()->json(['message' => 'Slot not available ❌']);
        }

        // lock slot
        $slot->is_booked = true;
        $slot->save();

        // create booking
        $booking = Booking::create([
            'vendor_id' => $slot->vendor_id,
            'name' => $request->name,
            'service' => $request->service,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'message' => 'Booking confirmed 🎉',
            'data' => $booking
        ]);
    }
}
