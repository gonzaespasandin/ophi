<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriberService {
    static public function subscribe($email) {
        $exists = NewsletterSubscriber::where('email', $email)->exists();

        if ($exists) {
            return true;
        }

        $subscriber = new NewsletterSubscriber();
        $subscriber['email'] = $email;
        $subscriber->save();

        return $subscriber;
    }
}
