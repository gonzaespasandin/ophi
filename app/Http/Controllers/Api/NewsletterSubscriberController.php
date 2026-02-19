<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsletterSubscriberService;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    function subscribe(Request $request) {
        NewsletterSubscriberService::subscribe($request->input('email'));

        return response()->noContent();
    }
}
