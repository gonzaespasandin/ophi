<?php

namespace App\Services;

use App\Exceptions\InvalidEmailChangeTokenException;
use App\Models\EmailChangeRequest;
use App\Models\User;
use App\Notifications\EmailChangeRequestedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmailChangeService
{
    private const EXPIRATION_MINUTES = 60;

    public function requestChange(User $user, string $newEmail, string $currentPassword): EmailChangeRequest
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña no es correcta',
            ]);
        }

        if (strcasecmp($newEmail, $user->email) === 0) {
            throw ValidationException::withMessages([
                'new_email' => 'Ese ya es tu email actual',
            ]);
        }

        if (User::where('email', $newEmail)->exists()) {
            throw ValidationException::withMessages([
                'new_email' => 'Ese email ya está registrado',
            ]);
        }

        $plainToken = Str::random(64);

        $request = DB::transaction(function () use ($user, $newEmail, $plainToken) {
            EmailChangeRequest::where('user_id', $user->id)->delete();

            return EmailChangeRequest::create([
                'user_id' => $user->id,
                'new_email' => $newEmail,
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
            ]);
        });

        Notification::route('mail', $newEmail)
            ->notify(new EmailChangeVerificationNotification($plainToken));

        $user->notify(new EmailChangeRequestedNotification($newEmail));

        return $request;
    }

    public function confirm(string $plainToken): User
    {
        $request = EmailChangeRequest::where('token', hash('sha256', $plainToken))->first();

        if (! $request || $request->isExpired()) {
            throw new InvalidEmailChangeTokenException();
        }

        if (User::where('email', $request->new_email)->exists()) {
            throw new InvalidEmailChangeTokenException('Ese email ya está registrado por otra cuenta.');
        }

        return DB::transaction(function () use ($request) {
            $user = $request->user;
            $user->email = $request->new_email;
            $user->save();

            $request->delete();

            return $user;
        });
    }
}
