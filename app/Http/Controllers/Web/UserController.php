<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $query = User::with('subscription');

        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->get('q')}%")
              ->orWhere('email', 'like', "%{$request->get('q')}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'query' => $request->get('q'),
        ]);
    }

    public function show(int $id) {
        return view('users.show', [
            'user' => User::with('subscription')->findOrFail($id),
        ]);
    }

    public function edit(int $id) {
        return view('users.edit', [
            'user' => User::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id) {
  
        $request->only('role');

        $user = User::findOrFail($id);
        $user->update(['role' => $request->input('role')]);

        return to_route('admin.users');
    }
}
