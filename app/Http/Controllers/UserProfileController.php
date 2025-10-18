<?php

namespace App\Http\Controllers;

use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    public function user() {
        $user = Auth::user();
        $ownMedicalProflie = $this->getUserOwnMedicalProfile();
        return view('profile.userProfile', [
            'user' => $user,
            'medicalProfile' => $ownMedicalProflie
        ]);
    }

    /**
     * Obtiene el perfil médico del usuario autenticado.
     * @return User $user es e
     */
    public function getUserOwnMedicalProfile(): ?MedicalProfile {
        $user = Auth::user();

        return  $user->medicalProfile;
    }
}
