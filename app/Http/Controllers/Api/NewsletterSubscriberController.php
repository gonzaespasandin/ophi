<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsletterSubscriberService;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    public function __construct(private NewsletterSubscriberService $newsletterService)
    {
    }

    public function subscribe(Request $request)
    {
        $this->newsletterService->subscribe($request->input('email'));

        return response()->noContent();
    }
}
