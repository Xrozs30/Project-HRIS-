<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresenceController extends Controller
{
    // HR View: List of all attendance today
    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $attendance = \App\Models\Presence::whereDate('presence_date', $date)->with('employee')->paginate(10)->withQueryString();
        return view('presence.index', compact('attendance', 'date'));
    }

    // Employee View: Camera Interface
    public function create()
    {
        // Check if already present today
        $today = date('Y-m-d');
        $cek = \App\Models\Presence::where('employee_id', auth()->user()->employee_id)->whereDate('presence_date', $today)->first();
        return view('presence.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'lat' => 'nullable',
            'long' => 'nullable',
        ]);

        $user = auth()->user();
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $lat = $request->lat;
        $long = $request->long;

        // Handle Image
        $image = $request->image;
        $folderPath = "public/presence/";
        $formatName = $user->employee_id . "-" . $date;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        \Illuminate\Support\Facades\Storage::put($file, $image_base64);

        $status = strtotime($time) <= strtotime('09:30:00') ? 'on_time' : 'late';

        \App\Models\Presence::create([
            'employee_id' => $user->employee_id,
            'presence_date' => $date,
            'presence_time_in' => $time,
            'presence_photo_in' => $fileName,
            'presence_lat' => $lat,
            'presence_long' => $long,
            'presence_status' => $status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Thank you, have a great day at work!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'lat' => 'nullable',
            'long' => 'nullable',
        ]);

        $user = auth()->user();
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $presence = \App\Models\Presence::where('employee_id', $user->employee_id)->whereDate('presence_date', $date)->first();
        if ($presence) {
            // Handle Image
            $image = $request->image;
            $folderPath = "public/presence/";
            $formatName = $user->employee_id . "-" . $date . "-out";
            $image_parts = explode(";base64,", $image);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;

            \Illuminate\Support\Facades\Storage::put($file, $image_base64);

            $presence->update([
                'presence_time_out' => $time,
                'presence_photo_out' => $fileName,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Be careful on your way home!');
    }
}