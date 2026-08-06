<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberService
{
    public function subscribe(string $email, ?User $user = null): NewsletterSubscriber
    {
        $subscriber = $this->findFor($email, $user) ?? new NewsletterSubscriber();

        $subscriber->email = $email;
        $subscriber->user_id = $user?->id ?? $subscriber->user_id;
        $subscriber->status = 'subscribed';
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return $subscriber;
    }

    public function unsubscribe(string $email, ?User $user = null): ?NewsletterSubscriber
    {
        $subscriber = $this->findFor($email, $user);

        if (! $subscriber) {
            return null;
        }

        $subscriber->status = 'unsubscribed';
        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return $subscriber;
    }

    public function isSubscribed(User $user): bool
    {
        $subscriber = $this->findFor($user->email, $user);

        return $subscriber?->status === 'subscribed';
    }

    private function findFor(string $email, ?User $user): ?NewsletterSubscriber
    {
        if ($user) {
            $byUser = NewsletterSubscriber::where('user_id', $user->id)->first();

            if ($byUser) {
                return $byUser;
            }
        }

        return NewsletterSubscriber::where('email', $email)->first();
    }
}
