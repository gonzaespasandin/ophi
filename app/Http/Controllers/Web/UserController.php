<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index(Request $request) {
        $query = User::with('subscription.plan');

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
            'user' => User::with('subscription.plan')->findOrFail($id),
        ]);
    }

    public function edit(int $id) {
        return view('users.edit', [
            'user' => User::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id) {
        if (auth()->user()->id === $id) {
            Session::flash('feedback.message', 'No podés modificar el rol de tu usuario actual');
            Session::flash('feedback.type', 'danger');
            return to_route('admin.users');
        }

        $request->only('role');

        $user = User::findOrFail($id);
        $role = $request->input('role');
        $user->update(['role' => $role]);

        if (DB::getSchemaBuilder()->hasTable('roles') && DB::getSchemaBuilder()->hasTable('role_user')) {
            $roleId = DB::table('roles')->where('name', $role)->value('id');

            if ($roleId) {
                DB::table('role_user')
                    ->where('user_id', $user->id)
                    ->whereIn('role_id', DB::table('roles')->whereIn('name', ['admin', 'user'])->pluck('id'))
                    ->delete();

                DB::table('role_user')->updateOrInsert([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
            }
        }

        Session::flash('feedback.message', 'Rol actualizado correctamente');
        Session::flash('feedback.type', 'success');
        return to_route('admin.users');
    }
}
