<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $query = User::query();

        if ($request->has('q')) {
            $query->where('name', 'like', "%{$request->get('q')}%");
            $query->orWhere('email', 'like', "%{$request->get('q')}%");
        }

        $users = $query->paginate(3)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'query' => $request->get('q'),
        ]);
    }

    public function show(User $user) {
        return view('users.show', [
            'user' => $user,
        ]);
    }
}
