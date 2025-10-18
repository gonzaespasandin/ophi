<?php

namespace App\Http\Controllers;

use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalProfileController extends Controller
{
    public function create() 
    {
        $user = auth()->user();
        return view('profile.create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request) {



        $data = $request->only('title', 'id_user');

        MedicalProfile::create($data);

        return to_route('home');
    }
}
