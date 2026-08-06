<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidEmailChangeTokenException;
use App\Http\Controllers\Controller;
use App\Services\EmailChangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private EmailChangeService $emailChangeService) {}

    public function updateEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'new_email' => 'required|email|max:255',
            'current_password' => 'required|string',
        ], [
            'new_email.required' => 'El email es obligatorio',
            'new_email.email' => 'El email no tiene un formato válido',
            'current_password.required' => 'Necesitamos tu contraseña actual para confirmar el cambio',
        ]);

        $this->emailChangeService->requestChange(
            $request->user(),
            $data['new_email'],
            $data['current_password']
        );

        return response()->json([
            'message' => 'Te enviamos un mail a '.$data['new_email'].' para confirmar el cambio',
        ]);
    }

    public function confirmEmail(string $token): JsonResponse
    {
        try {
            $user = $this->emailChangeService->confirm($token);
        } catch (InvalidEmailChangeTokenException $e) {
            return response()->json(['message' => $e->getMessage()], 410);
        }

        return response()->json([
            'message' => 'Tu email fue actualizado',
            'email' => $user->email,
        ]);
    }
}
