<?php

namespace App\Services;

use App\Exceptions\InvalidEmailChangeTokenException;
use App\Models\EmailChangeRequest;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\EmailChangeRequestedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        try {
            Notification::route('mail', $newEmail)
                ->notify(new EmailChangeVerificationNotification($plainToken));
        } catch (\Throwable $e) {
            $request->delete();

            throw $e;
        }

        try {
            $user->notify(new EmailChangeRequestedNotification($newEmail));
        } catch (\Throwable $e) {
            Log::warning('Could not notify the previous address about an email change request.', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
        }

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
            $oldEmail = $user->email;

            $user->email = $request->new_email;
            $user->save();

            $subscriber = NewsletterSubscriber::where('user_id', $user->id)
                ->orWhere('email', $oldEmail)
                ->first();

            if ($subscriber) {
                $colliding = NewsletterSubscriber::where('email', $request->new_email)
                    ->where('id', '!=', $subscriber->id)
                    ->first();

                if ($colliding) {
                    if ($colliding->user_id === null) {
                        $colliding->delete();
                    } else {
                        $subscriber->delete();
                        $subscriber = null;
                    }
                }

                if ($subscriber) {
                    $subscriber->user_id = $user->id;
                    $subscriber->email = $user->email;
                    $subscriber->save();
                }
            }

            $request->delete();

            return $user;
        });
    }
}
