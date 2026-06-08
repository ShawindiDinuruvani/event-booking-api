<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function book(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'seats_booked' => 'required|integer|min:1'
        ]);

        $event = Event::find($request->event_id);

        if ($event->available_seats < $request->seats_booked) {
            return response()->json([
                'message' => 'Not enough seats'
            ], 400);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'event_id' => $request->event_id,
            'seats_booked' => $request->seats_booked
        ]);

        $event->available_seats -= $request->seats_booked;
        $event->save();

        return response()->json([
            'message' => 'Booking successful',
            'booking' => $booking
        ]);
    }
}