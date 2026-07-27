<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriberService {
    static public function subscribe($email) {
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            $subscriber->update([
                'status' => 'subscribed',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);

            return $subscriber;
        }

        $subscriber = new NewsletterSubscriber();
        $subscriber['email'] = $email;
        $subscriber['status'] = 'subscribed';
        $subscriber['subscribed_at'] = now();
        $subscriber->save();

        return $subscriber;
    }
}
