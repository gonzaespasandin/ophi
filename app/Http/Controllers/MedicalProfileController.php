<?php

namespace App\Http\Controllers;

use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalProfileController extends Controller
{
    public function create() 
    {
        $user = Auth::user();
        return view('profile.create', [
            'user' => $user,
        ]);
    }


    /**
     * Inserta en DB un perfil médico con ID de usuario si hay un usuario autenticado. 
     * @param Request $request Información del form
     */
    public function store(Request $request) {

        // Hay usuario autenticado y su id?
        $user_id = Auth::id();

        // Verificación para insertar.
        if($user_id) {
            $data = $request->only('title');
            $data['user_id'] = $user_id;

            MedicalProfile::create($data);
        
            return to_route('home');
        }

        return back();
    }


}
