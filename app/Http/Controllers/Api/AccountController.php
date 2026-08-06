<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidEmailChangeTokenException;
use App\Http\Controllers\Controller;
use App\Models\EmailChangeRequest;
use App\Services\EmailChangeService;
use App\Services\NewsletterSubscriberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        private EmailChangeService $emailChangeService,
        private NewsletterSubscriberService $newsletterService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $pending = EmailChangeRequest::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'email' => $user->email,
            'pending_email' => $pending?->new_email,
            'newsletter_subscribed' => $this->newsletterService->isSubscribed($user),
        ]);
    }

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

    public function confirmEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $user = $this->emailChangeService->confirm($data['token']);
        } catch (InvalidEmailChangeTokenException $e) {
            return response()->json(['message' => $e->getMessage()], 410);
        }

        return response()->json([
            'message' => 'Tu email fue actualizado',
            'email' => $user->email,
        ]);
    }

    public function updateNewsletter(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subscribed' => 'required|boolean',
        ], [
            'subscribed.required' => 'Falta indicar si querés recibir novedades',
        ]);

        $user = $request->user();

        $data['subscribed']
            ? $this->newsletterService->subscribe($user->email, $user)
            : $this->newsletterService->unsubscribe($user->email, $user);

        return response()->json([
            'message' => $data['subscribed']
                ? 'Vas a recibir nuestras novedades'
                : 'Ya no vas a recibir novedades',
        ]);
    }
}
