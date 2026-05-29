<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function faceEnrollment()
    {
        if (Auth::user()->employee_face_descriptor) {
            return redirect()->route('dashboard')->with('warning', 'Wajah Anda sudah terdaftar. Hubungi HRD jika ingin memperbarui.');
        }
        return view('profile.face_enrollment');
    }

    public function saveFace(Request $request)
    {
        if (Auth::user()->employee_face_descriptor) {
            return redirect()->route('dashboard')->with('warning', 'Wajah Anda sudah terdaftar. Hubungi HRD jika ingin memperbarui.');
        }

        $request->validate([
            'face_descriptor' => 'required|string',
        ]);

        $user = Auth::user();
        $user->employee_face_descriptor = $request->face_descriptor;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Face enrolled successfully! You can now access the system and use attendance.');
    }
}
